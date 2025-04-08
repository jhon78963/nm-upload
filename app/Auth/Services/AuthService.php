<?php

namespace App\Auth\Services;

use App\Auth\Enums\TokenAbility;
use App\Auth\Exceptions\InvalidTokenException;
use App\Auth\Exceptions\InvalidUserCredentialsException;
use App\Auth\Models\PersonalAccessToken;
use App\Auth\Requests\DeleteTokenRequest;
use App\Auth\Requests\RefreshTokenRequest;
use App\Auth\Requests\UpdateMeRequest;
use App\User\Models\User;
use Carbon\Carbon;
use Hash;

class AuthService
{
    public function validateUser(string $password, User $user): User
    {
        if (!$user || !Hash::check($password, $user->password)) {
            throw new InvalidUserCredentialsException();
        }
        return $user;
    }

    public function updateMe(UpdateMeRequest $request): void {
        $request->user()->fill(
            $request->only(['username', 'email', 'name', 'surname'])
        )->save();
    }

    public function changePassword(User $user, string $newPassword): void {
        $user->password = Hash::make($newPassword);
        $user->save();
    }

    public function createTokens(User $user): array
    {
        $user->tokens()->delete();
        $accessToken = $user->createToken(
            'access_token',
            [TokenAbility::ACCESS_API->value],
            config('sanctum.access_token_expiration')
                ? Carbon::now()->addMinutes(config('sanctum.access_token_expiration'))
                : null
        )->plainTextToken;

        $refreshToken = $user->createToken(
            'refresh_token',
            [TokenAbility::ISSUE_ACCESS_TOKEN->value],
            config('sanctum.refresh_token_expiration')
                ? Carbon::now()->addMinutes(config('sanctum.refresh_token_expiration'))
                : null
        )->plainTextToken;

        return $this->generateTokenResponse($accessToken, $refreshToken);
    }

    public function deleteToken(User $user, $accessToken, $refreshToken)
    {
        $user->tokens()->find($accessToken->id)->delete();
        $user->tokens()->find($refreshToken->id)->delete();
    }

    public function validateTokens(RefreshTokenRequest |  DeleteTokenRequest $request): array
    {
        $refreshToken = PersonalAccessToken::findToken($request->refreshToken);
        $accessToken = PersonalAccessToken::findToken($request->accessToken);
        $user = User::find($refreshToken->tokenable_id);
        if ( !$refreshToken) throw new InvalidTokenException();
        return  [
            'refreshToken' => $refreshToken,
            'accessToken' => $accessToken,
            'user' => $user,
        ];
    }

    public function generateTokenResponse(string $accessToken, string $refreshToken): array
    {
        $response = [
            'token' => $accessToken,
            'refreshToken' => $refreshToken,
        ];

        if ($expirationToken = $this->calculateExpirationInMilliseconds(config('sanctum.access_token_expiration'))) {
            $response['expirationToken'] = $expirationToken;
        }

        if ($expirationRefreshToken = $this->calculateExpirationInMilliseconds(config('sanctum.refresh_token_expiration'))) {
            $response['expirationRefreshToken'] = $expirationRefreshToken;
        }

        return $response;
    }

    private function calculateExpirationInMilliseconds(int|null $expirationInMinutes): int|null
    {
        return $expirationInMinutes != null
            ? Carbon::parse($expirationInMinutes)->diffInMilliseconds(Carbon::now())
            : null;
    }
}

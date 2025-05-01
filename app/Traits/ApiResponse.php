<?php

namespace App\Traits;

use App\Models\User;

trait ApiResponse
{
   protected function authResponse(User $user, $tokenName = 'authToken')
   {
      return response()->json([
         'access_token' => $user->createToken($tokenName)->plainTextToken,
         'token_type' => 'Bearer',
         'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'has_password' => !is_null($user->password),
            'social_providers' => $user->socialAccounts->pluck('provider')
         ]
      ]);
   }
}

<?php

namespace App\Traits;

use App\Enum\VerificationType;
use App\Models\User;
use App\Models\User\Bio;
use App\Models\User\Settings;
use App\Models\Verification;

trait OnboardsNewUser
{
    protected function onboardNewUser(User $user, string $role = 'client')
    {
        $user->assignRole($role);

        Settings::firstOrCreate(['user_id' => $user->id]);
        Bio::firstOrCreate(['user_id' => $user->id]);

        foreach (
            [
                VerificationType::User->value,
                VerificationType::Address->value,
                VerificationType::Company->value,
            ] as $type
        ) {
            Verification::firstOrCreate([
                'user_id' => $user->id,
                'type' => $type,
            ]);
        }
    }
}

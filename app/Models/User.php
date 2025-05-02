<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\User\Settings;
use App\Notifications\Auth\ResetPasswordSms;
use App\Notifications\Auth\VerifyPhone;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'verification_code'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // relations
    public function settings(): HasOne
    {
        return $this->hasOne(Settings::class);
    }

    public function client_projects(): HasMany
    {
        return $this->hasMany(Project::class, 'client_id', 'id');
    }

    public function designer_projects(): HasMany
    {
        return $this->hasMany(Project::class, 'designer_id', 'id');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function hasSocialAccount($provider)
    {
        return $this->socialAccounts()->where('provider', $provider)->exists();
    }

    public function socialLogin($providerUser, $provider)
    {
        if (!$this->email_verified_at) {
            $this->forceFill(['email_verified_at' => now()])->save();
        }

        return $this->socialAccounts()->updateOrCreate(
            ['provider' => $provider, 'provider_user_id' => $providerUser->getId()],
            ['updated_at' => now()]
        );
    }

    // Verification
    public function sendPhoneVerificationNotification()
    {
        $this->notify(new VerifyPhone);
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function sendPasswordResetSmsNotification($token)
    {
        $this->notify(new ResetPasswordSms($token));
    }

    public function hasVerifiedEmail()
    {
        return ! is_null($this->email_verified_at);
    }

    public function hasVerifiedPhone()
    {
        return ! is_null($this->phone_verified_at);
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enum\User\UserType;
use App\Models\User\Bio;
use App\Models\User\Settings;
use App\Notifications\Auth\ResetPasswordSms;
use App\Notifications\Auth\VerifyEmail;
use App\Notifications\Auth\VerifyPhone;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\Permission\Traits\HasRoles;
use Stephenjude\FilamentTwoFactorAuthentication\TwoFactorAuthenticatable;


class User extends Authenticatable implements MustVerifyEmail, FilamentUser, HasPasskeys
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles, TwoFactorAuthenticatable;

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
        'verification_code',
        'type'
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

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }


    // relations
    public function settings(): HasOne
    {
        return $this->hasOne(Settings::class);
    }

    /**
     * Get the user's settings, creating default settings if none exist.
     */
    public function getSettingsAttribute()
    {
        return $this->settings()->firstOrCreate([
            'user_id' => $this->id,
        ], [
            'lang' => 'en',
            'mail_notifications' => true,
            'sms_notifications' => true,
        ]);
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

    public function bio(): HasOne
    {
        return $this->hasOne(Bio::class);
    }

    public function rates()
    {
        $type = $this->type === UserType::Client ? 'client_id' : 'designer_id';
        $rate = $this->hasMany(Rate::class, $type, 'id')->where('type', $this->type);
        $sum = $rate->sum('rate');
        $count = $rate->count();
        if ($count === 0) {
            return 0;
        }
        return $sum / $count;
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get the tickets associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function attachment()
    {
        return $this->hasOne(Attachment::class);
    }

    public function hasOpenTicket(User $user): bool
    {
        if ($this->hasRole('support') || $this->hasRole('admin')) {
            return $user->tickets()
                ->where('assigned_to', $this->id)
                ->where('status', 'open')
                ->exists();
        }
        return false;
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

    public function getAvatarAttribute(): string
    {
        $attachment = Attachment::where('user_id', $this->id)->first();
        return $attachment->url ?? config('app.default_avatar') . urlencode($this->name);
    }

    public function routeNotificationForTwilio()
    {
        return $this->phone;
    }
}

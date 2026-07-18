<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'stripe_customer_id',
        'stripe_subscription_id',
        'subscription_status',
        'plan',
        'current_period_ends_at',
        'trial_ends_at',
        'favorites',
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
            'password' => 'hashed',
            'current_period_ends_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'favorites' => 'array',
        ];
    }

    /**
     * The tool hrefs this user has favourited.
     *
     * @return array<int, string>
     */
    public function favoriteTools(): array
    {
        return array_values($this->favorites ?? []);
    }

    /**
     * Toggle a tool href in the user's favourites and persist. Returns true if
     * the tool is favourited after the toggle, false if it was removed.
     */
    public function toggleFavorite(string $href): bool
    {
        $favorites = $this->favorites ?? [];
        $index = array_search($href, $favorites, true);

        if ($index === false) {
            $favorites[] = $href;
            $favorited = true;
        } else {
            unset($favorites[$index]);
            $favorited = false;
        }

        $this->favorites = array_values($favorites);
        $this->save();

        return $favorited;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * An active paid (or complimentary) subscription that grants unlimited access.
     */
    public function hasActiveSubscription(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        // Complimentary access granted by an admin.
        if ($this->subscription_status === 'comp') {
            return $this->current_period_ends_at === null || $this->current_period_ends_at->isFuture();
        }

        if (! in_array($this->subscription_status, ['active', 'trialing'], true)) {
            return false;
        }

        return $this->current_period_ends_at === null || $this->current_period_ends_at->isFuture();
    }

    public function onTrial(): bool
    {
        return $this->subscription_status === 'trialing'
            || ($this->trial_ends_at !== null && $this->trial_ends_at->isFuture());
    }

    public function planLabel(): string
    {
        if ($this->isAdmin()) {
            return 'Admin';
        }
        if ($this->subscription_status === 'comp') {
            return 'Complimentary';
        }
        if ($this->hasActiveSubscription()) {
            return config("plans.plans.{$this->plan}.name", 'Pro');
        }

        return 'Free';
    }
}

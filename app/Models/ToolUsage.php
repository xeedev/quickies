<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ToolUsage extends Model
{
    protected $fillable = [
        'user_id', 'ip', 'fingerprint', 'tool_slug', 'used_on', 'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'used_on' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope usage rows to a single visitor identity. Logged-in visitors are keyed
     * by user id; anonymous visitors are keyed by IP address so the daily limit
     * survives switching browsers on the same device/network within a day.
     *
     * @param  array{user_id?:int, ip?:string}  $identity
     */
    public function scopeForIdentity($query, array $identity)
    {
        if (! empty($identity['user_id'])) {
            return $query->where('user_id', $identity['user_id']);
        }

        return $query->whereNull('user_id')->where('ip', $identity['ip'] ?? '0.0.0.0');
    }

    /**
     * @param  array{user_id?:int, ip?:string}  $identity
     */
    public static function record(array $identity, string $slug, Request $request): void
    {
        static::create([
            'user_id' => $identity['user_id'] ?? null,
            'ip' => $identity['ip'] ?? $request->ip(),
            'fingerprint' => substr(hash('sha256', $request->userAgent().'|'.$request->header('accept-language')), 0, 32),
            'tool_slug' => $slug,
            'used_on' => now()->toDateString(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);
    }
}

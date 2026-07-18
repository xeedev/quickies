<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('email');
            $table->string('stripe_customer_id')->nullable()->index()->after('password');
            $table->string('stripe_subscription_id')->nullable()->index()->after('stripe_customer_id');
            $table->string('subscription_status')->nullable()->after('stripe_subscription_id');
            $table->string('plan')->nullable()->after('subscription_status');
            $table->timestamp('current_period_ends_at')->nullable()->after('plan');
            $table->timestamp('trial_ends_at')->nullable()->after('current_period_ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'stripe_customer_id', 'stripe_subscription_id',
                'subscription_status', 'plan', 'current_period_ends_at', 'trial_ends_at',
            ]);
        });
    }
};

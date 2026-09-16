<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email');
            $table->string('phone')->nullable()->after('avatar');
            $table->text('bio')->nullable()->after('phone');
            $table->string('city')->nullable()->after('bio');
            $table->string('country')->nullable()->after('city');
            $table->string('website')->nullable()->after('country');
            $table->string('github_profile')->nullable()->after('website');
            $table->string('twitter_profile')->nullable()->after('github_profile');
            $table->string('timezone')->nullable()->default('UTC')->after('twitter_profile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar',
                'phone',
                'bio',
                'city',
                'country',
                'website',
                'github_profile',
                'twitter_profile',
                'timezone',
            ]);
        });
    }
};

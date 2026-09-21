<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a google_id column to the users table.
     *
     * This stores the Google account ID returned by Google OAuth.
     * It is nullable so existing users are unaffected, and unique
     * so that no two LandSure accounts can be linked to the same
     * Google account.
     *
     * This is intentionally separate from the email column.
     * Matching a returning Google user is done by google_id, not
     * by email, to avoid account-takeover risks if an email is
     * reassigned or unverified.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')
                ->nullable()
                ->unique()
                ->after('password');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['google_id']);
            $table->dropColumn('google_id');
        });
    }
};
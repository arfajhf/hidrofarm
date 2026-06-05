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
            $table->timestamp('api_token_expires_at')->nullable()->after('api_token_hash');
            $table->string('one_tap_token_hash')->nullable()->after('api_token_expires_at');
            $table->timestamp('one_tap_expires_at')->nullable()->after('one_tap_token_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['api_token_expires_at', 'one_tap_token_hash', 'one_tap_expires_at']);
        });
    }
};

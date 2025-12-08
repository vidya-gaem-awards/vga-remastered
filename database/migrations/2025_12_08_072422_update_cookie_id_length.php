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
        Schema::table('access', function (Blueprint $table) {
            $table->string('cookie_id', 511)->change();
        });

        Schema::table('votes', function (Blueprint $table) {
            $table->string('cookie_id', 511)->change();
        });

        Schema::table('voting_code_logs', function (Blueprint $table) {
            $table->string('cookie_id', 511)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('access', function (Blueprint $table) {
            $table->string('cookie_id')->change();
        });

        Schema::table('votes', function (Blueprint $table) {
            $table->string('cookie_id')->change();
        });

        Schema::table('voting_code_logs', function (Blueprint $table) {
            $table->string('cookie_id')->change();
        });
    }
};

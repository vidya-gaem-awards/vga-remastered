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
        Schema::table('user_nomination_groups', function (Blueprint $table) {
            $table->boolean('ignored')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_nomination_groups', function (Blueprint $table) {
            $table->boolean('ignored')->default(null)->change();
        });
    }
};

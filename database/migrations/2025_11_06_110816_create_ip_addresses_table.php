<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ip_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('ip');
            $table->boolean('whitelisted')->nullable();
            $table->integer('abuse_score');
            $table->string('country_code')->nullable();
            $table->string('domain')->nullable();
            $table->string('usage_type')->nullable();
            $table->string('isp')->nullable();
            $table->integer('report_count');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ip_addresses');
    }
};

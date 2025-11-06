<?php

use App\Models\Award;
use App\Models\File;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nominees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subtitle');
            $table->string('slug');
            $table->text('flavor_text');
            $table->foreignIdFor(Award::class)->constrained();
            $table->foreignIdFor(File::class)->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nominees');
    }
};

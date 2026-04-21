<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('android_releases', function (Blueprint $table): void {
            $table->id();
            $table->string('version');
            $table->unsignedInteger('build_number');
            $table->string('download_url');
            $table->string('checksum')->nullable();
            $table->string('channel')->default('stable');
            $table->text('release_notes')->nullable();
            $table->boolean('force_update')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['channel', 'build_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('android_releases');
    }
};

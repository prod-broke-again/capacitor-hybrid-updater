<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('web_bundles', function (Blueprint $table): void {
            $table->id();
            $table->string('version');
            $table->string('zip_url');
            $table->string('checksum')->nullable();
            $table->string('channel')->default('stable');
            $table->string('min_native_version')->default('0.0.0');
            $table->unsignedInteger('min_native_build')->default(0);
            $table->boolean('force_reload')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_bundles');
    }
};

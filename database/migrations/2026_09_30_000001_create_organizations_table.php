<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('primary_color', 7)->default('#003189');
            $table->string('secondary_color', 7)->default('#0047c8');
            $table->string('text_color', 7)->default('#ffffff');
            $table->unsignedBigInteger('logo_media_id')->nullable();
            $table->unsignedBigInteger('secondary_logo_media_id')->nullable();
            $table->string('website')->nullable();
            $table->string('address')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};

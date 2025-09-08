<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_key', function (Blueprint $table) {
            $table->uuid('id')->nullable(false)->primary(true);
            $table->dateTime('created_at')->nullable(false);
            $table->dateTime('valid_till')->nullable(true)->default(null)->index(true);
            $table->dateTime('deleted_at')->nullable(true)->default(null)->index(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_key');
    }
};

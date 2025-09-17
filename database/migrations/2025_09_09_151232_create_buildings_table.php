<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');

        Schema::create('buildings', function (Blueprint $table) {
            $table->uuid('id')->nullable(false)->primary(true);
            $table->string('address')->nullable(true);
            $table->geometry('coordinates', 'Point')->nullable(false)->spatialIndex();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};

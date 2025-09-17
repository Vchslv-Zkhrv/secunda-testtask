<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_activities', function (Blueprint $table) {
            $table->uuid('id')->nullable(false)->primary(true);
            $table->string('name')->nullable(false)->index(true); // не уникальное, т.к. названия подкатегорий могут дубоироваться
        });

        Schema::create('business_activities_parents', function (Blueprint $table) {
            $table->foreignUuid('child_id')->references('id')->on('business_activities')->cascadeOnDelete();
            $table->foreignUuid('parent_id')->references('id')->on('business_activities')->cascadeOnDelete();
            $table->boolean('is_direct')->nullable(false);

            $table->unique(['child_id', 'parent_id']);
            $table->primary(['child_id', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_activities');
        Schema::dropIfExists('business_activities_parents');
    }
};

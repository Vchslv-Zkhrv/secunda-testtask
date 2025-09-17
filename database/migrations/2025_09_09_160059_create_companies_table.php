<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->nullable(false)->primary(true);
            $table->string('name', 255)->nullable(false)->unique(true)->index(true);
            $table->foreignUuid('building_id')->references('id')->on('buildings')->cascadeOnDelete();
            $table->dateTime('created_at')->nullable(false)->useCurrent();
            $table->dateTime('updated_at')->nullable(false)->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('company_activity', function (Blueprint $table) {
            $table->foreignUuid('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreignUuid('activity_id')->references('id')->on('business_activities')->cascadeOnDelete();

            $table->primary(['company_id', 'activity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
        Schema::dropIfExists('company_activity');
    }
};

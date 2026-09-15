<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_analyses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parcel_id')
                ->constrained('parcels')
                ->cascadeOnDelete();

            $table->string('overall_status')->default('unknown');

            $table->decimal('overall_score', 5, 2)->nullable();

            $table->string('flood_status')->default('unknown');
            $table->decimal('flood_score', 5, 2)->nullable();

            $table->string('buffer_status')->default('unknown');
            $table->decimal('buffer_score', 5, 2)->nullable();

            $table->string('planning_status')->default('unknown');
            $table->decimal('planning_score', 5, 2)->nullable();

            $table->string('land_status')->default('unknown');
            $table->decimal('land_score', 5, 2)->nullable();

            $table->string('boundary_status')->default('unknown');
            $table->decimal('boundary_score', 5, 2)->nullable();

            $table->json('findings')->nullable();
            $table->json('sources')->nullable();

            $table->timestamp('analyzed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_analyses');
    }
};
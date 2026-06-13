<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('foreign_national_id')
                ->constrained('foreign_nationals')
                ->cascadeOnDelete();

            $table->foreignId('annulled_by_id')
                ->nullable()
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->foreignId('enrollment_id')
                ->index()
                ->constrained('enrollments')
                ->cascadeOnDelete();

            $table->foreignId('exam_id')
                ->index()
                ->constrained('exams');

            $table->foreignId('center_id')
                ->constrained('centers')
                ->cascadeOnDelete();

            $table->string('annulled_reason')->nullable()->default(null);

            $table->unsignedTinyInteger('total_mark')->default(0);
            $table->boolean('is_passed')->nullable()->default(null);

            $table->dateTime('annulled_at')->nullable()->default(null)->index();
            $table->dateTime('last_activity_at')->nullable()->default(null);
            $table->dateTime('started_at')->nullable()->default(null)->index();
            $table->dateTime('finished_at')->nullable()->default(null)->index();
            $table->dateTime('checked_at')->nullable()->default(null)->index();
            $table->dateTime('expired_at')->nullable()->default(null)->index();

            $table->dateTime('speaking_started_at')->nullable()->default(null);
            $table->dateTime('speaking_finished_at')->nullable()->default(null);

            $table->timestamps();
            $table->unsignedTinyInteger('solved')->default(0);

            $table->unique(['enrollment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempts');
    }
};

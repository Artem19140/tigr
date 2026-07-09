<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attempt_answers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('exam_id')
                ->index()
                ->constrained('exams')
                ->cascadeOnDelete();

            $table->foreignId('task_variant_id')
                ->index()
                ->constrained('task_variants')
                ->cascadeOnDelete();

            $table->foreignId('attempt_id')
                ->index()
                ->constrained('attempts')
                ->cascadeOnDelete();

            $table->foreignId('answer_id')
                ->index()
                ->nullable()
                ->default(null)
                ->constrained('answers')
                ->cascadeOnDelete();

            $table->datetime('audio_played_at')->nullable()->default(null);

            $table->foreignId('checked_by_id')
                ->nullable()
                ->default(null)
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->datetime('checked_at')->nullable()->default(null);
            $table->unsignedTinyInteger('mark')->nullable()->default(null);
            $table->jsonb('answer')->nullable()->default(null);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foreign_national_answers');
    }
};

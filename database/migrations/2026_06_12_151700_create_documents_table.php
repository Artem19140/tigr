<?php

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            $table->morphs('documentable');

            $table->string('path');
            $table->string('original_name');
            $table->foreignIdFor(Employee::class, 'creator_id');

            $table->string('document_type');
            $table->string('mime_type');
            $table->bigInteger('size_bytes');
            $table->dateTime('deleted_at')->nullable()->default(null);
            $table->jsonb('context')->nullable()->default(null);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};

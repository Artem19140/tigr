<?php

use App\Models\Center;
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

            $table->integer('documentable_id');
            $table->string('documentable_type');

            $table->string('path');
            $table->string('original_name');
            $table->foreignIdFor(Employee::class, 'creator_id');
            $table->foreignIdFor(Center::class, 'center_id');
            $table->string('document_type');
            $table->string('mime_type');
            $table->string('size_kb');
            $table->dateTime('deleted_at')->nullable()->default(null);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};

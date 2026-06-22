<?php

use App\Models\Center;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            
            $table->string('status');
            $table->unsignedSmallInteger('total_chunks');
            $table->uuid()->unique('uuid');
            $table->unsignedSmallInteger('uploaded_chunks');
            $table->foreignIdFor(Center::class, 'center_id');
            $table->string('mime_type');
            $table->string('original_name');
            $table->string('path')->nullable()->default(null);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};

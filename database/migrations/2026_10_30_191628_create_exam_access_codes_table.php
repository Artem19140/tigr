<?php

use App\Models\Employee;
use App\Models\Enrollment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exam_access_codes', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique()->nullable()->index();
            $table->string('code_snapshot');
            $table->foreignIdFor(Enrollment::class)->index();
            $table->string('type');

            $table->string('recovery_reason')
                ->nullable()
                ->default(null);
            $table->foreignIdFor(Employee::class, 'generated_by')
                ->nullable()
                ->default(null);
            $table->timestamp('expires_at');
            $table->timestamp('used_at')
                ->nullable()
                ->default(null);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_access_codes');
    }
};

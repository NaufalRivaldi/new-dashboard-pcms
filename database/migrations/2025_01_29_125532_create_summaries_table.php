<?php

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
        Schema::create('summaries', function (Blueprint $table) {
            $table->id();

            $table->string('month')->index();
            $table->string('year')->index();
            $table->double('registration_fee')->default(0);
            $table->double('course_fee')->default(0);
            $table->double('total_fee')->default(0);
            $table->double('royalty')->default(0);
            $table->integer('active_student')->default(0);
            $table->integer('new_student')->default(0);
            $table->integer('inactive_student')->default(0);
            $table->integer('leave_student')->default(0);
            $table->boolean('status')->default(false);
            $table->foreignId('branch_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('approver_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('summaries');
    }
};

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
        Schema::create('counseling_referrals', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('referrer_id');

            // Referral Context
            $table->string('case_reference', 100)->nullable()
                  ->comment('Link to tracking number: LOST-001, VIO-123, INC-045');

            // Urgency Level
            $table->string('support_urgency', 50)
                  ->default('Standard')
                  ->comment('Standard | Priority | Immediate');

            // Workflow Status
            $table->string('administrative_status', 100)
                  ->default('Draft')
                  ->comment('Draft | Forwarded to Guidance | Scheduled | Resolved');

            // Referral Content
            $table->text('administrative_observations')->nullable()
                  ->comment('Context from registry findings, observations, details for guidance office');

            // Scheduling & Follow-up
            $table->date('scheduled_date')->nullable()
                  ->comment('When the counseling session is scheduled');

            $table->text('guidance_notes')->nullable()
                  ->comment('Response/follow-up notes from guidance office');

            // Timestamps & Soft Deletes
            $table->timestamps();
            $table->softDeletes();

            // Foreign Key Constraints
            $table->foreign('student_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('referrer_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict');

            // Indices for common queries
            $table->index('student_id');
            $table->index('administrative_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counseling_referrals');
    }
};

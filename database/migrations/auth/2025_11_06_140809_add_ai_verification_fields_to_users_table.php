<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add AI verification fields to track:
     * - Image hashes (prevent duplicate ID uploads)
     * - Face matching scores
     * - AI confidence levels
     * - Manual review status
     */
    public function up(): void
    {
        Schema::connection('auth_db')->table('users', function (Blueprint $table) {
            // Perceptual hashes for duplicate detection
            $table->string('id_front_hash', 64)->nullable()->after('valid_id_front_image');
            $table->string('id_back_hash', 64)->nullable()->after('valid_id_back_image');
            $table->string('selfie_hash', 64)->nullable()->after('selfie_with_id_image');
            
            // AI verification scores (0-100)
            $table->decimal('face_match_score', 5, 2)->nullable()->after('selfie_hash')->comment('Face similarity: ID vs Selfie');
            $table->decimal('id_authenticity_score', 5, 2)->nullable()->comment('ID authenticity from Teachable Machine');
            $table->decimal('liveness_score', 5, 2)->nullable()->comment('Selfie liveness detection');
            
            // AI verification results
            $table->json('ai_verification_data')->nullable()->comment('Full AI analysis results');
            $table->enum('ai_verification_status', ['pending', 'passed', 'failed', 'manual_review'])->default('pending');
            $table->text('ai_verification_notes')->nullable()->comment('AI rejection reasons or notes');
            
            // Manual review (for low confidence or flagged cases)
            $table->unsignedBigInteger('reviewed_by')->nullable()->comment('Admin user ID who reviewed');
            $table->enum('manual_review_status', ['pending', 'approved', 'rejected'])->nullable();
            $table->text('manual_review_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            
            // Duplicate detection flags
            $table->boolean('is_duplicate_id_detected')->default(false);
            $table->unsignedBigInteger('duplicate_of_user_id')->nullable()->comment('Original user ID if duplicate detected');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('auth_db')->table('users', function (Blueprint $table) {
            $table->dropColumn([
                'id_front_hash',
                'id_back_hash',
                'selfie_hash',
                'face_match_score',
                'id_authenticity_score',
                'liveness_score',
                'ai_verification_data',
                'ai_verification_status',
                'ai_verification_notes',
                'reviewed_by',
                'manual_review_status',
                'manual_review_notes',
                'reviewed_at',
                'is_duplicate_id_detected',
                'duplicate_of_user_id',
            ]);
        });
    }
};

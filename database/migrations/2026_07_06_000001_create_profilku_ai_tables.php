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
        // 1. Tabel Domain Knowledge Base
        Schema::create('knowledge_base_domains', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->enum('category', ['personality', 'interest', 'problem', 'academic', 'career']);
            $table->text('description');
            $table->json('indicators')->nullable(); // List of string indicators
            $table->json('keywords')->nullable(); // List of lowercase keywords
            $table->json('synonyms')->nullable(); // Synonyms for keywords
            $table->json('example_behaviors')->nullable(); // Real-life behaviors
            $table->json('exploration_questions')->nullable(); // Questions AI can ask
            $table->json('follow_up_questions')->nullable(); // Follow up questions
            $table->json('recommendations')->nullable(); // Array: student, bk, wali, parent
            $table->decimal('evidence_weight', 3, 2)->default(1.00);
            $table->timestamps();
        });

        // 2. Tabel AI Providers
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. gemini, openrouter
            $table->string('api_key')->nullable();
            $table->string('model');
            $table->float('temperature')->default(0.7);
            $table->float('top_p')->default(0.9);
            $table->integer('max_tokens')->default(1000);
            $table->text('system_prompt');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // 3. Tabel Aturan Alur (Rules Engine)
        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category'); // Conversation, Confidence, Domain Coverage, Evidence, Memory, Safety, Career, Report, Session
            $table->string('priority')->default('Medium'); // Critical, High, Medium, Low
            $table->string('trigger_condition'); // e.g. confidence_score < 70
            $table->string('action'); // e.g. deepen_exploration
            $table->text('description')->nullable();
            $table->text('parameters')->nullable(); // optional parameters
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Tabel Percakapan (Conversations)
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['active', 'paused', 'completed'])->default('active');
            $table->integer('current_stage')->default(1); // 1-12
            $table->timestamps();
        });

        // 5. Tabel Pesan Percakapan (Conversation Messages)
        Schema::create('conversation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
            $table->enum('sender', ['student', 'ai']);
            $table->text('message_text');
            $table->timestamp('created_at')->useCurrent();
        });

        // 6. Tabel Memori Siswa (Student Memories)
        Schema::create('student_memories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->string('key'); // e.g. hobby, friend_name, career_goal
            $table->text('value');
            $table->float('confidence')->default(1.0);
            $table->unsignedBigInteger('source_message_id')->nullable(); // reference without constraint to avoid drop order issues
            $table->timestamps();
        });

        // 7. Tabel Bukti Profiling (Evidence)
        Schema::create('evidence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('domain_id')->constrained('knowledge_base_domains')->onDelete('cascade');
            $table->string('indicator');
            $table->text('excerpt'); // direct quote
            $table->float('weight')->default(0.5);
            $table->text('reasoning');
            $table->unsignedBigInteger('source_message_id')->nullable();
            $table->timestamps();
        });

        // 8. Tabel Skor Keyakinan Domain (Confidence Scores)
        Schema::create('confidence_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('domain_id')->constrained('knowledge_base_domains')->onDelete('cascade');
            $table->integer('score')->default(0); // 0 - 100
            $table->timestamps();
            
            $table->unique(['student_id', 'domain_id']);
        });

        // 9. Tabel Catatan Guru (Teacher Notes)
        Schema::create('teacher_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->text('note_text');
            $table->timestamps();
        });

        // 10. Tabel Laporan Akhir (Reports)
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->text('executive_summary')->nullable();
            $table->text('personality_analysis')->nullable();
            $table->text('strengths')->nullable();
            $table->text('development_areas')->nullable();
            $table->text('interests')->nullable();
            $table->text('talents')->nullable();
            $table->text('problems')->nullable();
            $table->text('motivation')->nullable();
            $table->text('career_goals')->nullable();
            $table->json('confidence_scores_json')->nullable();
            $table->json('evidence_json')->nullable();
            $table->text('student_recommendations')->nullable();
            $table->text('bk_recommendations')->nullable();
            $table->text('wali_recommendations')->nullable();
            $table->text('parent_recommendations')->nullable();
            $table->text('follow_up_plan')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('teacher_notes');
        Schema::dropIfExists('confidence_scores');
        Schema::dropIfExists('evidence');
        Schema::dropIfExists('student_memories');
        Schema::dropIfExists('conversation_messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('rules');
        Schema::dropIfExists('ai_providers');
        Schema::dropIfExists('knowledge_base_domains');
    }
};

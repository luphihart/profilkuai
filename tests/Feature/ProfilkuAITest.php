<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\KnowledgeBaseDomain;
use App\Models\ConfidenceScore;
use App\Models\Evidence;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Services\ConfidenceEngine;
use App\Services\ReflectionEngine;
use App\Services\RuleEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilkuAITest extends TestCase
{
    use RefreshDatabase;

    protected $student;
    protected $domain;
    protected $conversation;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup dasar sekolah
        $major = Major::create(['name' => 'Rekayasa Perangkat Lunak', 'code' => 'RPL']);
        $class = SchoolClass::create([
            'name' => 'XII RPL 1',
            'major_id' => $major->id
        ]);

        // 2. Setup user student
        $this->student = User::create([
            'name' => 'Rian Hidayat',
            'email' => 'rian@profilku.ai',
            'password' => bcrypt('password'),
            'role' => 'student',
            'class_id' => $class->id
        ]);

        // 3. Setup domain KB
        $this->domain = KnowledgeBaseDomain::create([
            'name' => 'Kepercayaan Diri',
            'category' => 'personality',
            'description' => 'Tingkat keyakinan diri.',
            'indicators' => ['Yakin tampil'],
            'keywords' => ['pede', 'minder'],
            'evidence_weight' => 1.0
        ]);

        // 4. Setup Percakapan aktif
        $this->conversation = Conversation::create([
            'student_id' => $this->student->id,
            'status' => 'active',
            'current_stage' => 1
        ]);
    }

    /**
     * Test User Model Roles and Relations
     */
    public function test_user_roles_and_relations()
    {
        $this->assertTrue($this->student->isStudent());
        $this->assertFalse($this->student->isAdmin());
        $this->assertEquals('XII RPL 1', $this->student->schoolClass->name);
    }

    /**
     * Test Confidence Engine Calculations
     */
    public function test_confidence_engine_calculation()
    {
        // Hubungkan pesan percakapan
        $message = ConversationMessage::create([
            'conversation_id' => $this->conversation->id,
            'sender' => 'student',
            'message_text' => 'Aku minder saat presentasi depan kelas.'
        ]);

        // Buat mock evidence untuk mendongkrak Confidence Score
        Evidence::create([
            'student_id' => $this->student->id,
            'domain_id' => $this->domain->id,
            'indicator' => 'Yakin tampil',
            'excerpt' => 'minder saat presentasi',
            'weight' => 0.8,
            'reasoning' => 'Terdeteksi minder.',
            'source_message_id' => $message->id
        ]);

        $confidenceEngine = new ConfidenceEngine();
        $updatedScores = $confidenceEngine->recalculateScores($this->student->id);

        // Target weight = 1.5 * 1.0 = 1.5. Score = min(100, (0.8 / 1.5) * 100) = 53%
        $this->assertDatabaseHas('confidence_scores', [
            'student_id' => $this->student->id,
            'domain_id' => $this->domain->id,
            'score' => 53
        ]);

        $this->assertEquals(53, $updatedScores[$this->domain->id]);
    }

    /**
     * Test Stage transition in Reflection Engine
     */
    public function test_reflection_engine_stage_transition()
    {
        $reflectionEngine = new ReflectionEngine();

        // Di awal sesi (Stage 1), jika belum ada pesan, belum bergeser ke Stage 2
        $result = $reflectionEngine->evaluateProgress($this->conversation);
        $this->assertEquals(1, $result['current_stage']);

        // Tambah pesan agar melebih ambang batas
        ConversationMessage::create([
            'conversation_id' => $this->conversation->id,
            'sender' => 'student',
            'message_text' => 'Halo mentor AI!'
        ]);
        ConversationMessage::create([
            'conversation_id' => $this->conversation->id,
            'sender' => 'ai',
            'message_text' => 'Halo! Siapa namamu?'
        ]);

        $this->conversation->touch(); // trigger updated_at untuk hitungan pesan
        $result = $reflectionEngine->evaluateProgress($this->conversation);
        
        // Seharusnya bergeser ke Tahap 2 karena jumlah pesan telah terpenuhi
        $this->assertEquals(2, $result['current_stage']);
    }

    /**
     * Test Rule Evaluation in Rule Engine
     */
    public function test_rule_engine_short_answer_check()
    {
        $ruleEngine = new RuleEngine();

        // 1. Cek jika jawaban pendek
        $instructions = $ruleEngine->evaluateRules('Ok', $this->conversation);
        $this->assertTrue(in_array("ATURAN ALUR: Jawaban siswa sangat singkat. Jangan langsung melompat ke topik baru. Tanggapi dengan mengajak siswa menjabarkan pemikirannya secara lebih detail atau berikan contoh sederhana untuk memicu mereka bercerita.", $instructions));

        // 2. Cek jika jawaban panjang (tidak kena short answer rule)
        $longAnswer = "Saya sangat tertarik dengan bidang coding karena saya suka memecahkan masalah logika matematika sejak SMP.";
        $instructionsLong = $ruleEngine->evaluateRules($longAnswer, $this->conversation);
        $this->assertFalse(in_array("ATURAN ALUR: Jawaban siswa sangat singkat. Jangan langsung melompat ke topik baru. Tanggapi dengan mengajak siswa menjabarkan pemikirannya secara lebih detail atau berikan contoh sederhana untuk memicu mereka bercerita.", $instructionsLong));
    }

    /**
     * Test AI recommendation generator endpoint
     */
    public function test_ai_recommendation_generation_endpoint()
    {
        $counselor = User::factory()->create(['role' => 'guru_bk']);
        
        // Mock AIService
        $this->mock(\App\Services\AIService::class, function ($mock) {
            $mock->shouldReceive('generateResponse')
                ->once()
                ->andReturn('Ini draf rekomendasi bimbingan AI untuk Rian.');
        });

        // Pastikan ada provider aktif
        \App\Models\AIProvider::create([
            'name' => 'Gemini',
            'model' => 'gemini-1.5-flash',
            'api_key' => 'fake_api_key',
            'system_prompt' => 'Kamu adalah asisten BK.',
            'is_active' => true
        ]);

        $response = $this->actingAs($counselor)
            ->post(route('bk.student.recommendation.generate', $this->student->id));

        $response->assertStatus(200);
        $response->assertJson([
            'recommendation' => 'Ini draf rekomendasi bimbingan AI untuk Rian.'
        ]);
    }

    /**
     * Test downloading Excel import template
     */
    public function test_download_import_template()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('admin.users.import-template'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=template_import_pengguna.xlsx');
        $this->assertNotEmpty($response->streamedContent());
    }

    /**
     * Test importing users via Excel upload
     */
    public function test_import_users_via_excel()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Buat file Excel nyata menggunakan PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Nama Lengkap');
        $sheet->setCellValue('B1', 'Email');
        $sheet->setCellValue('C1', 'Password');
        $sheet->setCellValue('D1', 'Role');
        $sheet->setCellValue('E1', 'Kelas');

        $sheet->setCellValue('A2', 'Test Student Excel');
        $sheet->setCellValue('B2', 'testexcel.student@sekolah.sch.id');
        $sheet->setCellValue('C2', 'password123');
        $sheet->setCellValue('D2', 'murid'); // Test role synonym (murid -> student)
        $sheet->setCellValue('E2', $this->student->schoolClass->name);

        $sheet->setCellValue('A3', 'Test BK Excel');
        $sheet->setCellValue('B3', 'testexcel.bk@sekolah.sch.id');
        $sheet->setCellValue('C3', 'password123');
        $sheet->setCellValue('D3', 'guru_bk');
        $sheet->setCellValue('E3', '');

        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($tempFile);

        $uploadedFile = new \Illuminate\Http\UploadedFile(
            $tempFile,
            'template_import_pengguna.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->actingAs($admin)
            ->post(route('admin.users.import'), [
                'import_file' => $uploadedFile
            ]);

        $response->assertStatus(302); // Redirect back
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('users', [
            'email' => 'testexcel.student@sekolah.sch.id',
            'role' => 'student',
            'class_id' => $this->student->class_id
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'testexcel.bk@sekolah.sch.id',
            'role' => 'guru_bk'
        ]);

        @unlink($tempFile);
    }

    /**
     * Test editing a user as Admin
     */
    public function test_admin_can_edit_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $studentToEdit = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($admin)
            ->post(route('admin.users.update', $studentToEdit->id), [
                'name' => 'Nama Baru Student',
                'email' => 'studentbaru@sekolah.sch.id',
                'role' => 'student',
                'class_id' => $this->student->class_id
            ]);

        $response->assertStatus(302); // Redirect back
        $this->assertDatabaseHas('users', [
            'id' => $studentToEdit->id,
            'name' => 'Nama Baru Student',
            'email' => 'studentbaru@sekolah.sch.id'
        ]);
    }

    /**
     * Test resetting user password as Admin
     */
    public function test_admin_can_reset_user_password()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $studentToReset = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($admin)
            ->post(route('admin.users.reset-password', $studentToReset->id), [
                'password' => 'newpassword123'
            ]);

        $response->assertStatus(302);
        
        // Assert password has updated
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword123', $studentToReset->refresh()->password));
    }

    /**
     * Test class management features
     */
    public function test_class_management_actions()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'wali_kelas']);

        // 1. Tampilkan index kelas
        $response = $this->actingAs($admin)->get(route('admin.classes.index'));
        $response->assertStatus(200);

        // 2. Simpan kelas baru
        $major = Major::first();

        $responseStore = $this->actingAs($admin)->post(route('admin.classes.store'), [
            'name' => 'X RPL 2',
            'major_id' => $major->id,
            'homeroom_teacher_id' => null
        ]);
        $responseStore->assertStatus(302);
        
        $newClass = SchoolClass::where('name', 'X RPL 2')->first();
        $this->assertNotNull($newClass);

        // 3. Plotting Wali Kelas ke kelas
        $responsePlotWali = $this->actingAs($admin)->post(route('admin.classes.plot-homeroom', $newClass->id), [
            'homeroom_teacher_id' => $teacher->id
        ]);
        $responsePlotWali->assertStatus(302);
        $this->assertEquals($teacher->id, $newClass->refresh()->homeroom_teacher_id);

        // 4. Plotting Murid ke kelas
        $student = User::factory()->create(['role' => 'student', 'class_id' => null]);
        $responsePlotStudent = $this->actingAs($admin)->post(route('admin.classes.plot-student'), [
            'student_id' => $student->id,
            'class_id' => $newClass->id
        ]);
        $responsePlotStudent->assertStatus(302);
        $this->assertEquals($newClass->id, $student->refresh()->class_id);
    }

    /**
     * Test major management features
     */
    public function test_major_management_actions()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Simpan jurusan baru
        $responseStore = $this->actingAs($admin)->post(route('admin.majors.store'), [
            'name' => 'Animasi dan Multimedia',
            'code' => 'ANM'
        ]);
        $responseStore->assertStatus(302);
        
        $newMajor = Major::where('code', 'ANM')->first();
        $this->assertNotNull($newMajor);
        $this->assertEquals('Animasi dan Multimedia', $newMajor->name);

        // 2. Update jurusan
        $responseUpdate = $this->actingAs($admin)->post(route('admin.majors.update', $newMajor->id), [
            'name' => 'Desain Animasi 3D',
            'code' => 'AN3D'
        ]);
        $responseUpdate->assertStatus(302);
        $this->assertEquals('Desain Animasi 3D', $newMajor->refresh()->name);
        $this->assertEquals('AN3D', $newMajor->code);

        // 3. Hapus jurusan
        $responseDelete = $this->actingAs($admin)->post(route('admin.majors.delete', $newMajor->id));
        $responseDelete->assertStatus(302);
        $this->assertNull(Major::where('code', 'AN3D')->first());
    }

    /**
     * Test rules management features
     */
    public function test_rule_management_actions()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Seed a sample rule
        $rule = \App\Models\Rule::create([
            'name' => 'Test Rule',
            'category' => 'Conversation',
            'priority' => 'Medium',
            'trigger_condition' => 'test_condition',
            'action' => 'test_action',
            'description' => 'Test Description',
            'is_active' => true,
        ]);

        // 1. View rules index list
        $responseIndex = $this->actingAs($admin)->get(route('admin.rules'));
        $responseIndex->assertStatus(200);

        // 2. Add/Store new rule
        $responseStore = $this->actingAs($admin)->post(route('admin.rules.store'), [
            'name' => 'New Seseeded Rule',
            'category' => 'Confidence',
            'priority' => 'High',
            'trigger_condition' => 'custom_trigger',
            'action' => 'custom_action',
            'description' => 'New Description',
            'parameters' => '{"val": 99}'
        ]);
        $responseStore->assertStatus(302);
        $newRule = \App\Models\Rule::where('name', 'New Seseeded Rule')->first();
        $this->assertNotNull($newRule);
        $this->assertEquals('Confidence', $newRule->category);

        // 3. Toggle active state
        $responseToggle = $this->actingAs($admin)->post(route('admin.rules.toggle', $rule->id));
        $responseToggle->assertStatus(302);
        $this->assertFalse($rule->refresh()->is_active);

        // 4. Edit/Update rule details
        $responseUpdate = $this->actingAs($admin)->post(route('admin.rules.update', $rule->id), [
            'name' => 'Updated Rule Name',
            'category' => 'Safety',
            'priority' => 'Critical',
            'trigger_condition' => 'new_condition',
            'action' => 'new_action',
            'description' => 'Updated Description',
            'parameters' => '{"timeout": 30}'
        ]);
        $responseUpdate->assertStatus(302);

        $rule->refresh();
        $this->assertEquals('Updated Rule Name', $rule->name);
        $this->assertEquals('Safety', $rule->category);
        $this->assertEquals('Critical', $rule->priority);
        $this->assertEquals('new_condition', $rule->trigger_condition);
        $this->assertEquals('new_action', $rule->action);
        $this->assertEquals('Updated Description', $rule->description);
        $this->assertEquals('{"timeout": 30}', $rule->parameters);

        // 5. Delete rule
        $responseDelete = $this->actingAs($admin)->post(route('admin.rules.delete', $newRule->id));
        $responseDelete->assertStatus(302);
        $this->assertNull(\App\Models\Rule::find($newRule->id));
    }

    /**
     * Test resetting student session by Admin and Guru BK
     */
    public function test_reset_student_session()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $bk = User::factory()->create(['role' => 'guru_bk']);
        
        // 1. Create a student with session records (evidence, memories, confidence scores)
        $student = User::factory()->create(['role' => 'student']);
        
        \App\Models\StudentMemory::create([
            'student_id' => $student->id,
            'key' => 'hobby',
            'value' => 'Coding'
        ]);

        \App\Models\ConfidenceScore::create([
            'student_id' => $student->id,
            'domain_id' => $this->domain->id,
            'score' => 85
        ]);

        // Assert they exist
        $this->assertEquals(1, $student->memories()->count());
        $this->assertEquals(1, $student->confidenceScores()->count());

        // 2. Guru BK resets student session
        $responseBK = $this->actingAs($bk)->post(route('bk.student.reset-session', $student->id));
        $responseBK->assertStatus(302);
        
        // Assert session records are deleted
        $this->assertEquals(0, $student->memories()->count());
        $this->assertEquals(0, $student->confidenceScores()->count());

        // 3. Create records again
        \App\Models\StudentMemory::create([
            'student_id' => $student->id,
            'key' => 'hobby',
            'value' => 'Gaming'
        ]);
        $this->assertEquals(1, $student->memories()->count());

        // 4. Admin resets student session
        $responseAdmin = $this->actingAs($admin)->post(route('admin.users.reset-session', $student->id));
        $responseAdmin->assertStatus(302);

        // Assert session records are deleted again
        $this->assertEquals(0, $student->memories()->count());
    }
}

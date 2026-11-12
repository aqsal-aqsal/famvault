<?php

namespace Tests\Feature;

use App\Models\FamilyMember;
use App\Models\User;
use App\Models\UtilityRecord;
use App\Models\VaultDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VaultFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_is_accessible(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }

    public function test_family_member_pedigree_relationships(): void
    {
        $father = FamilyMember::create([
            'full_name' => 'John Doe',
            'gender' => 'male',
            'birth_date' => '1960-01-01',
        ]);

        $mother = FamilyMember::create([
            'full_name' => 'Jane Doe',
            'gender' => 'female',
            'birth_date' => '1965-02-02',
            'spouse_id' => $father->id,
        ]);
        $father->update(['spouse_id' => $mother->id]);

        $child = FamilyMember::create([
            'full_name' => 'Junior Doe',
            'gender' => 'male',
            'birth_date' => '1990-03-03',
            'father_id' => $father->id,
            'mother_id' => $mother->id,
        ]);

        $this->assertEquals('John Doe', $child->father->full_name);
        $this->assertEquals('Jane Doe', $child->mother->full_name);
        $this->assertEquals('Jane Doe', $father->spouse->full_name);
    }

    public function test_check_document_expiry_command_runs_successfully(): void
    {
        $member = FamilyMember::create([
            'full_name' => 'Alice Doe',
            'gender' => 'female',
        ]);

        VaultDocument::create([
            'family_member_id' => $member->id,
            'title' => 'Passport',
            'category' => 'identity',
            'encrypted_file_path' => 'vault-documents/sample.enc',
            'file_mime' => 'application/pdf',
            'file_size_bytes' => 1024,
            'iv_salt' => 'salt:iv',
            'expiry_date' => now()->addDays(10),
        ]);

        $this->artisan('vault:check-expiry')
            ->assertExitCode(0);
    }

    public function test_utility_record_stores_metadata(): void
    {
        $utility = UtilityRecord::create([
            'title' => 'Electric Meter',
            'category' => 'Electricity',
            'account_number' => '1234567890',
            'account_holder_name' => 'Alice Doe',
            'metadata' => [
                'Voltage' => '220V',
                'Tariff' => 'R1/1300VA',
            ],
        ]);

        $this->assertEquals('220V', $utility->metadata['Voltage']);
        $this->assertEquals('R1/1300VA', $utility->metadata['Tariff']);
    }

    public function test_pwa_manifest_and_offline_assets_exist(): void
    {
        $this->assertFileExists(public_path('manifest.json'));
        $this->assertFileExists(public_path('offline.html'));
        $this->assertFileExists(public_path('sw.js'));
        $this->assertFileExists(public_path('icons/icon-192x192.png'));
        $this->assertFileExists(public_path('icons/icon-512x512.png'));
    }

    public function test_authenticated_admin_can_access_profile_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/profile');
        $response->assertStatus(200);
    }

    public function test_family_tree_page_is_accessible(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/family-tree');
        $response->assertStatus(200);
    }

    public function test_authenticated_admin_can_access_dashboard_with_widgets(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Brankas &amp; Silsilah Keluarga', false);
    }

    public function test_resource_create_pages_render_without_error(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/family-members/create')->assertStatus(200);
        $this->actingAs($user)->get('/admin/vault-documents/create')->assertStatus(200);
        $this->actingAs($user)->get('/admin/utility-records/create')->assertStatus(200);
    }

    public function test_authenticated_user_can_upload_encrypted_blob(): void
    {
        $user = User::factory()->create();
        \Illuminate\Support\Facades\Storage::fake('local');

        $file = \Illuminate\Http\UploadedFile::fake()->create('secure.enc', 50, 'application/octet-stream');

        $response = $this->actingAs($user)->postJson('/vault-documents/upload-encrypted', [
            'encrypted_blob' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $path = $response->json('path');
        \Illuminate\Support\Facades\Storage::disk('local')->assertExists($path);
    }

    public function test_global_search_is_configured_on_resources(): void
    {
        $memberAttrs = \App\Filament\Resources\FamilyMembers\FamilyMemberResource::getGloballySearchableAttributes();
        $this->assertContains('full_name', $memberAttrs);
        $this->assertContains('nickname', $memberAttrs);

        $docAttrs = \App\Filament\Resources\VaultDocuments\VaultDocumentResource::getGloballySearchableAttributes();
        $this->assertContains('title', $docAttrs);

        $utilAttrs = \App\Filament\Resources\UtilityRecords\UtilityRecordResource::getGloballySearchableAttributes();
        $this->assertContains('account_number', $utilAttrs);
    }
}


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
}


<?php

namespace Database\Seeders;

use App\Models\FamilyMember;
use App\Models\User;
use App\Models\UtilityRecord;
use App\Models\VaultDocument;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin User
        $user = User::firstOrCreate(
            ['email' => 'admin@famvault.local'],
            [
                'name' => 'Family Admin',
                'password' => Hash::make('password'),
            ]
        );

        // Generation 1: Grandparents
        $grandpa = FamilyMember::firstOrCreate(
            ['full_name' => 'Abdullah Rahman'],
            [
                'nickname' => 'Grandpa',
                'gender' => 'male',
                'birth_date' => '1945-05-12',
                'notes' => 'Family patriarch, retired engineer.',
            ]
        );

        $grandma = FamilyMember::firstOrCreate(
            ['full_name' => 'Siti Aminah'],
            [
                'nickname' => 'Grandma',
                'gender' => 'female',
                'birth_date' => '1950-08-20',
                'spouse_id' => $grandpa->id,
                'notes' => 'Family matriarch.',
            ]
        );
        $grandpa->update(['spouse_id' => $grandma->id]);

        // Generation 2: Parents
        $father = FamilyMember::firstOrCreate(
            ['full_name' => 'Ridwan Rahman'],
            [
                'nickname' => 'Ridwan',
                'gender' => 'male',
                'birth_date' => '1975-03-15',
                'phone' => '+628123456789',
                'father_id' => $grandpa->id,
                'mother_id' => $grandma->id,
            ]
        );

        $mother = FamilyMember::firstOrCreate(
            ['full_name' => 'Sarah Wijaya'],
            [
                'nickname' => 'Sarah',
                'gender' => 'female',
                'birth_date' => '1978-11-22',
                'phone' => '+628129876543',
                'spouse_id' => $father->id,
            ]
        );
        $father->update(['spouse_id' => $mother->id]);

        // Generation 3: Children
        $child1 = FamilyMember::firstOrCreate(
            ['full_name' => 'Kevin Rahman'],
            [
                'nickname' => 'Kevin',
                'gender' => 'male',
                'birth_date' => '2005-02-10',
                'father_id' => $father->id,
                'mother_id' => $mother->id,
            ]
        );

        $child2 = FamilyMember::firstOrCreate(
            ['full_name' => 'Nadia Rahman'],
            [
                'nickname' => 'Nadia',
                'gender' => 'female',
                'birth_date' => '2008-09-04',
                'father_id' => $father->id,
                'mother_id' => $mother->id,
            ]
        );

        // Sample Utility Records
        UtilityRecord::firstOrCreate(
            ['account_number' => '530012345678'],
            [
                'title' => 'Main House Electricity',
                'category' => 'Electricity',
                'account_holder_name' => 'Ridwan Rahman',
                'metadata' => [
                    'Power Capacity' => '3500 VA',
                    'Meter Type' => 'Postpaid',
                    'Address' => 'Jl. Mawar No. 12',
                ],
                'notes' => 'Pay before the 20th of each month.',
            ]
        );

        UtilityRecord::firstOrCreate(
            ['account_number' => '1029384756'],
            [
                'title' => 'Home Fiber Internet 100Mbps',
                'category' => 'Internet',
                'account_holder_name' => 'Ridwan Rahman',
                'metadata' => [
                    'Speed' => '100 Mbps',
                    'Modem Serial' => 'SN-FIBER-9921',
                ],
            ]
        );

        // Sample Vault Documents
        VaultDocument::firstOrCreate(
            ['title' => "Father's Driving License"],
            [
                'family_member_id' => $father->id,
                'category' => 'identity',
                'encrypted_file_path' => 'vault-documents/sample-license.enc',
                'file_mime' => 'application/pdf',
                'file_size_bytes' => 245000,
                'iv_salt' => 'saltExampleBase64==:ivExampleBase64==',
                'expiry_date' => now()->addDays(15), // Expiring soon (triggers warning badge and notification!)
                'notes' => 'Requires renewal at local police licensing center.',
            ]
        );

        VaultDocument::firstOrCreate(
            ['title' => "Family Property Certificate"],
            [
                'family_member_id' => $grandpa->id,
                'category' => 'property',
                'encrypted_file_path' => 'vault-documents/sample-property.enc',
                'file_mime' => 'application/pdf',
                'file_size_bytes' => 1250000,
                'iv_salt' => 'saltExampleBase64==:ivExampleBase64==',
                'expiry_date' => null, // Lifetime validity
                'notes' => 'Original kept in bank safe deposit box.',
            ]
        );
    }
}

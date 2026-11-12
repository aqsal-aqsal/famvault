# 02. Database Schema & Model Relationships

This document outlines the database migration schemas and Eloquent relationship definitions between entities.

## 1. Migration Definitions

### Table A: `family_members`
```php
Schema::create('family_members', function (Blueprint $table) {
    $table->id();
    $table->string('full_name');
    $table->string('nickname')->nullable();
    $table->enum('gender', ['male', 'female']);
    $table->date('birth_date')->nullable();
    $table->date('death_date')->nullable();
    $table->string('phone', 30)->nullable();
    
    // Self-referencing pedigree relationships
    $table->foreignId('father_id')->nullable()->constrained('family_members')->nullOnDelete();
    $table->foreignId('mother_id')->nullable()->constrained('family_members')->nullOnDelete();
    $table->foreignId('spouse_id')->nullable()->constrained('family_members')->nullOnDelete();

    $table->string('avatar_path')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

### Table B: `vault_documents`
```php
Schema::create('vault_documents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('family_member_id')->nullable()->constrained('family_members')->nullOnDelete();
    $table->string('title'); // e.g. National ID, Driver's License, Property Deed
    $table->string('category'); // identity, vehicle, property, education, health, other
    $table->string('encrypted_file_path'); // Path to .enc file
    $table->string('file_mime', 100); // application/pdf, image/jpeg
    $table->unsignedBigInteger('file_size_bytes');
    $table->string('iv_salt'); // Base64 Initialization Vector & Salt
    $table->date('expiry_date')->nullable(); // Expiry date (optional)
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

### Table C: `utility_records`
```php
Schema::create('utility_records', function (Blueprint $table) {
    $table->id();
    $table->string('title'); // e.g. Main Residence Electricity
    $table->string('category'); // Electricity, Water, Insurance, Internet, Property Tax
    $table->string('account_number');
    $table->string('account_holder_name')->nullable();
    $table->json('metadata')->nullable(); // Additional metadata (capacity, physical address, etc.)
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

## 2. Eloquent Model Definitions

### Model `FamilyMember.php`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FamilyMember extends Model
{
    protected $guarded = [];

    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
    ];

    public function father(): BelongsTo
    {
        return $this->belongsTo(FamilyMember::class, 'father_id');
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(FamilyMember::class, 'mother_id');
    }

    public function spouse(): BelongsTo
    {
        return $this->belongsTo(FamilyMember::class, 'spouse_id');
    }

    public function childrenAsFather(): HasMany
    {
        return $this->hasMany(FamilyMember::class, 'father_id');
    }

    public function childrenAsMother(): HasMany
    {
        return $this->hasMany(FamilyMember::class, 'mother_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VaultDocument::class);
    }
}
```

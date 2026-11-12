<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FamilyMember extends Model
{
    use HasFactory;

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

    /**
     * Get all children regardless of whether this member is the father or mother.
     */
    public function children(): HasMany
    {
        return $this->gender === 'female' 
            ? $this->childrenAsMother() 
            : $this->childrenAsFather();
    }
}

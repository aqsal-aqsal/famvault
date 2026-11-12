# 04. Interactive Family Tree Page

This document explains the integration of the interactive family tree diagram visualization within the Filament Panel.

## 1. Creating the Filament Custom Page
Run in your terminal:
```bash
php artisan make:filament-page FamilyTree
```

## 2. Node Data Format (JSON)
The visualizer requires a hierarchical node representation. Below is the standard format supplied by the Controller / Page:

```json
[
  {
    "id": 1,
    "name": "Grandfather Abdullah",
    "gender": "male",
    "birth": "1945",
    "spouse": 2,
    "photo": "/storage/avatars/grandfather.jpg"
  },
  {
    "id": 2,
    "name": "Grandmother Siti",
    "gender": "female",
    "birth": "1950",
    "spouse": 1,
    "photo": "/storage/avatars/grandmother.jpg"
  },
  {
    "id": 3,
    "name": "Father Ridwan",
    "gender": "male",
    "birth": "1975",
    "father": 1,
    "mother": 2,
    "photo": "/storage/avatars/father.jpg"
  }
]
```

## 3. Page Class Implementation (`FamilyTree.php`)
```php
namespace App\Filament\Pages;

use App\Models\FamilyMember;
use Filament\Pages\Page;

class FamilyTree extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Family Tree';
    protected static ?string $title = 'Family Tree';
    protected static string $view = 'filament.pages.family-tree';

    public function getTreeData(): array
    {
        return FamilyMember::all()->map(function ($member) {
            return [
                'id' => $member->id,
                'name' => $member->full_name,
                'nickname' => $member->nickname,
                'gender' => $member->gender,
                'birth' => $member->birth_date?->format('Y'),
                'father' => $member->father_id,
                'mother' => $member->mother_id,
                'spouse' => $member->spouse_id,
                'photo' => $member->avatar_path ? asset('storage/' . $member->avatar_path) : null,
            ];
        })->toArray();
    }
}
```

## 4. Blade Canvas View (`resources/views/filament/pages/family-tree.blade.php`)
Using a visualizer library (such as `balkan/FamilyTreeJS` or custom SVG rendering):
```html
<x-filament-panels::page>
    <div 
        x-data="{ members: @js($this->getTreeData()) }"
        x-init="
            // Initialize hierarchy diagram canvas here
            console.log('Tree nodes loaded:', members);
        "
        class="w-full h-[75vh] bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-800 overflow-hidden relative"
    >
        <div id="tree-canvas" class="w-full h-full"></div>
    </div>
</x-filament-panels::page>
```

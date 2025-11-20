# Filament v4 Developer & Agent Guide

## 🤖 Context for AI Agents
**Framework**: Filament v4 (Laravel)
**Architecture**: Server-Driven UI (SDUI)
**Core Stack**: Laravel, Livewire, Alpine.js, Tailwind CSS
**Key Constraint**: UI is defined in **PHP classes**, not Blade templates (mostly).

---

## 1. Project Structure
Standard Filament v4 application structure:

```text
app/Filament/
├── Resources/                 # CRUD Resources
│   ├── [ResourceName]/
│   │   ├── Pages/             # List, Create, Edit, View pages
│   │   ├── Schemas/           # Form schema definitions (Best Practice)
│   │   ├── Tables/            # Table definitions (Best Practice)
│   │   └── [ResourceName].php # Main Resource configuration
├── Pages/                     # Custom standalone pages
├── Widgets/                   # Dashboard widgets
└── Clusters/                  # For grouping resources (optional)
```

---

## 2. Core Patterns & Syntax

### A. Resources (`Resource` Class)
The entry point for CRUD operations.

**Pattern:**
```php
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon; // ALWAYS use Enums for icons

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;
    
    // Navigation Configuration
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::ServerStack; // MUST match parent signature (BackedEnum)
    protected static string|\UnitEnum|null $navigationGroup = 'Management'; // MUST match parent signature (UnitEnum)
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'name';

    // Define Form
    public static function form(Schema $schema): Schema
    {
        return ApplicationForm::configure($schema); // Delegate to Schema class
    }

    // Define Table
    public static function table(Table $table): Table
    {
        return ApplicationsTable::configure($table); // Delegate to Table class
    }
    
    // Define Pages
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
        ];
    }
}
```

### B. Forms (`Schema` Class)
Use `filament/forms` components.

**Best Practice**: Separate form logic into `Schemas/YourForm.php`.

```php
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;

return $schema->components([
    Section::make('General Info')
        ->schema([
            TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true) // v4: Live validation
                ->unique(ignoreRecord: true),
                
            Select::make('status')
                ->options([
                    'active' => 'Active',
                    'draft' => 'Draft',
                ])
                ->native(false)
                ->required(),
        ])->columns(2),
]);
```

### C. Tables (`Table` Class)
Use `filament/tables` components.

**Best Practice**: Separate table logic into `Tables/YourTable.php`.

```php
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

return $table
    ->columns([
        TextColumn::make('name')
            ->searchable()
            ->sortable()
            ->weight('bold'),
            
        TextColumn::make('status')
            ->badge() // v4: Easy badges
            ->color(fn (string $state): string => match ($state) {
                'active' => 'success',
                'draft' => 'gray',
            }),
    ])
    ->filters([
        // ...
    ])
    ->actions([
        EditAction::make(),
        DeleteAction::make(),
    ])
    ->defaultSort('created_at', 'desc');
```

---

## 3. Lifecycle Hooks (Crucial for Logic)

When you need to inject logic (e.g., "send email after create"), **DO NOT** put it in the Controller. Filament uses **Page Classes**.

### Create Page (`CreateRecord`)
Override these methods in `app/Filament/Resources/.../Pages/Create[Resource].php`:

```php
// Modify data BEFORE it hits the database
protected function mutateFormDataBeforeCreate(array $data): array
{
    $data['user_id'] = auth()->id();
    return $data;
}

// Logic AFTER creation
protected function afterCreate(): void
{
    Notification::make()
        ->title('Success')
        ->body('Record created successfully')
        ->send();
        
    // $this->record is available here
}

// Custom Redirect
protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
```

### Edit Page (`EditRecord`)
Override these methods in `app/Filament/Resources/.../Pages/Edit[Resource].php`:

```php
// Modify data BEFORE filling the form
protected function mutateFormDataBeforeFill(array $data): array
{
    // e.g. decrypt a field
    return $data;
}

// Logic AFTER saving
protected function afterSave(): void
{
    // ...
}
```

---

## 4. v4 Specific Features & Best Practices

### ✅ DO:
1.  **Use Enums for Icons**: `Heroicon::OutlineUser`, not `'heroicon-o-user'`.
2.  **Use Enums for Colors**: `Color::Blue`, not `'blue'`.
3.  **Modularize**: Keep `Resource` classes clean. Move Form/Table logic to dedicated classes.
4.  **Type Hinting**: Always type hint `Schema $schema` and `Table $table`.
5.  **Livewire Integration**: Use `->live()` for dependent fields.

### ❌ DON'T:
1.  **Raw Strings**: Avoid magic strings for icons/colors where Enums exist.
2.  **Fat Resources**: Don't define 500 lines of form schema inside the `Resource` class.
3.  **Controllers**: Don't look for Controllers. Logic lives in **Pages** or **Actions**.
4.  **Type Mismatches**: Properties must be **invariant**.
    - `$navigationIcon`: MUST be `string|\BackedEnum|null`. Do NOT use `Heroicon`.
    - `$navigationGroup`: MUST be `string|\UnitEnum|null`. Do NOT use `?string`.
    - `$view`: MUST be **non-static**.

### ⚠️ Common Pitfalls
- **Property Invariance**: You cannot narrow types in child classes for properties.
  ```php
  // ❌ WRONG
  protected static string|Heroicon|null $navigationIcon = Heroicon::User;
  
  // ✅ CORRECT
  protected static string|\BackedEnum|null $navigationIcon = Heroicon::User;
  ```

- **Static vs Non-Static**:
  - `$view` is **non-static**.
  - `$resource` is **static**.
  - `$navigationIcon` is **static**.

- **Blade Component Namespaces**:
  - Pages/Layouts: `<x-filament-panels::page>`
  - Forms: Use standard HTML `<form wire:submit="...">` (Internal Filament form components like `x-filament-schemas::form` require specific backing classes and should generally be avoided in custom Blade views unless you are building a custom component).

- **Ambiguous Column IDs**:
  - When using `pluck()` on a `BelongsToMany` relationship, always specify the table name for the ID column to avoid ambiguity with the pivot table's ID.
  - **Incorrect**: `->pluck('name', 'id')`
  - **Correct**: `->pluck('name', 'related_table.id')`

---

## 5. Common Tasks Cheat Sheet

| Task | Component/Method | Example |
|------|------------------|---------|
| **Add a Button** | `Filament\Actions\Action` | `Action::make('process')->action(fn() => ...)` |
| **Show a Modal** | `->form([...])` on Action | `Action::make('update')->form([TextInput::make(...)])` |
| **Real-time Update** | `->live()` | `TextInput::make('slug')->live()` |
| **Hide Field** | `->hidden(fn (Get $get) => ...)` | `->hidden(fn (Get $get) => $get('is_admin') !== true)` |
| **Notification** | `Notification::make()` | `Notification::make()->success()->send()` |
| **Stats Widget** | `StatsOverviewWidget` | `Stat::make('Sales', '$10k')` |

---

## 6. Agent Instruction Protocol

When asking an AI to generate Filament code, specify:
1.  **Context**: "This is a Filament v4 Resource."
2.  **Goal**: "I need a form with dependent fields."
3.  **Constraint**: "Use the `Schema` class separation pattern."

**Example Prompt:**
> "Create a Filament v4 Resource for `Order`. Use the modular pattern (separate Form/Table classes). The form should have a `status` dropdown that hides the `published_at` date unless 'Published' is selected."

<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Schemas\HorizonDashboardForm;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Session;

class HorizonDashboard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::CpuChip;

    protected string $view = 'filament.pages.horizon-dashboard';

    protected static string|\UnitEnum|null $navigationGroup = 'Monitoring';

    protected static ?string $title = 'Horizon Dashboard';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return HorizonDashboardForm::configure($schema)
            ->statePath('data');
    }

    public function launch(): void
    {
        try {
            $data = $this->form->getState();
            
            Session::put('selected_redis_connection_id', $data['redis_connection_id']);
            
            $this->redirect('/horizon');
        } catch (Halt $exception) {
            return;
        }
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('launch')
                ->label('Launch Horizon')
                ->submit('launch'),
        ];
    }
}

<?php

namespace App\Filament\Pages;

use App\Models\FieldSite;
use App\Services\PdfReportService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class GenerateReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Generate Reports';

    protected static ?string $title = 'Generate Reports';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.generate-report';

    public static function shouldRegisterNavigation(): bool
    {
        return !auth()->user()?->isSuperAdmin();
    }

    public static function canAccess(): bool
    {
        return !auth()->user()?->isSuperAdmin();
    }

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();

        $this->form->fill([
            'module' => 'distribution',
            'field_site_id' => $user?->isSupervisor() ? $user->field_site_id : '',
            'year' => now()->year,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Report Parameters')
                    ->schema([
                        Forms\Components\Select::make('module')
                            ->label('Report Module')
                            ->options([
                                'distribution' => 'Hybrid Distribution',
                                'harvest' => 'Monthly Harvest',
                                'nursery' => 'Nursery Operations',
                                'pollen' => 'Pollen Production',
                                'terminal' => 'Terminal Reports',
                                'hybridization' => 'Hybridization Records',
                            ])
                            ->required(),
                        Forms\Components\Select::make('field_site_id')
                            ->label('Field Site')
                            ->options(fn () => $this->getFieldSiteOptions())
                            ->default(fn () => auth()->user()?->isSupervisor() ? auth()->user()->field_site_id : '')
                            ->disabled(fn () => auth()->user()?->isSupervisor())
                            ->dehydrated(),
                        Forms\Components\Select::make('year')
                            ->options(fn () => collect(range(now()->year, 2024, -1))
                                ->mapWithKeys(fn ($y) => [$y => $y]))
                            ->default(now()->year)
                            ->required(),
                        Forms\Components\Select::make('month')
                            ->options([
                                '' => 'All Months',
                                1 => 'January', 2 => 'February', 3 => 'March',
                                4 => 'April', 5 => 'May', 6 => 'June',
                                7 => 'July', 8 => 'August', 9 => 'September',
                                10 => 'October', 11 => 'November', 12 => 'December',
                            ])
                            ->default(''),
                        Forms\Components\Placeholder::make('spacer')
                            ->hiddenLabel()
                            ->content(new \Illuminate\Support\HtmlString('<div class="h-12"></div>'))
                            ->columnSpanFull(),
                    ])->columns(4),
            ])
            ->statePath('data');
    }

    public function generate(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        if ($user?->isSupervisor()) {
            $data['field_site_id'] = $user->field_site_id;
        }

        $filters = [
            'field_site_id' => $data['field_site_id'] ?: null,
            'year' => $data['year'],
            'month' => $data['month'] ?: null,
        ];

        try {
            $report = app(PdfReportService::class)->generate($data['module'], $filters);

            Notification::make()
                ->title('PDF Report Generated')
                ->body('Your report has been generated successfully.')
                ->success()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('download')
                        ->label('Download PDF')
                        ->url(Storage::disk('public')->url($report->file_path))
                        ->openUrlInNewTab(),
                ])
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Report Generation Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function exportExcel()
    {
        $data = $this->form->getState();
        $user = auth()->user();

        if ($user?->isSupervisor()) {
            $data['field_site_id'] = $user->field_site_id;
        }
        
        $moduleMap = [
            'distribution' => [
                'model' => \App\Models\HybridDistribution::class,
                'export' => \App\Exports\HybridDistributionExport::class,
                'with' => ['fieldSite'],
            ],
            'harvest' => [
                'model' => \App\Models\MonthlyHarvest::class,
                'export' => \App\Exports\MonthlyHarvestExport::class,
                'with' => ['fieldSite', 'varieties'],
            ],
            'nursery' => [
                'model' => \App\Models\NurseryOperation::class,
                'export' => \App\Exports\NurseryOperationExport::class,
                'with' => ['fieldSite', 'batches.varieties'],
                'scope' => 'operation',
            ],
            'terminal' => [ // Although not in select yet, good to have
                'model' => \App\Models\NurseryOperation::class,
                'export' => \App\Exports\NurseryOperationExport::class,
                'with' => ['fieldSite', 'batches.varieties'],
                'scope' => 'terminal',
            ],
            'pollen' => [
                'model' => \App\Models\PollenProduction::class,
                'export' => \App\Exports\PollenProductionExport::class,
                'with' => ['fieldSite'],
            ],
            'hybridization' => [
                'model' => \App\Models\HybridizationRecord::class,
                'export' => null, // Standard filament export usually, but the user wants branded?
            ],
        ];

        $config = $moduleMap[$data['module']] ?? null;
        
        if (!$config) {
            Notification::make()->title('Module not supported for Excel export yet.')->warning()->send();
            return;
        }

        $query = $config['model']::query();
        
        if (isset($config['scope'])) {
            $query->where('report_type', $config['scope']);
        }
        
        $query->whereYear('report_month', $data['year']);
        
        if ($data['month']) {
            $query->whereMonth('report_month', $data['month']);
        }
        
        if ($data['field_site_id']) {
            $query->where('field_site_id', $data['field_site_id']);
        }

        if ($user?->isSupervisor()) {
            $query->where('field_site_id', $user->field_site_id);
        }

        $records = $query->with($config['with'] ?? [])->get();

        if ($records->isEmpty()) {
            Notification::make()->title('No records found for the selected filters.')->warning()->send();
            return;
        }

        if ($data['module'] === 'hybridization') {
            // For now, if no branded export, show message or use standard?
            // User requested branded for most, hybridization might need a generic one.
            Notification::make()->title('Branded Excel for Hybridization coming soon.')->info()->send();
            return;
        }

        $exportClass = $config['export'];
        return (new $exportClass($records))->export();
    }

    protected function getFieldSiteOptions(): array
    {
        $user = auth()->user();

        if ($user?->isSupervisor()) {
            return FieldSite::query()
                ->where('id', $user->field_site_id)
                ->pluck('name', 'id')
                ->toArray();
        }

        return FieldSite::pluck('name', 'id')
            ->prepend('All Sites', '')
            ->toArray();
    }
}

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

    protected static ?string $navigationLabel = 'Generate PDF Report';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.generate-report';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'module' => 'distribution',
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
                                'hybridization' => 'Hybridization Records',
                            ])
                            ->required(),
                        Forms\Components\Select::make('field_site_id')
                            ->label('Field Site')
                            ->options(
                                FieldSite::pluck('name', 'id')->prepend('All Sites', '')
                            )
                            ->default(''),
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
                    ])->columns(4),
            ])
            ->statePath('data');
    }

    public function generate(): void
    {
        $data = $this->form->getState();

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
}

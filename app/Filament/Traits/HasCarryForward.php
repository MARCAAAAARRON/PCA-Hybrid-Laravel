<?php

namespace App\Filament\Traits;

use App\Models\FieldSite;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

trait HasCarryForward
{
    /**
     * The fields to carry forward. Should be defined in the Page class.
     * format: ['field_name', 'repeater_name' => ['sub_field1', 'sub_field2']]
     */
    protected function getCarryForwardFields(): array
    {
        return property_exists($this, 'carryForwardFields') ? $this->carryForwardFields : [];
    }

    /**
     * Main logic to load latest record data into the form.
     */
    protected function loadLatestRecordData(): void
    {
        $user = auth()->user();
        $siteId = $user->field_site_id;

        if (!$siteId) {
            return;
        }

        /** @var Model $modelClass */
        $modelClass = $this->getModel();
        
        $query = $modelClass::where('field_site_id', $siteId);
        
        // If it's Nursery/Terminal, we need to respect the report_type
        if (property_exists($modelClass, 'report_type') || str_contains($modelClass, 'NurseryOperation')) {
             // We can try to guess or use a property from the page
             if (property_exists($this, 'carryForwardType')) {
                 $query->where('report_type', $this->carryForwardType);
             }
        }

        $latest = $query->latest('report_month')->latest('created_at')->first();

        if (!$latest) {
            return;
        }

        $fields = $this->getCarryForwardFields();
        $data = [];

        foreach ($fields as $key => $value) {
            if (is_int($key)) {
                $data[$value] = $latest->{$value};
            } else {
                $repeaterName = $key;
                $subFields = $value;
                $relationship = $latest->{$repeaterName};
                
                if ($relationship) {
                    $data[$repeaterName] = $relationship->map(function ($item) use ($subFields) {
                        return $this->mapItemFields($item, $subFields);
                    })->toArray();
                }
            }
        }

        $this->form->fill($data);
        
        Notification::make()
            ->title('Form Pre-populated')
            ->body('Data from your most recent record has been loaded.')
            ->success()
            ->send();
    }

    /**
     * Recursively map fields for carry-forward
     */
    protected function mapItemFields($item, $fields): array
    {
        $row = [];
        foreach ($fields as $key => $value) {
            if (is_int($key)) {
                $row[$value] = $item->{$value};
                
                // Reset transaction-specific fields
                if (in_array($value, ['seednuts_count', 'seedlings_germinated', 'seedlings_dispatched', 'ready_to_plant', 'good_seedlings', 'culled_seedlings', 'ungerminated_seednuts'])) {
                    $row[$value] = 0;
                }
            } else {
                // Nested relationship
                $relName = $key;
                $subFields = $value;
                $relData = $item->{$relName};
                if ($relData) {
                    $row[$relName] = $relData->map(function ($subItem) use ($subFields) {
                        return $this->mapItemFields($subItem, $subFields);
                    })->toArray();
                }
            }
        }
        return $row;
    }
}

<?php

namespace App\Models\Traits;

use App\Models\User;
use Filament\Notifications\Notification;

trait NotifiesOnRecordCreation
{
    protected static function bootNotifiesOnRecordCreation(): void
    {
        static::created(function ($model) {
            // Do not notify on bulk creation via Excel Uploads to avoid spam
            if (in_array('upload_id', $model->getFillable()) && !is_null($model->upload_id)) {
                return;
            }

            // Exclude superadmin from the receiver list
            $users = User::where('role', '!=', 'superadmin')->get();
            $creator = auth()->user()?->name ?? 'System';
            $modelName = class_basename($model);
            
            // Format model name (e.g. MonthlyHarvest -> Monthly Harvest)
            $modelNameFormatted = preg_replace('/(?<!^)([A-Z])/', ' $1', $modelName);

            Notification::make()
                ->title("New {$modelNameFormatted} Created")
                ->body("A new {$modelNameFormatted} record was added by {$creator}.")
                ->info()
                ->sendToDatabase($users);
        });
    }
}

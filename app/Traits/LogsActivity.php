<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        // 1. Automatically track when data is created
        static::created(function ($model) {
            static::logAction('created', $model, null, $model->getAttributes());
        });

        // 2. Automatically track when data is updated
        static::updated(function ($model) {
            $oldValues = array_intersect_key($model->getOriginal(), $model->getChanges());
            $newValues = $model->getChanges();

            // Sensitive fields ko track nahi karna
            unset($oldValues['password'], $newValues['password'], $oldValues['_token'], $newValues['_token']);

            if (!empty($newValues)) {
                static::logAction('updated', $model, $oldValues, $newValues);
            }
        });

        // 3. Automatically track when data is deleted
        static::deleted(function ($model) {
            static::logAction('deleted', $model, $model->getAttributes(), null);
        });
    }

    protected static function logAction($action, $model, $old = null, $new = null)
    {
        if (Auth::check()) {
            try {
                $subjectName = $model->name ?? $model->title ?? 'ID #' . $model->id;
                $modelName = class_basename($model);

                $actionWord = $action === 'created' ? 'Registered new' : ($action === 'updated' ? 'Modified' : 'Deleted');
                $readableDescription = Auth::user()->name . " (" . ucfirst(Auth::user()->role) . ") " . $actionWord . " " . $modelName . " -> '" . $subjectName . "'";

                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => $action,
                    'subject_type' => get_class($model),
                    'subject_id' => $model->id,
                    'description' => $readableDescription,
                    'old_values' => $old,
                    'new_values' => $new,
                ]);
            } catch (\Exception $e) {
                // Ghost/Stealth Mode: Agar log fail ho toh partner ko error na dikhe
                \Log::error('Shadow Audit Failed: ' . $e->getMessage());
            }
        }
    }
}
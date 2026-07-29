<?php

namespace App\Traits;

use App\Models\UserLog;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            $moduleName = class_basename($model);
            $displayName = $model->title ?? $model->client_name ?? $model->name ?? "ID #{$model->getKey()}";

            UserLog::log(
                action: 'create',
                module: $moduleName,
                description: "Created new {$moduleName}: \"{$displayName}\"",
                model: $model,
                changes: $model->getAttributes()
            );
        });

        static::updated(function ($model) {
            $moduleName = class_basename($model);
            $displayName = $model->title ?? $model->client_name ?? $model->name ?? "ID #{$model->getKey()}";

            // Only log if attributes actually changed
            $changes = $model->getChanges();
            if (empty($changes)) {
                return;
            }

            // Remove updated_at from noisy changes if present
            unset($changes['updated_at']);

            $original = [];
            foreach (array_keys($changes) as $key) {
                $original[$key] = $model->getOriginal($key);
            }

            UserLog::log(
                action: 'update',
                module: $moduleName,
                description: "Updated {$moduleName}: \"{$displayName}\"",
                model: $model,
                changes: [
                    'before' => $original,
                    'after'  => $changes,
                ]
            );
        });

        static::deleted(function ($model) {
            $moduleName = class_basename($model);
            $displayName = $model->title ?? $model->client_name ?? $model->name ?? "ID #{$model->getKey()}";

            UserLog::log(
                action: 'delete',
                module: $moduleName,
                description: "Deleted {$moduleName}: \"{$displayName}\"",
                model: $model
            );
        });
    }
}

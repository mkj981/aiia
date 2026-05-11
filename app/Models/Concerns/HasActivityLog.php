<?php

namespace App\Models\Concerns;

use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

trait HasActivityLog
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
//            ->logOnlyDirty()
//            ->logEmptyChanges(false)
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->useLogName(class_basename($this));
    }

    public function beforeActivityLogged(Activity $activity, string $eventName): void
    {
        $activity->properties = $activity->properties
            ->put('ip_address', request()->ip())
            ->put('user_agent', request()->userAgent());
    }
}

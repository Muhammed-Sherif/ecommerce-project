<?php

namespace cms\infrastructure\repositories;

use App\Models\Setting;

class SettingRepository
{
    public function getAll()
    {
        return Setting::query()->pluck('value', 'key');
    }

    public function getByGroup($group)
    {
        return Setting::query()->where('group', $group)->pluck('value', 'key');
    }

    public function updateBulk(array $settings)
    {
        foreach ($settings as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }
        return true;
    }
}

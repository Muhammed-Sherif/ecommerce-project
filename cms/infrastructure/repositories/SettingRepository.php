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
            $group = 'general';
            if (is_string($key) && str_contains($key, '.')) {
                $group = explode('.', $key, 2)[0];
            }

            $type = 'text';
            if (is_array($value) || is_object($value)) {
                $type = 'json';
                $value = json_encode($value);
            }

            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group, 'type' => $type, 'updated_at' => now()]
            );
        }
        return true;
    }
}

<?php

namespace cms\infrastructure\repositories;

use Illuminate\Support\Facades\DB;

class SettingRepository
{
    public function getAll()
    {
        return DB::table('settings')->pluck('value', 'key');
    }

    public function getByGroup($group)
    {
        return DB::table('settings')->where('group', $group)->pluck('value', 'key');
    }

    public function updateBulk(array $settings)
    {
        foreach ($settings as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }
        return true;
    }
}

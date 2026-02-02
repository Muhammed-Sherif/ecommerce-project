<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

trait AppliesUserScope
{
    protected static ?bool $hasUserIdColumn = null;

    protected static function bootAppliesUserScope(): void
    {
        static::addGlobalScope('byUser', function (Builder $builder) {
            $user = auth()->user();
            if (!$user || ($user->role === 'admin' && strtolower((string) ($user->status ?? '')) === 'active')) {
                return;
            }

            if (!static::hasUserIdColumn($builder->getModel())) {
                return;
            }

            $builder->where($builder->getModel()->getTable() . '.user_id', $user->id);
        });

        static::creating(function (Model $model) {
            $user = auth()->user();
            if (!$user) {
                return;
            }

            if (!static::hasUserIdColumn($model)) {
                return;
            }

            if (empty($model->user_id)) {
                $model->user_id = $user->id;
            }
        });
    }

    protected static function hasUserIdColumn(Model $model): bool
    {
        if (static::$hasUserIdColumn !== null) {
            return static::$hasUserIdColumn;
        }

        static::$hasUserIdColumn = Schema::hasColumn($model->getTable(), 'user_id');
        return static::$hasUserIdColumn;
    }
}

<?php

namespace App\Models\Concerns;

use App\Models\Property;
use App\Models\Scopes\StaffCurrentPropertyScope;
use App\Support\PropertyContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

trait BelongsToStaffCurrentProperty
{
    public static function bootBelongsToStaffCurrentProperty(): void
    {
        $instance = new static;

        $table = $instance->getTable();

        if (! Schema::hasTable($table)) {
            return;
        }

        $column = defined('static::PROPERTY_COLUMN') ? static::PROPERTY_COLUMN : 'branch_id';

        if (! Schema::hasColumn($table, $column)) {
            return;
        }

        static::addGlobalScope(new StaffCurrentPropertyScope);

        static::creating(function (Model $model) use ($column): void {
            if (! empty($model->{$column})) {
                return;
            }

            $property = app(PropertyContext::class)->property();

            if ($property) {
                $model->{$column} = $property->branch_id;
            }
        });
    }

    public function property()
    {
        $column = defined('static::PROPERTY_COLUMN') ? static::PROPERTY_COLUMN : 'branch_id';

        return $this->belongsTo(Property::class, $column, 'branch_id');
    }

    public function scopeForStaffProperty(Builder $query, int|Property $property): Builder
    {
        $column = defined('static::PROPERTY_COLUMN') ? static::PROPERTY_COLUMN : 'branch_id';
        $branchId = $property instanceof Property ? $property->branch_id : $property;

        return $query->withoutGlobalScope(StaffCurrentPropertyScope::class)
            ->where($this->qualifyColumn($column), $branchId);
    }
}

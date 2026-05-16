<?php

namespace App\Models\Concerns;

use App\Models\Branch;
use App\Models\Property;
use App\Models\Scopes\CurrentPropertyScope;
use App\Support\PropertyContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

trait BelongsToCurrentProperty
{
    public static function bootBelongsToCurrentProperty(): void
    {
        $instance = new static;

        $table = $instance->getTable();

        if (! Schema::hasTable($table)) {
            return;
        }

        $column = static::getPropertyColumn();

        if (! Schema::hasColumn($table, $column)) {
            return;
        }

        static::addGlobalScope(new CurrentPropertyScope);

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

    public static function getPropertyColumn(): string
    {
        return defined('static::PROPERTY_COLUMN') ? static::PROPERTY_COLUMN : 'branch_id';
    }

    public function property()
    {
        return $this->belongsTo(Property::class, static::getPropertyColumn(), 'branch_id');
    }

    public function scopeForProperty(Builder $query, int|Property $property): Builder
    {
        $column = static::getPropertyColumn();
        $branchId = $property instanceof Property ? $property->branch_id : $property;

        return $query->withoutGlobalScope(CurrentPropertyScope::class)
            ->where($this->qualifyColumn($column), $branchId);
    }
}

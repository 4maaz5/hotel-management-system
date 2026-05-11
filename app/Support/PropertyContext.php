<?php

namespace App\Support;

use App\Models\Property;

class PropertyContext
{
    protected ?Property $property = null;

    protected ?int $propertyId = null;

    public function setProperty(?Property $property): void
    {
        $this->property = $property;
        $this->propertyId = $property?->getKey();
    }

    public function setPropertyId(?int $propertyId): void
    {
        $this->property = null;
        $this->propertyId = $propertyId;
    }

    public function property(): ?Property
    {
        if ($this->property) {
            return $this->property;
        }

        if (! $this->propertyId) {
            return null;
        }

        return $this->property = Property::find($this->propertyId);
    }

    public function id(): ?int
    {
        return $this->propertyId;
    }

    public function branchId(): ?int
    {
        $property = $this->property();

        return $property?->branch_id;
    }

    public function forget(): void
    {
        $this->property = null;
        $this->propertyId = null;
    }
}

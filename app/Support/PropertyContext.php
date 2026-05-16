<?php

namespace App\Support;

use App\Models\Property;

class PropertyContext
{
    protected ?Property $property = null;

    protected ?int $propertyId = null;

    protected ?int $branchId = null;

    public function setProperty(?Property $property): void
    {
        $this->property = $property;
        $this->propertyId = $property?->getKey();
        $this->branchId = $property?->branch_id;
    }

    public function setPropertyId(?int $propertyId): void
    {
        $this->property = null;
        $this->propertyId = $propertyId;
        $this->branchId = null;
    }

    public function setBranchId(?int $branchId): void
    {
        $this->property = null;
        $this->propertyId = null;
        $this->branchId = $branchId;
    }

    public function property(): ?Property
    {
        if ($this->property) {
            return $this->property;
        }

        if (! $this->propertyId) {
            if ($this->branchId) {
                return $this->property = Property::where('branch_id', $this->branchId)->first();
            }

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
        if ($this->branchId) {
            return $this->branchId;
        }

        $property = $this->property();

        return $property?->branch_id;
    }

    public function forget(): void
    {
        $this->property = null;
        $this->propertyId = null;
        $this->branchId = null;
    }
}

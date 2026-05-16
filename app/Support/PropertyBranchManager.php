<?php

namespace App\Support;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertyBranchManager
{
    public function ensureBranch(Property $property, ?User $owner = null): Property
    {
        if ($property->branch_id && Branch::whereKey($property->branch_id)->exists()) {
            return $property;
        }

        return DB::transaction(function () use ($property, $owner): Property {
            $lockedProperty = Property::withoutGlobalScopes()
                ->lockForUpdate()
                ->find($property->id) ?? $property;

            if ($lockedProperty->branch_id && Branch::whereKey($lockedProperty->branch_id)->exists()) {
                return $lockedProperty->refresh();
            }

            $tenantId = (int) ($lockedProperty->company_id ?: $owner?->company_id);

            if (! $tenantId) {
                return $lockedProperty;
            }

            $brand = Brand::firstOrCreate([
                'company_id' => $tenantId,
                'name' => 'Default Brand',
            ]);

            $branch = Branch::create([
                'company_id' => $tenantId,
                'brand_id' => $brand->id,
                'name' => $this->branchName($lockedProperty),
                'location' => $this->branchLocation($lockedProperty),
                'manager' => $owner?->name ?: 'Property Manager',
                'email' => $this->uniqueBranchEmail($lockedProperty),
                'phone' => $lockedProperty->phone ?: $lockedProperty->mobile,
                'status' => 'Active',
            ]);

            $lockedProperty->forceFill(['branch_id' => $branch->id])->save();

            if ($owner) {
                $lockedProperty->users()->syncWithoutDetaching([$owner->id]);

                if (! $owner->branch_id) {
                    $owner->forceFill(['branch_id' => $branch->id])->save();
                }
            }

            return $lockedProperty->refresh();
        });
    }

    private function branchName(Property $property): string
    {
        return Str::limit($property->property_name_en ?: 'Property Branch', 250, '');
    }

    private function branchLocation(Property $property): string
    {
        return Str::limit($property->address_en ?: $property->property_name_en ?: 'Property Location', 250, '');
    }

    private function uniqueBranchEmail(Property $property): string
    {
        $email = $property->email ?: "property-{$property->id}@branch.local";

        if (! Branch::where('email', $email)->exists()) {
            return $email;
        }

        $fallback = "property-{$property->id}@branch.local";

        if (! Branch::where('email', $fallback)->exists()) {
            return $fallback;
        }

        do {
            $candidate = 'property-'.$property->id.'-'.Str::lower(Str::random(6)).'@branch.local';
        } while (Branch::where('email', $candidate)->exists());

        return $candidate;
    }
}

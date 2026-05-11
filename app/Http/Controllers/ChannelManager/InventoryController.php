<?php

namespace App\Http\Controllers\ChannelManager;

use App\Http\Controllers\Controller;
use App\Models\RatePlan;
use App\Models\Unit;
use App\Models\UnitTypeCustomization;

class InventoryController extends Controller
{
    public function index()
    {
        $products = UnitTypeCustomization::query()
            ->orderBy('website_sort_order')
            ->orderBy('name')
            ->get()
            ->map(function (UnitTypeCustomization $product) {
                return [
                    'product' => $product,
                    'active_units' => Unit::query()
                        ->where('unit_type_id', $product->unit_type_id)
                        ->where('is_active', true)
                        ->count(),
                ];
            });

        return view('admin.product_inventory.index', [
            'products' => $products,
            'activeUnitsCount' => Unit::query()->where('is_active', true)->count(),
            'publishedProductsCount' => UnitTypeCustomization::query()->where('is_published_online', true)->count(),
            'ratePlanCount' => RatePlan::query()->count(),
        ]);
    }
}

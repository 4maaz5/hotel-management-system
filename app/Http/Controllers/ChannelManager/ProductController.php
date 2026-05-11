<?php

namespace App\Http\Controllers\ChannelManager;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\UnitTypeCustomization;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = UnitTypeCustomization::query()
            ->with('unitType')
            ->withCount('images')
            ->orderBy('website_sort_order')
            ->orderBy('name')
            ->get()
            ->map(function (UnitTypeCustomization $product) {
                $assignedUnits = Unit::query()
                    ->where('unit_type_id', $product->unit_type_id)
                    ->count();
                $previewUnit = Unit::query()
                    ->where('unit_type_id', $product->unit_type_id)
                    ->where('is_active', true)
                    ->orderBy('unit_number')
                    ->orderBy('id')
                    ->first();

                $websiteName = $product->website_name_en ?: $product->name;
                $seoReady = filled($product->seo_title_en)
                    && filled($product->seo_description_en)
                    && filled($product->website_slug);
                $previewSlug = $previewUnit
                    ? $previewUnit->id.'-'.Str::slug(trim(($product->website_slug ?: $product->name ?: $previewUnit->unitType?->name ?: 'unit').' '.($previewUnit->unit_number ?: $previewUnit->id)))
                    : null;

                return compact('product', 'assignedUnits', 'websiteName', 'seoReady', 'previewSlug');
            });

        return view('admin.channel_product.index', compact('products'));
    }
}

<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\Corporate;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CorporateController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->visibleCorporates($request);
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        $corporates = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.corporates.index', compact('corporates'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'postal_code' => 'nullable|string|max:20',
            'vat_registration_number' => 'nullable|string|max:50',
            'commercial_registration_number' => 'nullable|string|max:50',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'country' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'street' => 'nullable|string|max:100',
            'building_number' => 'nullable|string|max:20',
            'secondary_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:20',
            'contact_person_name' => 'nullable|string|max:150',
            'contact_person_dial_code' => 'nullable|string|max:10',
            'contact_person_phone' => 'nullable|string|max:20',
        ]);

        $corporate = Corporate::create($validated);

        return response()->json([
            'success' => true,
            'message' => __('messages.corporate_created_successfully'),
            'corporate' => [
                'id' => $corporate->id,
                'name' => $corporate->name,
            ]
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        $corporates = $this->visibleCorporates($request)
            ->where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'discount_type', 'discount_value']);

        return response()->json($corporates);
    }

    public function show(Request $request, Corporate $corporate)
    {
        $corporate = $this->visibleCorporates($request)->findOrFail($corporate->id);

        return response()->json($corporate);
    }

    public function update(Request $request, Corporate $corporate): JsonResponse
    {
        $corporate = $this->visibleCorporates($request)->findOrFail($corporate->id);

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'postal_code' => 'nullable|string|max:20',
            'vat_registration_number' => 'nullable|string|max:50',
            'commercial_registration_number' => 'nullable|string|max:50',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'country' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'street' => 'nullable|string|max:100',
            'building_number' => 'nullable|string|max:20',
            'secondary_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:20',
            'contact_person_name' => 'nullable|string|max:150',
            'contact_person_dial_code' => 'nullable|string|max:10',
            'contact_person_phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $corporate->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('messages.corporate_updated_successfully'),
        ]);
    }

    public function destroy(Request $request, Corporate $corporate): JsonResponse
    {
        $corporate = $this->visibleCorporates($request)->findOrFail($corporate->id);

        $corporate->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.corporate_deleted_successfully'),
        ]);
    }

    protected function visibleCorporates(Request $request): Builder
    {
        return Corporate::query();
    }
}

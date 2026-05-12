<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Company;
use App\Models\CompanyDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        // Initialize variables
        $branches = collect();
        $branchesCards = collect();
        $brands = collect();
        $companies = collect();

        if ($user->hasRole('super_admin')) {
            $branches = Branch::with('documents')->get();
            $brands = Brand::all();
            $companies = Company::all();
            $branchesCards = Branch::paginate(10);
        } elseif ($user->branch_id) {
            $branches = Branch::where('id', $user->branch_id)->get();
            $branchesCards = Branch::where('id', $user->branch_id)->paginate(10);
            $brands = Brand::whereHas('branches', function ($q) use ($user) {
                $q->where('id', $user->branch_id);
            })->get();
            $companies = Company::whereHas('branches', function ($q) use ($user) {
                $q->where('id', $user->branch_id);
            })->get();
        } else {
            $branches = Branch::with('documents')->get();
            $branchesCards = Branch::paginate(10);
            $brands = Brand::all();
            $companies = $user->company_id
                ? Company::whereKey($user->company_id)->get()
                : collect();
        }

        return view('Admin.Backend.Branch.index', compact(
            'branches',
            'branchesCards',
            'brands',
            'companies'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            // existing branch rules ...
            'branch_name' => 'required|string|max:255|unique:branches,name',
            'brand_id' => 'required',
            'company_id' => 'required',
            'branch_address' => 'required|string|max:255',
            'branch_manager' => 'required|string|max:255',
            'branch_email' => 'required|email|unique:branches,email',
            'branch_phone' => 'required|string|max:20|unique:branches,phone',
            'branch_status' => 'required|in:Active,Inactive',

            // extra branch fields
            'building_type' => 'required|in:owned,rented',

            // documents - UPDATED FIELD NAMES
            'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',

            // Document names
            'doc_name' => 'nullable|array',
            'doc_name.*' => 'nullable|string|max:255',

            // Date type
            'date_type' => 'nullable|array',
            'date_type.*' => 'nullable|in:hijri,gregorian',

            // Hijri date fields
            'start_date_hijri' => 'nullable|array',
            'start_date_hijri.*' => 'nullable|string|regex:/^\d{4}-\d{2}-\d{2}$/',
            'end_date_hijri' => 'nullable|array',
            'end_date_hijri.*' => 'nullable|string|regex:/^\d{4}-\d{2}-\d{2}$/',

            // Gregorian date fields
            'start_date_gregorian' => 'nullable|array',
            'start_date_gregorian.*' => 'nullable|date',
            'end_date_gregorian' => 'nullable|array',
            'end_date_gregorian.*' => 'nullable|date',
        ]);

        // Create branch
        $branch = new Branch;
        $branch->name = $validated['branch_name'];
        $branch->brand_id = $validated['brand_id'];
        $branch->company_id = $validated['company_id'];
        $branch->location = $validated['branch_address'];
        $branch->manager = $validated['branch_manager'];
        $branch->email = $validated['branch_email'];
        $branch->phone = $validated['branch_phone'];
        $branch->status = $validated['branch_status'];
        $branch->market_price = $request->market_price;
        $branch->total_rent = $request->total_rent;
        $branch->sale_price = $request->sale_price;
        $branch->rent_start_date = $request->rent_start_date;
        $branch->rent_end_date = $request->rent_end_date;
        $branch->damage_assist = $request->damage_assist;

        // Extra fields
        $branch->building_type = $validated['building_type'];
        $branch->total_rent = $request->total_rent;
        $branch->installments = $request->installments;

        // Handle file upload
        if ($request->hasFile('rent_agreement')) {
            $file = $request->file('rent_agreement');
            $path = $file->store('branch_rent_agreements', 'public');
            $branch->rent_agreement = $path;
        }

        $branch->save();

        // Handle multiple documents
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $doc_names = $request->input('doc_name', []);
            $date_types = $request->input('date_type', []);
            $start_dates_hijri = $request->input('start_date_hijri', []);
            $end_dates_hijri = $request->input('end_date_hijri', []);
            $start_dates_gregorian = $request->input('start_date_gregorian', []);
            $end_dates_gregorian = $request->input('end_date_gregorian', []);

            foreach ($files as $index => $file) {
                $path = $file->store('company_documents', 'public');

                // Get document name
                $docName = $doc_names[$index] ?? null;

                // Get date type for this document
                $dateType = $date_types[$index] ?? 'hijri';

                // Get dates based on date type
                $issueDate = null;
                $expirationDate = null;

                if ($dateType === 'hijri') {
                    // Use Hijri dates
                    if (isset($start_dates_hijri[$index]) && trim($start_dates_hijri[$index]) !== '') {
                        $issueDate = trim($start_dates_hijri[$index]);
                    }

                    if (isset($end_dates_hijri[$index]) && trim($end_dates_hijri[$index]) !== '') {
                        $expirationDate = trim($end_dates_hijri[$index]);
                    }
                } else {
                    // Use Gregorian dates
                    if (isset($start_dates_gregorian[$index]) && trim($start_dates_gregorian[$index]) !== '') {
                        $issueDate = trim($start_dates_gregorian[$index]);
                        // Ensure proper format for Gregorian dates
                        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $issueDate)) {
                            $issueDate = date('Y-m-d', strtotime($issueDate));
                        }
                    }

                    if (isset($end_dates_gregorian[$index]) && trim($end_dates_gregorian[$index]) !== '') {
                        $expirationDate = trim($end_dates_gregorian[$index]);
                        // Ensure proper format for Gregorian dates
                        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $expirationDate)) {
                            $expirationDate = date('Y-m-d', strtotime($expirationDate));
                        }
                    }
                }

                // Optional: doc_numbers if you have them
                $doc_numbers = $request->input('doc_number', []);

                CompanyDocument::create([
                    'name' => $docName,
                    'branch_id' => $branch->id,
                    'company_id' => $branch->company_id,
                    'type' => 'legal',
                    'issued_by' => $doc_numbers[$index] ?? null,
                    'file_path' => $path,
                    'issue_date' => $issueDate,
                    'expiration_date' => $expirationDate,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => __('messages.branch_created_successfully'),
            'data' => $branch,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // dd($request->all());
        try {
            //  Validation - UPDATED FIELD NAMES
            $validated = $request->validate([
                'edit_branch_name' => 'required|string|max:255',
                'branch_location' => 'required|string|max:255',
                'branch_manager' => 'required|string|max:255',
                'branch_email' => 'required|email|unique:branches,email,'.$request->branchId,
                'branch_phone' => 'required|string|max:20|unique:branches,phone,'.$request->branchId,
                'branch_status' => 'required|in:Active,Inactive',

                // pricing
                'market_price' => 'nullable|numeric|min:0',
                'rent' => 'nullable|numeric|min:0',
                'sale_price' => 'nullable|numeric|min:0',
                'damage_assist' => 'nullable|numeric|min:0',

                // documents - UPDATED TO MATCH YOUR HTML
                'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
                'doc_name' => 'nullable|array',
                'doc_name.*' => 'nullable|string|max:255',

                // Date type field
                'date_type' => 'nullable|array',
                'date_type.*' => 'nullable|in:hijri,gregorian',

                // Hijri date fields
                'start_date_hijri' => 'nullable|array',
                'start_date_hijri.*' => 'nullable|string|regex:/^\d{4}-\d{2}-\d{2}$/',
                'end_date_hijri' => 'nullable|array',
                'end_date_hijri.*' => 'nullable|string|regex:/^\d{4}-\d{2}-\d{2}$/',

                // Gregorian date fields
                'start_date_gregorian' => 'nullable|array',
                'start_date_gregorian.*' => 'nullable|date',
                'end_date_gregorian' => 'nullable|array',
                'end_date_gregorian.*' => 'nullable|date',
            ]);

            //  Find the branch
            $branch = Branch::findOrFail($request->branchId);

            //  Update all fields
            $branch->update([
                'name' => $validated['edit_branch_name'],
                'location' => $validated['branch_location'],
                'manager' => $validated['branch_manager'],
                'email' => $validated['branch_email'],
                'phone' => $validated['branch_phone'],
                'status' => $validated['branch_status'],
                'market_price' => $request->market_price,
                'total_rent' => $request->total_rent,
                'installments' => $request->installments,
                'sale_price' => $request->sale_price,
                'rent_start_date' => $request->rent_start_date,
                'rent_end_date' => $request->rent_end_date,
                'damage_assist' => $request->damage_assist,
            ]);
            if ($request->hasFile('rent_agreement')) {
                $file = $request->file('rent_agreement');
                $path = $file->store('branch_rent_agreements', 'public');
                $branch->rent_agreement = $path;
            }
            $branch->save();

            // Handle new document uploads
            if ($request->hasFile('files')) {
                // Get arrays of data - UPDATED FIELD NAMES
                $files = $request->file('files');
                $doc_names = $request->input('doc_name', []);
                $date_types = $request->input('date_type', []);
                $start_dates_hijri = $request->input('start_date_hijri', []);
                $end_dates_hijri = $request->input('end_date_hijri', []);
                $start_dates_gregorian = $request->input('start_date_gregorian', []);
                $end_dates_gregorian = $request->input('end_date_gregorian', []);

                //  Save new documents
                foreach ($files as $index => $file) {
                    $path = $file->store('company_documents', 'public');

                    // Get document name
                    $docName = $doc_names[$index] ?? null;

                    // Get date type for this document
                    $dateType = $date_types[$index] ?? 'hijri';

                    // Get dates based on date type
                    $issueDate = null;
                    $expirationDate = null;

                    if ($dateType === 'hijri') {
                        // Use Hijri dates
                        if (isset($start_dates_hijri[$index]) && trim($start_dates_hijri[$index]) !== '') {
                            $issueDate = trim($start_dates_hijri[$index]);
                        }

                        if (isset($end_dates_hijri[$index]) && trim($end_dates_hijri[$index]) !== '') {
                            $expirationDate = trim($end_dates_hijri[$index]);
                        }
                    } else {
                        // Use Gregorian dates
                        if (isset($start_dates_gregorian[$index]) && trim($start_dates_gregorian[$index]) !== '') {
                            $issueDate = trim($start_dates_gregorian[$index]);
                            // Ensure proper format for Gregorian dates
                            if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $issueDate)) {
                                $issueDate = date('Y-m-d', strtotime($issueDate));
                            }
                        }

                        if (isset($end_dates_gregorian[$index]) && trim($end_dates_gregorian[$index]) !== '') {
                            $expirationDate = trim($end_dates_gregorian[$index]);
                            // Ensure proper format for Gregorian dates
                            if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $expirationDate)) {
                                $expirationDate = date('Y-m-d', strtotime($expirationDate));
                            }
                        }
                    }

                    CompanyDocument::create([
                        'name' => $docName,
                        'branch_id' => $branch->id,
                        'company_id' => $branch->company_id,
                        'type' => 'legal',
                        'issued_by' => null,
                        'file_path' => $path,
                        'issue_date' => $issueDate,
                        'expiration_date' => $expirationDate,
                        'date_type' => $dateType,
                    ]);
                }
            }

            //  Response
            return response()->json([
                'success' => true,
                'message' => __('messages.branch_updated_successfully'),
                'data' => $branch,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.something_went_wrong').$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $branch = Branch::find($request->branchId);

        if (! $branch) {
            return response()->json([
                'success' => false,
                'message' => 'Branch not found',
            ], 404);
        }

        //  Delete rent agreement file if exists
        if ($branch->rent_agreement && Storage::disk('public')->exists($branch->rent_agreement)) {
            Storage::disk('public')->delete($branch->rent_agreement);
        }

        //  Delete all related company documents
        $documents = CompanyDocument::where('branch_id', $branch->id)->get();
        foreach ($documents as $doc) {
            if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
            $doc->delete();
        }

        //  Delete branch record
        $branch->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.branch_deleted_successfully'),
        ]);
    }

    public function filter(Request $request)
    {
        $query = Branch::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%'.$request->location.'%');
        }

        if ($request->filled('manager')) {
            $query->where('manager', 'like', '%'.$request->manager.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $branches = $query->latest()->get();

        // AJAX request → return rendered rows
        if ($request->ajax()) {
            return view('Admin.Backend.partials.branches_rows', compact('branches'))->render();
        }

        return view('Admin.Backend.branches.index', compact('branches'));
    }

    public function getDocuments($id)
    {
        try {
            $branch = Branch::with('documents')->findOrFail($id);

            return response()->json([
                'success' => true,
                'documents' => $branch->documents,
                'branch' => [
                    'rent_agreement' => $branch->rent_agreement,
                    'rent_start_date' => $branch->rent_start_date,
                    'rent_end_date' => $branch->rent_end_date,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading documents',
            ], 500);
        }
    }
}

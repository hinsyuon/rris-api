<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\Tenant\TenantDetailsResource;

class TenantController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tenants",
     *     tags={"Tenants"},
     *     summary="Get list of tenants (User / Admin)",
     *     description="Retrieve a paginated list of tenants with optional sorting and filtering.",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term to filter tenants by [first name, last name, email, phone number, or address]",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_col",
     *         in="query",
     *         description="Column to sort by [id, first_name, last_name]",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_dir",
     *         in="query",
     *         description="Sort direction (asc or desc)",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="filter_col",
     *         in="query",
     *         description="Column to filter by [gender, joined_at]",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="filter_val",
     *         in="query",
     *         description="Value to filter the specified column by",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page for pagination",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=15
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get list of tenants successfully",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *     ),
     *    @OA\Response(
     *        response=422,
     *        description="Validation Error",
     *     )
     * )    
     */
    public function index(Request $req)
    {
        try{
            $allowSortCol = ['id', 'first_name', 'last_name'];
            $allowFilterCol = ['gender', 'joined_at'];
            $inputs = $this->index_req_validate($req, $allowSortCol, $allowFilterCol);
            if (!$inputs['result']) {
                return $this->res_fail('Query Parameters failed validation' . $inputs['message'], [], 1, 400);
            }

            $req->validate([
                'page' => 'nullable|integer|min:1|max:1000000',
                'per_page' => 'nullable|integer|min:1|max:100'

            ]);

            // Set default data
            $sort_col = $inputs['sort_col'] ?? 'id';
            $sort_dir = $inputs['sort_dir'] ?? 'desc';
            $page = $req->filled('page') ? intval($req->input('page')) : 1;
            $per_page =  $req->filled('per_page') ? intval($req->input('per_page')) : 15;

            $tenants = new Tenant();

            // Set Search
            if ($inputs['is_search']) {
                $search = $inputs['search'];
                $tenants = $tenants->where(function ($q) use ($search) {
                    $q->where('id',  $search)
                    ->orWhere('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone_number', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%');
                });
            }

            // Set Filter
            if ($inputs['is_filter']) {

                $tenants = $tenants->where($inputs['filter_col'], $inputs['filter_val']);
            }
            // Set range filter for price_per_month

            $tenants = $tenants->orderBy($sort_col, $sort_dir)->paginate($per_page, ['*'], 'page', $page);
            return $this->res_paginate($tenants, 'Get all tenants successfully', TenantDetailsResource::collection($tenants));
       } catch (ValidationException $e) {
            return $this->return_error($e, 422);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/tenants/{id}",
     *     tags={"Tenants"},
     *     summary="Get tenant detail by ID (User / Admin)",
     *     description="Retrieve detailed information about a specific tenant by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the tenant to retrieve",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get a tenant detail successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tenant not found",
     *     ),
     *    @OA\Response(
     *        response=422,
     *        description="Validation Error",
     * 
     *     )
     * )
     */
    public function find(Request $req, $id)
    {
        try {
            // Merge ID into request for validation
            $req->merge(['id' => $id]);
            // Validate ID
            $req->validate([
                'id' => 'integer|exists:tenants,id'
            ]);
            $tenant = Tenant::where('id', $id)->first();
            if (!$tenant) {
                return $this->res_fail('Tenant not found', [], 1, 404);
            }
            return $this->res_success('Get a tenant detail successfully', new TenantDetailsResource($tenant));
        } catch (ValidationException $e) {
            return $this->return_error($e, 422);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/tenants",
     *     tags={"Tenants"},
     *     summary="Create a new tenant (Admin only)",
     *     description="Create a new tenant with the provided information.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","last_name","gender","email","phone_number", "room_id"},
     *             @OA\Property(property="first_name", type="string", maxLength=250, example="John"),
     *             @OA\Property(property="last_name", type="string", maxLength=250, example="Doe"),
     *             @OA\Property(property="gender", type="integer", example=1, description="1: Male, 2: Female, 3: Other"),    
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="phone_number", type="string", maxLength=20, example="+1234567890"),
     *             @OA\Property(property="address", type="string", maxLength=500, example="123 Main St, City, Country"),
     *             @OA\Property(property="joined_at", type="string", format="date", example="2023-01-01"),
     *             @OA\Property(property="room_id", type="integer", example=1),
     *             @OA\Property(property="amount_paid", type="number", format="float", example=500.00),
     *             @OA\Property(property="payment_date", type="string", format="date", example="2023-01-01"),
     *             @OA\Property(property="payment_status", type="integer", example=1, description="0: Pending, 1: Paid, 2: Late"),
     *         )
     *     ),
     *    @OA\Response( 
     *        response=201,
     *        description="Tenant created successfully",
     *    ),
     *    @OA\Response(
     *        response=422,
     *        description="Validation Error",
     *    )
     * )        
     */
    public function store(Request $req)
    {
        try {
            // Validate input data
            $req->validate([
                'first_name' => 'required|string|max:250',
                'last_name' => 'required|string|max:250',
                'gender' => 'required|in:' . implode(',', [Tenant::MALE, Tenant::FEMALE, Tenant::OTHER]), // 1: Male, 2: Female, 3: Other
                'email' => 'required|email|unique:tenants,email',
                'phone_number' => 'required|string|max:20',
                'address' => 'nullable|string|max:500',
                'joined_at' => 'nullable|string|date_format:Y-m-d',
                'amount_paid' => 'nullable|numeric|min:0',
                'payment_date' => 'nullable|string|date_format:Y-m-d',
                'payment_status' => 'nullable|in:'. implode(',', [Tenant::PENDING, Tenant::PAID, Tenant::LATE]), // 0: Pending, 1: Paid, 2: Late
                'room_id' => 'nullable|integer|exists:rooms,id',
            ]);

            // Create new tenant
            $tenant = new Tenant(
              $req->only([
                  'first_name',
                  'last_name',
                  'gender',
                  'email',
                  'phone_number',
                  'address',
                  'joined_at',
              ]));
            $tenant->save();

            // If payment details are provided, create a rent payment record
            if ($req->filled(['amount_paid', 'payment_date', 'payment_status', 'room_id'])) {
                $tenant->rooms()->attach($req->input('room_id'), [
                    'amount_paid' => $req->input('amount_paid'),
                    'payment_date' => $req->input('payment_date'),
                    'payment_status' => $req->input('payment_status'),
                ]);
            }

            return $this->res_success('Tenant created successfully');
        } catch (ValidationException $e) {
            return $this->return_error($e, 422);
        }
    }
    /**
     * @OA\Put(
     *     path="/api/tenants/{id}",
     *     tags={"Tenants"},
     *     summary="Update tenant details by ID (Admin only)",
     *     description="Update the details of a specific tenant by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the tenant to update",
     *         required=true,
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="first_name", type="string", maxLength=250, example="John"),
     *             @OA\Property(property="last_name", type="string", maxLength=250, example="Doe"),
     *             @OA\Property(property="gender", type="integer", example=1, description="1: Male, 2: Female, 3: Other"),  
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="phone_number", type="string", maxLength=20, example="+1234567890"),
     *             @OA\Property(property="address", type="string", maxLength=500, example="123 Main St, City, Country"),
     *             @OA\Property(property="joined_at", type="string", format="date", example="2023-01-01"),
     *             @OA\Property(property="room_id", type="integer", example=1),
     *            @OA\Property(property="amount_paid", type="number", format="float", example=400.00),
     *            @OA\Property(property="payment_date", type="string", format="date", example="2023-01-01"),
     *            @OA\Property(property="payment_status", type="integer", example=1, description="0: Pending, 1: Paid, 2: Late"),
     *         )
     *     ),
     *    @OA\Response(
     *        response=200,
     *       description="Tenant updated successfully",
     *    ),
     *    @OA\Response(
     *       response=404,
     *      description="Tenant not found",
     *    ),
     *    @OA\Response(
     *       response=422,
     *      description="Validation Error",
     *    ),
     * )
     */
    public function update(Request $req, $id)
    {
        try {
            // Merge ID into request for validation
            $req->merge(['id' => $id]);
            // Validate input data
            $req->validate([
                'id' => 'integer|exists:tenants,id',
                'first_name' => 'nullable|string|max:250',
                'last_name' => 'nullable|string|max:250', 
                'gender' => 'nullable|in:' .implode(',', [Tenant::MALE, Tenant::FEMALE, Tenant::OTHER]), // 1: Male, 2: Female, 3: Other
                'email' => 'nullable|email|unique:tenants,email,'.$id,
                'phone_number' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'joined_at' => 'nullable|string|date_format:Y-m-d',
                'amount_paid' => 'nullable|numeric|min:0',
                'payment_date' => 'nullable|string|date_format:Y-m-d',
                'payment_status' => 'nullable|in:'.implode(',', [Tenant::PENDING, Tenant::PAID, Tenant::LATE]), // 0: Pending, 1: Paid, 2: Late
                'room_id' => 'nullable|integer|exists:rooms,id',
            ]);
            $tenant = Tenant::where('id', $id)->first();
            if (!$tenant) {
                return $this->res_fail('Tenant not found', [], 1, 404);
            }
            // Update tenant details
            if ($req->filled('first_name')) {
                $tenant->first_name = $req->first_name;
            }
            if ($req->filled('last_name')) {
                $tenant->last_name = $req->last_name;
            }
            if ($req->filled('gender')) {
                $tenant->gender = $req->gender;
            }
            if ($req->filled('email')) {
                $tenant->email = $req->email;
            }
            if ($req->filled('phone_number')) {
                $tenant->phone_number = $req->phone_number;
            }
            if ($req->filled('address')) {
                $tenant->address = $req->address;
            }
            if ($req->filled('joined_at')) {
                $tenant->joined_at = $req->joined_at;
            }

            if ($req->filled(['amount_paid', 'payment_date', 'payment_status', 'room_id'])) {
                // Update or create rent payment record
                $tenant->rooms()->syncWithoutDetaching([
                    $req->input('room_id') => [
                        'amount_paid' => $req->input('amount_paid'),
                        'payment_date' => $req->input('payment_date'),
                        'payment_status' => $req->input('payment_status'),
                    ]
                ]);
            }
            $tenant->save();

            return $this->res_success('Tenant updated successfully', new TenantDetailsResource($tenant));
        } catch (ValidationException $e) {
            return $this->return_error($e, 422);
        }
    }
    /**
     * @OA\Delete(
     *     path="/api/tenants/{id}",
     *     tags={"Tenants"},
     *     summary="Delete a tenant by ID (Admin only)",
     *     description="Delete a specific tenant by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the tenant to delete",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tenant deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tenant not found",
     *     ),
     *    @OA\Response(
     *        response=422,
     *        description="Validation Error",
     *     )
     * )
     */
    public function destroy(Request $req, $id)
    {
        try {
            // Merge ID into request for validation
            $req->merge(['id' => $id]);
            // Validate ID
            $req->validate([
                'id' => 'integer|exists:tenants,id'
            ]);
            $tenant = Tenant::where('id', $id)->first();
            if (!$tenant) {
                return $this->res_fail('Tenant not found', [], 1, 404);
            }
            $tenant->delete();
            return $this->res_success('Tenant deleted successfully');
        } catch (ValidationException $e) {
            return $this->return_error($e, 422);
        }
    }
    /**
     * @OA\Post(
     *    path="/api/tenants/bulk-delete",
     *   tags={"Tenants"},
     *   summary="Bulk delete tenants by IDs (Admin only)",
     *   description="Delete multiple tenants by providing an array of their IDs.",
     *   @OA\RequestBody(
     *       required=true,
     *      @OA\JsonContent(
     *          required={"ids"},
     *         @OA\Property(
     *            property="ids",
     *            type="array",
     *          @OA\Items(
     *               type="integer",
     *               example=1
     *          )
     *    )
     *    )
     *  ),
     *   @OA\Response(
     *       response=200,
     *      description="Tenants deleted successfully",
     *   ),
     *   @OA\Response(
     *      response=404,
     *     description="No Tenants found to delete",
     *   ),
     *  @OA\Response(
     *      response=422,
     *     description="Validation Error",
     *    )
     *  )
     */
    public function bulk_delete(Request $req)
    {
        try {
            $req->validate([
                'ids' => 'required|array|min:1',
                'ids.*' => 'integer|exists:tenants,id'
            ]);

            $ids = $req->input('ids');

            // Delete tenants in bulk
            $tenant = Tenant::whereIn('id', $ids)->delete();

            if (!$tenant) {
                return $this->res_fail('No Tenants found to delete', [], 1, 404);
            }

            return $this->res_success('Tenants deleted successfully');
        } catch (ValidationException $e) {
            return $this->return_error($e, 422);
        }
    }
}
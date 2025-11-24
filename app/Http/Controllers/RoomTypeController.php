<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomType\RoomTypeResource;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Models\RoomType;
use Illuminate\Validation\ValidationException;


class RoomTypeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/room-types",
     *     summary="Get all room types (User / Admin)",
     *     tags={"Room Types"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Search term to filter room types by ID, name, or description",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_col",
     *         in="query",
     *         required=false,
     *         description="Column to sort [id, name]",
     *         @OA\Schema(type="string", default="id")
     *     ),
     *     @OA\Parameter(
     *         name="sort_dir",
     *         in="query",
     *         required=false,
     *         description="Sort direction [asc or desc]",
     *         @OA\Schema(type="string", default="desc")
     *     ),
     *      @OA\Parameter(
     *        name="page",
     *        description="Page number for pagination",
     *       in="query",
     *       required=false,
     *       description="Current page number for pagination",
     *      @OA\Schema(type="integer", default=1)
     *  ),
     *    @OA\Parameter(
     *        name="per_page",
     *       in="query",
     *       required=false,
     *      description="Number of items per page for pagination",
     *      @OA\Schema(type="integer", default=15)
     *  ),
     *     @OA\Response(
     *         response=200,
     *         description="Get all room types successfully"
     *     ),
     *    @OA\Response(
     *        response=400,
     *       description="Query Parameters failed validation"
     *     )
     * )
     */
    public function index(Request $req)
    {
       try {
             $allowSortCol = ['id', 'name'];
            $inputs = $this->index_req_validate($req, $allowSortCol);
            if(!$inputs['result']) {
                return $this->res_fail('Query Parameters failed validation' . $inputs['message'], [], 1, 400);
            }
            // Validatation
            $req->validate([
                'page' => 'nullable|integer|min:1|max:1000000',
                'per_page' => 'nullable|integer|min:1|max:100'
            ]);

            // Set default
            $sort_col = $inputs['sort_col'] ?? 'id';
            $sort_dir = $inputs['sort_dir'] ?? 'desc';
            $page = $req->filled('page') ? intval($req->input('page')) : 1;
            $per_page =  $req->filled('per_page') ? intval($req->input('per_page')) : 15;

            $roomTypes = new RoomType();

            if($inputs['is_search']) {
                $search = $inputs['search'];
                $roomTypes = $roomTypes->where(function($q) use ($search) {
                        $q->where('id',  $search )
                        ->orWhere('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
                                    
            }

            $roomTypes = $roomTypes->orderBy($sort_col, $sort_dir)->paginate($per_page, ['*'], 'page', $page);
            return $this->res_paginate($roomTypes, 'Get all room types successfully', RoomTypeResource::collection($roomTypes));
        }catch (ValidationException $e) {
            return $this->return_error($e, 422);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/room-types/{id}",
     *     summary="Get a room type by ID (User / Admin)",
     *     tags={"Room Types"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the room type to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get room type successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room type not found"
     *     ),
     *    @OA\Response(
     *         response=422,
     *         description="Invalid input"
     *     )
     * )
     */
    public function find(Request $req, $id)
    {
        $roomType = RoomType::where('id', $id)->first();
        if (!$roomType) {
            return $this->res_fail('Room type not found', [], 1, 404);
        }

        return $this->res_success('Get room type successfully', new RoomTypeResource($roomType));
    }

    /**
     * @OA\Post(
     *     path="/api/room-types",
     *     summary="Create a new room type (Admin only)",
     *     tags={"Room Types"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Single"),
     *             @OA\Property(property="description", type="string", example="A room assigned to one person.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room type created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input"
     *     )
     * )
     */
    public function store(Request $req)
    {
        try {
            $req->validate([
                'name' => 'required|string|unique:room_types,name',
                'description' => 'nullable|string|max:65535',
            ]);
            $roomType = new RoomType($req->only(['name', 'description']));

            $roomType->save();

            return $this->res_success('Room type created successfully');
        } catch (ValidationException $e) {
            return $this->return_error($e, 422);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/room-types/{id}",
     *     summary="Update a room type (Admin only)",
     *     tags={"Room Types"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the room type to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Single"),
     *             @OA\Property(property="description", type="string", example="A room assigned to one person.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room type updated successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room type not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input"
     *     )
     * )
     */
    public function update(Request $req, $id)
    {
        try {
            // Merge ID into request for validation
            $req->merge(['id' => $id]);
            // Validation
            $req->validate([
                'id' => 'required|integer|exists:room_types,id',
                'name' => 'nullable|string|unique:room_types,name,' ,
                'description' => 'nullable|string|max:65535',
            ]);

            $roomType = RoomType::where('id', $id)->first();
            if (!$roomType) {            
                return $this->res_fail('Room type not found', [], 1, 404);
            }           
        
            if ($req->filled('name')) {
                $roomType->name = $req->name;
            }
            if ($req->filled('description')) {
                $roomType->description = $req->description;
            }
            $roomType->save();     

            return $this->res_success('Room type updated successfully');
        } catch (ValidationException $e) {
            return $this->return_error($e, 422);
        }
    }   
    /**
     * @OA\Delete(
     *     path="/api/room-types/{id}",
     *     summary="Delete a room type",
     *     tags={"Room Types"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the room type to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room type deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room type not found"
     *     ),
     *    @OA\Response(
     *         response=422,
     *         description="Invalid input"
     *     )
     * )
     */
    public function destroy(Request $req, $id)
    {
        try {
            $roomType = RoomType::where('id', $id)->first();
            if (!$roomType) {
                return $this->res_fail('Room type not found', [], 1, 404);
            }       
            $roomType->delete();
            return $this->res_success('Room type deleted successfully');
        } catch (ValidationException $e) {
            return $this->return_error($e, 422);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/room-types/bulk-delete",
     *     summary="Bulk delete room types (Admin only)",
     *     tags={"Room Types"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ids"},
     *             @OA\Property(
     *                 property="ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 description="Array of room type IDs to delete"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted Multiple Room types successfully"
     *     ),
     *    @OA\Response(
     *        response=404,
     *        description="No Room types found to delete"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input"
     *     )
     * )
     */
    public function bulk_delete(Request $req)
    {
        try {
            // merge ids array if not present
            $req->merge(['ids' => $req->input('ids')]);
            // Validation 
            $req->validate([
                'ids' => 'required|array|min:1',
                'ids.*' => 'integer|distinct|exists:room_types,id',
            ]);

            $ids = $req->input('ids');
           $roomType = RoomType::whereIn('id', $ids)->delete();
           if (!$roomType) {
                return $this->res_fail('No Room types found to delete', [], 1, 404);
            }

            return $this->res_success('Deleted Multiple Room types successfully');
        } catch (ValidationException $e) {
            return $this->return_error($e, 422);
        }
    }
}   

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Http\Resources\Room\RoomDetailsResource;
use Illuminate\Validation\ValidationException;

class RoomController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/rooms",
     *     tags={"Rooms"},
     *     summary="Get list of rooms (User / Admin)",
     *     description="Retrieve a paginated list of rooms with optional sorting and filtering.",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term to filter rooms by ID or room number.",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filter_col",
     *         in="query",
     *         description="Column to filter by [room_type_id, status, price_per_month].",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filter_val",
     *         in="query",
     *         description="Value to filter the specified column by [room_type_id: 1 for Single, 2 for Double, 3 for Suite ], [status: 1 for Available, 2 for Booked]",
     *         required=false,
     *         @OA\Schema(type="string", )
     *     ),
     *     @OA\Parameter(
     *         name="price_min",
     *         in="query",
     *         description="Minimum price per month for filtering.",
     *         required=false,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="price_max",
     *         in="query",
     *         description="Maximum price per month for filtering.",
     *         required=false,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="sort_col",
     *         in="query",
     *         description="Column to sort by [id, room_number]",
     *         required=false,
     *         @OA\Schema(type="string", default="id")
     *     ),
     *     @OA\Parameter(
     *         name="sort_dir",
     *         in="query",
     *         description="Sort direction [asc or desc]",
     *         required=false,
     *         @OA\Schema(type="string", default="desc")
     *     ),
     *    @OA\Parameter(
     *    name="page",
     *    in="query",
     *    description="Current page number for pagination.",
     *    required=false,
     *    @OA\Schema(type="integer", default=1)
     *    ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page for pagination.",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get all rooms successfully",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - Validation errors",
     *     ),
     *    @OA\Response(
     *         response=422,
     *         description="Invalid input"
     *     )
     * )        
     */
    public function index(Request $req)
    {
       try{
            $allowSortCol = ['id', 'room_number'];
            $allowFilterCol = ['room_type_id', 'status', 'price_per_month'];
            $inputs = $this->index_req_validate($req, $allowSortCol, $allowFilterCol);
            if (!$inputs['result']) {
                return $this->res_fail('Query Parameters failed validation' . $inputs['message'], [], 1, 400);
            }

            $req->validate([
                'price_min' => 'nullable|numeric|min:0|lte:price_max|max:99999999.99',
                'price_max' => 'nullable|numeric|min:0|gte:price_min|max:99999999.99',
                'page' => 'nullable|integer|min:1|max:1000000',
                'per_page' => 'nullable|integer|min:1|max:100'

            ]);

            // Set default data
            $sort_col = $inputs['sort_col'] ?? 'id';
            $sort_dir = $inputs['sort_dir'] ?? 'desc';
            $price_min = $req->filled('price_min') ? floatval($req->input('price_min')) : -1;
            $price_max = $req->filled('price_max') ? floatval($req->input('price_max')) : -1;
            $page = $req->filled('page') ? intval($req->input('page')) : 1;
            $per_page =  $req->filled('per_page') ? intval($req->input('per_page')) : 15;

            $rooms = new Room();

            // Set Search
            if ($inputs['is_search']) {
                $search = $inputs['search'];
                $rooms = $rooms->where(function ($q) use ($search) {
                    $q->where('id',  $search)
                    ->orWhere('room_number', 'like', '%' . $search . '%');
                });
            }

            // Set Filter
            if ($inputs['is_filter']) {

                $rooms = $rooms->where($inputs['filter_col'], $inputs['filter_val']);
            }
            // Set range filter for price_per_month

            if ($price_min > -1) {
                $rooms = $rooms->where('price_per_month', '>=', floatval($price_min));
            }
            if ($price_max > -1) {
                $rooms = $rooms->where('price_per_month', '<=', floatval($price_max));
            }

            $rooms = $rooms->orderBy($sort_col, $sort_dir)->paginate($per_page, ['*'], 'page', $page);
            return $this->res_paginate($rooms, 'Get all rooms successfully', RoomDetailsResource::collection($rooms));
       } catch (ValidationException $e) {
            return $this->return_error($e, 422);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/rooms/{id}",
     *     tags={"Rooms"},
     *     summary="Get room details by ID (User / Admin)",
     *     description="Retrieve detailed information about a specific room by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the room to retrieve.",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get a room detail successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found",
     *     ),
     *    @OA\Response(
     *        response=422,
     *       description="Validation error",
     *     )
     * )        
     */
    public function find(Request $req, $id)
    {
        try {
            // Merge ID into request for validation
            $req->merge(['id' => $id]);
            // Validation rules
            $req->validate([
                'id' => 'required|integer|min:1|exists:rooms,id'
            ]);

            $room = Room::where('id', $id)->first();
            if (!$room) {
                return $this->res_fail('Room not found', [], 1, 404);
            }

            return $this->res_success('Get a room detail successfully', new RoomDetailsResource($room));
        } catch (ValidationException $e) {
            return $this->return_error($e, 422);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/rooms",
     *     tags={"Rooms"},
     *     summary="Create a new room (Admin only)",
     *     description="Create a new room with the provided details.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"room_number", "room_type_id", "status", "price_per_month"},
     *             @OA\Property(property="room_number", type="string", maxLength=10, example="A101"),
     *             @OA\Property(property="room_type_id", type="integer", example=1),
     *             @OA\Property(property="status", type="integer", description="1: Available, 2: Booked", example=1),   
     *            @OA\Property(property="price_per_month", type="number", format="float", example=1500.00),
     *            @OA\Property(property="description", type="string", maxLength=500, example="A cozy single room.")
     *         )
     *     )
     * ),
     *     @OA\Response(
     *         response=200,
     *         description="Room created successfully",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     )
     * )    
     */
    public function store(Request $req)
    {
        try {
            // Validation rules
            $req->validate([
                'room_number' => 'required|string|max:10|unique:rooms,room_number',
                'room_type_id' => 'required|integer|exists:room_types,id',
                'status' => 'required|integer|in:'.implode(',', [Room::AVAILABLE, Room::BOOKED]), // 1: Available, 2: Booked
                'price_per_month' => 'required|numeric|min:0|max:99999999.99',
                'description' => 'nullable|string|max:500'
            ]);

            // Create new room
            $room = new Room($req->only(['room_number', 'room_type_id', 'status', 'price_per_month', 'description']));
            $room->save();

            return $this->res_success('Room created successfully', new RoomDetailsResource($room), 201);
        } catch (ValidationException $e) {
            return $this->return_error($e, 422);
        }
    }
    /**
     * @OA\Put(
     *     path="/api/rooms/{id}",
     *     tags={"Rooms"},
     *     summary="Update an existing room by ID (Admin only)",
     *     description="Update the details of an existing room by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the room to update.",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="room_number", type="string", maxLength=10, example="A101"),
     *             @OA\Property(property="room_type_id", type="integer", example=1),
     *             @OA\Property(property="status", type="integer", description="1: Available, 2: Booked", example=1),   
     *            @OA\Property(property="price_per_month", type="number", format="float", example=1500.00),
     *           @OA\Property(property="description", type="string", maxLength=500, example="A cozy single room.")
     *        )
     *     )
     * ),
     *     @OA\Response(
     *         response=200,
     *         description="Room updated successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found",    
     *      ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     ),
     * )    
     */
    public function update(Request $req, $id)
    {
       try {
            // Merge ID into request for validation
            $req->merge(['id' => $id]);
            // Validation rules
            $req->validate([
                'id' => 'required|integer|min:1|exists:rooms,id',
                'room_number' => 'sometimes|required|string|max:10|unique:rooms,room_number,' . $id,
                'room_type_id' => 'sometimes|required|integer|exists:room_types,id',
                'status' => 'sometimes|required|integer|in:'.implode(',', [Room::AVAILABLE, Room::BOOKED]), // 1: Available, 2: Booked
                'price_per_month' => 'sometimes|required|numeric|min:0|max:99999999.99',
                'description' => 'nullable|string|max:500'
            ]);

            // Find the room
            $room = Room::where('id', $id)->first();
            if (!$room) {
                return $this->res_fail('Room not found', [], 1, 404);
            }

            // Update room details
            if($req->filled('room_number')) {
                $room->room_number = $req->input('room_number');
            }
            if($req->filled('room_type_id')) {
                $room->room_type_id = $req->input('room_type_id');
            }
            if($req->filled('status')) {
                $room->status = $req->input('status');
            }
            if($req->filled('price_per_month')) {
                $room->price_per_month = $req->input('price_per_month');
            }
            if($req->filled('description')) {
                $room->description = $req->input('description');
            }
            $room->save();

            return $this->res_success('Room updated successfully', new RoomDetailsResource($room));
        } catch (ValidationException $e) {
            return $this->return_error($e, 422);
        }

    }
    /**
     * @OA\Delete(
     *     path="/api/rooms/{id}",
     *     tags={"Rooms"},
     *     summary="Delete a room (Admin only)",
     *     description="Delete an existing room by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the room to delete.",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room deleted successfully",
     *     ),
     *     @ROA\Response(
     *         response=404,
     *         description="Room not found",    
     *      ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     ),
     * )    
     */
    public function destroy(Request $req, $id)
    {
        try {
            // Merge ID into request for validation
            $req->merge(['id' => $id]);
            // Validation rules
            $req->validate([
                'id' => 'required|integer|min:1|exists:rooms,id'
            ]);

            // Find the room
            $room = Room::where('id', $id)->first();
            if (!$room) {
                return $this->res_fail('Room not found', [], 1, 404);
            }

            // Delete the room
            $room->delete();

            return $this->res_success('Room deleted successfully');
        } catch (ValidationException $e) {
            return $this->return_error($e, 422);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/rooms/bulk-delete",
     *     tags={"Rooms"},
     *     summary="Bulk delete rooms (Admin only)",
     *     description="Delete multiple rooms by their IDs.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ids"},
     *             @OA\Property(
     *                 property="ids",
     *                 type="array",
     *                 @OA\Items(type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rooms deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     )
     * )
     */
    public function bulk_delete(Request $req)
    {
        try {
            // Merge IDs into request for validation
            $req->merge(['ids' => $req->input('ids')]);
            // Validation rules
            $req->validate([
                'ids' => 'required|array|min:1',
                'ids.*' => 'integer|min:1|exists:rooms,id'
            ]);

            $ids = $req->input('ids');

            // Delete rooms in bulk
            $room = Room::whereIn('id', $ids)->delete();

            if (!$room) {
                return $this->res_fail('No Rooms found to delete', [], 1, 404);
            }

            return $this->res_success('Rooms deleted successfully');
        } catch (ValidationException $e) {
            return $this->return_error($e, 422);
        }
    }
}

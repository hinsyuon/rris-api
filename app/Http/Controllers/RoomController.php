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
     *     summary="Get list of rooms",
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
}

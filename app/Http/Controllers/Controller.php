<?php

namespace App\Http\Controllers;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;


/**
 * @OA\Info(
 *    title="Rent Room Information System API",
 *    version="1.0.0",
 *    description="API documentation for the Rent Room Information System",
 *    @OA\Contact(
 *        email="it.hinsyuon@gmail.com",
 *        name="Hinsy Uon",
 *        url="https://github.com/hinsyuon"
 *    )
 * )
 * @OA\Tag(
 *     name="Rooms",
 *     description="API Endpoints for Managing Rooms"
 * )
 * @OA\Tag(
 *     name="Room Types",
 *     description="API Endpoints for Managing Room Types"
 * )
 * @OA\Tag(
 *     name="Tenants",
 *     description="API Endpoints for Managing Tenants"
 * )
 */

abstract class Controller
{
    //
    protected function return_error(ValidationException $e, $code)
    {
        return response()->json([
                'result' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors()
        ], $code);
    }
    protected function index_req_validate(Request $req, $allowSortCol = [], $allowFilterCol = [], $allowFilterVal = [])
    {
        $res = [];
        $search = strval($req->input('search', ''));
        $sortCol = strval($req->input('sort_col', ''));
        $sortDir = strval($req->input('sort_dir', ''));
        $filterCol = strval($req->input('filter_col', ''));
        $filterVal = $req->input('filter_val');

        if ($req->has('sort_col')) {
            $res['is_sort'] = true;
            if (count($allowSortCol) > 0) {
                if (strlen($sortCol) == 0) {
                    $res['result'] = false;
                    $res['message'] = 'Sort column can not be empty.';
                    return $res;
                }
                if (!in_array($sortCol, $allowSortCol)) {
                    $res['result'] = false;
                    $res['message'] = 'Sort column not allowed.';
                    return $res;
                }
                if (strlen($sortDir) == 0) {
                    $res['result'] = false;
                    $res['message'] = 'Sort direction can not be empty.';
                    return $res;
                }
                if (!in_array($sortDir, ['asc', 'desc'])) {
                    $res['result'] = false;
                    $res['message'] = 'Sort direction is invalid.';
                    return $res;
                }
            }
        } else {
            $res['is_sort'] = false;
        }
        if ($req->has('filter_col')) {
            $res['is_filter'] = true;
            if (count($allowFilterCol) > 0) {
                if (strlen($filterCol) == 0) {
                    $res['result'] = false;
                    $res['message'] = 'Filter colum can not be empty.';
                    return $res;
                }
                if (!in_array($filterCol, $allowFilterCol)) {
                    $res['result'] = false;
                    $res['message'] = 'Filter colum not allowed.';
                    return $res;
                }
            }
            if (count($allowFilterVal) > 0) {
                if (!in_array($filterVal, $allowFilterVal)) {
                    $res['result'] = false;
                    $res['message'] = 'Filter value not allowed.';
                    return $res;
                }
            }
        } else {
            $res['is_filter'] = false;
        }

        $res['result'] = true;
        $res['search'] = $search;
        $res['is_search'] = strlen($search) > 0;
        $res['sort_col'] = $sortCol;
        $res['sort_dir'] = $sortDir;
        $res['filter_col'] = $filterCol;
        $res['filter_val'] = $filterVal;
        return $res;
    }

    protected function res_success($message = '', $data = [], $code = 1, $errors = [])
    {
        $responseData['result'] = true;
        $responseData['code'] = $code;
        $responseData['message'] = $message;
        $responseData['data'] = $data;
        if($errors) $responseData['errors'] = $errors;
        return response()->json($responseData, 200);
    }

    protected function res_fail($message = '', $data = [], $code = 1, $status = 400)
    {
        $responseData['result'] = false;
        $responseData['code'] = $code;
        $responseData['message'] = $message;
        $responseData['data'] = $data;
        return response()->json($responseData, $status);
    }

    public function res_wentwrong()
    {
        $responseData['result'] = false;
        $responseData['code'] = 1;
        $responseData['message'] = 'Something went wrong!';
        $responseData['data'] = [];
        return response()->json($responseData, 500);
    }

    protected function res_paginate($paginate, $message = '', $data = [], $code = 1)
    {
        $responseData['result'] = true;
        $responseData['code'] = $code;
        $responseData['message'] = $message;
        $responseData['data'] = $data;
        $responseData['paginate'] = [
            'has_page' => $paginate->hasPages(),
            'on_first_page' => $paginate->onFirstPage(),
            'has_more_pages' => $paginate->hasMorePages(),
            'first_item' => $paginate->firstItem(),
            'last_item' => $paginate->lastItem(),
            'total' => $paginate->total(),
            'current_page' => $paginate->currentPage(),
            'last_page' => $paginate->lastPage()
        ];
        return response()->json($responseData, 200);
    } 
}
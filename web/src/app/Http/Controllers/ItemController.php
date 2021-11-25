<?php

namespace App\Http\Controllers;

use App\Helpers\ItemRequestHelper;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Get all records
     *
     * @param  Request $request Request Item
     *
     * @return Response
     */
    public function getAllRecords(Request $request)
    {
        $allRecords = ItemRequestHelper::getAllRecords();

        return $this->respondSuccess("Successfully fetched object", $allRecords);
    }

    /**
     * Get a specific Item
     *
     * @param  Request $request Request Item
     * @param  string  $key     Key
     *
     * @return Response
     */
    public function show(Request $request, string $key)
    {
        $timestamp = $request->input('timestamp');
        $objectValue = ItemRequestHelper::getValue($key, $timestamp);

        if (!$objectValue) {
            return $this->respondError('Object not found', 404);
        }

        return $this->respondSuccess("Successfully fetched object", $objectValue);
    }

    /**
     * Store a new item
     *
     * @param  Request $request Request
     * @param  string  $key     Key
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $requestBody = $request->all();

        $userId = auth()->user()->id;

        // remove empty key & value
        $requestBody = collect($requestBody)->filter(function($key, $value) {
            return !$key || $value;
        })->toArray();

        if (empty($requestBody) || !$this->isAssociative($requestBody)) {
            return $this->respondError('Invalid request body provided', 422);
        }

        $result = ItemRequestHelper::store($requestBody, $userId);

        return $this->respondSuccess('Successfully created object(s)', $result, 201);
    }

    /**
     *
     * NOTE: to extract this out to another helper
     *
     * Check whether an object is an associative array
     *
     * @param  array  $arr Array
     *
     * @return boolean
     */
    private function isAssociative($arr) {
        foreach ($arr as $key => $value) { if (is_string($key)) return true; } return false;
    }
}

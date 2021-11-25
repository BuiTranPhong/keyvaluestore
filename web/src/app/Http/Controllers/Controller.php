<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Respond error
     * @param  string $message    Message
     * @param  int    $statusCode Status code
     * @return Response
     */
    protected function respondError(string $message, int $statusCode)
    {
        return response()->json(
          [
            'error' => $message
          ],
          $statusCode,
        );
    }

    /**
     * Respond error
     * @param  mixed  $message    Message
     * @param  int    $statusCode Status code
     * @return Response
     */
    protected function respondSuccess(string $message, $data, int $statusCode = 200)
    {
        return response()->json(
          [
            'message' => $message,
            'data' => $data
          ],
          $statusCode,
        );
    }
}

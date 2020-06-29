<?php
namespace App\Traits;
use Illuminate\Http\Response;
trait ApiResponser
{
    /**
     * Building success response
     * @param $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse($data)
    {
        return \response()->json(['status' => 200, 'data' => $data]);
    }

    public function successPostResponse()
    {
        return \response()->json(['status' => 201, 'message' => 'success']);
    }
    public function errorResponse($message)
    {
        return \response()->json(['status' => 400, 'error' => $message]);
    }
}

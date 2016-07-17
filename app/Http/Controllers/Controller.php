<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Exception;

class Controller extends BaseController
{
    /**
     * This function acts like a try catch wrapper for response handeling. The
     * main reason of this function is to avoid code repetition so instead of
     * having the same try catch called over and over we have it just here.
     *
     * @param  Closure $callBack    A function to be wrapped by try catch
     * @return Illuminate\Http\Response
     */
    protected function executeAndRespond($callBack)
    {
        try {
            $result = $callBack();

            $response = $result->response;
            $statusCode = $result->statusCode;
        } catch (Exception $e) {
            $response = null;
            $statusCode = 500;
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    /**
     * Ideally we could make a class for this but for similycity sake it just
     * wrapps the response and its code into a StdClass object.
     *
     * @param  Closure $callBack    A function to be wrapped by try catch
     * @return StdClass
     */
    protected function respond($response, $statusCode)
    {
        return (object) [
            'response' => $response,
            'statusCode' => $statusCode,
        ];
    }
}

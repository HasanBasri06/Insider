<?php

namespace App;

use Symfony\Component\HttpFoundation\Response;

trait ResponseTrait
{
    /**
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\Response
     */
    public function error(string $message, int $code = 400): Response 
    {
        return response([
            'message' => $message,
            'code' => $code
        ], $code);
    }
}

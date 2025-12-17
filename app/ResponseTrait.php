<?php

namespace App;

use Symfony\Component\HttpFoundation\Response;

trait ResponseTrait
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function error(string $message, int $code = 400): Response
    {
        return response([
            'message' => $message,
            'code' => $code,
        ], $code);
    }
}

<?php

namespace RDStation;

use GuzzleHttp\Exception\RequestException;

class RDException extends RequestException
{
    public function __construct(
        $message = null,
        $code = 0,
        Exception $previous = null)
    {
        switch ($code) {

            case 401:
                $message = 'Invalid token.';
                break;

            case 400:
                $message = 'Identifier not found or invalid email/email_lead data.';
                break;

        }

        parent::__construct($message, $code, $previous);
    }
}

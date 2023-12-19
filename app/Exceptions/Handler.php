<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        // handle stripe errors

        if ($exception instanceof \Stripe\Exception\CardException ||
            $exception instanceof \Stripe\Error\Api ||
            $exception instanceof \Stripe\Error\ApiConnection ||
            $exception instanceof \Stripe\Error\Authentication ||
            $exception instanceof \Stripe\Error\InvalidRequest ||
            $exception instanceof \Stripe\Error\Base) {
            $body = $exception->getJsonBody();
            $err = $body['error']['message'];

            return back()->withError($err);
        }

        return parent::render($request, $exception);
    }
}

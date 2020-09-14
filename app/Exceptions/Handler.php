<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;

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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if($this->is404($exception)) {
            $this->log404($request);
        }
        return parent::render($request, $exception);
    }

    private function is404($exception)
    {
        return $exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException
            || $exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
    }
    private function log404($request)
    {
        $error = [
            'url'    => $request->url(),
            'method' => $request->method(),
            'data'   => $request->all(),
        ];

        $message = '404: ' . $error['url'] . "\n" . json_encode($error, JSON_PRETTY_PRINT);

        Log::debug($message);
    }
}

<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Plista\StatsdClient\StatsdClient;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
        $code = $exception instanceof HttpException
            ? $exception->getStatusCode()
            : $exception->getCode();

        //	Unrecognised exceptions with error code 0 get changed to 500
        if(intval($code) <= 0) $code = 500;

        $parentRender = parent::render($request, $exception);

        // if parent returns a JsonResponse
        // for example in case of a ValidationException
        if ($parentRender instanceof JsonResponse) {
            return $parentRender;
        }

        $message = $exception->getMessage();

        if(empty($message)){
            $arr = explode('\\', get_class($exception));
            $message = trim(implode(" ",preg_split('/(?=[A-Z])/',array_pop($arr))));
        }

        $response = [
            'message' => $message,
            'code' => $code,
        ];

        if(config('app.debug') == "true"){
            $response["stack"] = explode("\n",$exception->getTraceAsString());
        }else{
            $response["stack"] = "Disabled: Production Mode";
        }

        return new JsonResponse($response, $code);
    }
}

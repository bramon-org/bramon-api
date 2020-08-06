<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    const HTTP_RESPONSE_CODES = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        429 => 'Too Many Requests',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported'
    ];

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
     * @param Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception $exception
     * @return \Illuminate\Http\Response|JsonResponse
     * @throws Exception
     */
    public function render($request, Exception $exception)
    {
        $this->report($exception);

        # 401 - Unauthorized
        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        # 422 - Validation Error
        if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        switch (get_class($exception)) {
            # 404 - Not Found
            case 'Illuminate\Database\Eloquent\ModelNotFoundException':
                $statusCode = 404;
                $message = 'Not found.';
                break;

            # Not sure about this case here.
            case 'Illuminate\Http\Exception\HttpResponseException':
                $statusCode = 422;
                $message = $exception->getResponse();
                break;

            case 'GuzzleHttp\Exception\ConnectException':
                $statusCode = 400;
                $message = [
                    'base' => [
                        'Error while trying to connect to third party.',
                    ],
                ];
                break;

            # Whenever using abort() helper
            case 'Symfony\Component\HttpKernel\Exception\HttpException':
                $statusCode = $exception->getStatusCode();
                $message = $exception->getMessage();

                $message = $this->isJsonMessage($message)
                    ? json_decode($message, true)
                    : ["base" => [$message]];
                break;

            # All other exceptions
            default:
                $statusCode = method_exists($exception, 'getStatusCode')
                    ? $exception->getStatusCode()
                    : 500;

                $message = $statusCode === 500
                    ? $this->getInternalServerErrorMessage($exception)
                    : self::HTTP_RESPONSE_CODES[$statusCode];
                break;
        }

        $errorData = ['message' => $message];
        $debugData = [];

        return $this->throw($statusCode, $errorData, $debugData);
    }

    /**
     * @param $status
     * @param $errorData
     * @param array $debugData
     * @return JsonResponse
     */
    private function throw($status, $errorData, array $debugData = [])
    {
        $errorData['status'] = $status;
        $response = ['error' => $errorData];

        if (config('app.debug')) {
            $response['debug'] = $debugData;
        }

        return response()->json($response, $status);
    }

    /**
     * @param $request
     * @param AuthenticationException $exception
     * @return mixed
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->throw(401, ['message' => ['base' => ['Not authenticated']]]);
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param ValidationException $e
     * @param  Request  $request
     * @return Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        if ($e->response) {
            return $e->response;
        }

        return $this->invalidJson($request, $e);
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param  Request  $request
     * @param ValidationException $exception
     * @return JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'message' => $exception->getMessage(),
            'errors' => $exception->errors(),
        ], $exception->status);
    }

    /**
     * Returns the default message for internal server error
     * @param $exception
     * @return array
     */
    private function getInternalServerErrorMessage(Exception $exception): array
    {
        $timestamp = gmdate('U');
        $message = config('app.debug')
            ? $exception->getMessage() . ' in ' . $exception->getFile() . '(' . $exception->getLine() . ')'
            : 'Something went wrong. Please refer to support with the following code: ' . $timestamp;
        return ['fatal' => $message];
    }
}

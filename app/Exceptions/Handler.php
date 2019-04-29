<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\ExceptionHandler as SymfonyExceptionHandler;
use App\Mail\ExceptionOccured;

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
        //For example
        
        /* Send to Sentry
        if (app()->environment('production')) {
            if (app()->bound('sentry') && $this->shouldReport($exception)) {
                app('sentry')->captureException($exception);
            }
        }        
        */
        
        if ($this->shouldReport($exception)) {
            $this->sendEmail($exception);
        }
        
        parent::report($exception);
    }
    
    /**
     * Parse the exception and send email
     * 
     * @param Exception $exception
     * @return void
     */
    public function sendEmail(Exception $exception) : void
    {
        $e = FlattenException::create($exception);

        $handler = new SymfonyExceptionHandler();

        $html = $handler->getHtml($e);

        Mail::queue(new ExceptionOccured($html));
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
        //For example
        if ($request->is('api/*')) {
            return $this->renderForAPI($exception);
        }
        
        return parent::render($request, $exception);
    }
    
    /**
     * 
     * @param Exception $exception
     * @return JsonResponse
     */
    public function renderForAPI(Exception $exception) : JsonResponse
    {
        $response = [
            'error' => true,
            'message' => $exception->getMessage()            
        ];        

        if ($exception instanceof ModelNotFoundException) {

            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
            
        } elseif ($exception instanceof NotFoundHttpException) {

            $response['message'] = 'Page not found.';
            
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
            
        } elseif ($exception instanceof ValidationException) {
            
            $response['validation_errors'] = $exception->errors();
            
            return new JsonResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
          
        } else {

            return new JsonResponse(
                $response,
                $this->isHttpException($exception) 
                    ? $exception->getStatusCode() 
                    : Response::HTTP_INTERNAL_SERVER_ERROR,
                $this->isHttpException($exception) ? $exception->getHeaders() : []
            );

        }        
    }
    
}

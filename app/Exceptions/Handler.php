<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use PDOException;
use Psy\Exception\FatalErrorException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class Handler extends ExceptionHandler
{
	use ApiResponser;
	
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
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (Throwable $e, $request) {
			if ($request->is('api/*')) {
				if ($e instanceof NotFoundHttpException)
				{
					Log::debug($e->getStatusCode() . ' - ' . $e->getMessage() . ' - ' . $request->url());
					
					return $this->responseError('Ошибка, страница не найдена', $e->getStatusCode(), $request->url());
				}
				if ($e instanceof TokenMismatchException
					|| $e instanceof MethodNotAllowedHttpException
					|| $e instanceof TooManyRequestsHttpException
					|| $e instanceof FatalErrorException
					|| $e instanceof PDOException
					|| $e instanceof QueryException
					|| $e instanceof HttpException)
				{
					Log::debug($e->getStatusCode() . ' - ' . $e->getMessage());
					
					return $this->responseError('Ошибка, попробуйте позже', $e->getStatusCode(), $e->getMessage() . ' - ' . $request->url());
				}
			}
			
			//return parent::render($request, $e);
        });
    }
}

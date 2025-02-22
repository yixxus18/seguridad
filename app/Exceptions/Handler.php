<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
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
    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        /**
         * Registra una función reportable para manejar excepciones.
         *
         * @param \Throwable $e La excepción lanzada
         */
        $this->reportable(function (Throwable $e) {
            \Log::info('Exception occurred', [
                'error_name' => class_basename($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
                'url' => request()?->fullUrl(),
                'ip' => request()?->ip(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
                'previous' => $e->getPrevious(),
            ]);
        });
    }

    /**
     * Renderiza una excepción en una respuesta HTTP.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Log the error before handling
        \Log::error('Rendering exception', [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'url' => $request->fullUrl(),
        ]);

        // Handle database connection errors
        if ($exception instanceof \Illuminate\Database\QueryException ||
            $exception instanceof \PDOException) {
            \Log::critical('Database connection error', [
                'error' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ]);

            return response()->view('errors.database', [
                'message' => 'No se pudo completar la peticion . Por favor intente nuevamente más tarde.'
            ], 500);
        }

        // Handle token mismatch
        if ($exception instanceof TokenMismatchException) {
            session()->invalidate();
            session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.')
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
        }

        // Default error handling
        return parent::render($request, $exception);
    }
}

<?php

use App\Http\Middleware\ForceJsonResponseMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //Forces the response to always be a json
        $middleware->api(prepend:[
            ForceJsonResponseMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $throwable){
           return response()->json([
              'message' => 'Unauthenticated.',
               'errors' => [ 'code' => '1001']
           ], 401);
        });

        $exceptions->render(function (NotFoundHttpException $throwable){
            return response()->json([
                'message' => 'Not Found',
                'errors' => [ 'code' => '1002']
            ], 404);
        });
    })->create();

<?php

namespace Maenbn\OpenAmAuth\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Foundation\Application;

class SetOpenAmCookie
{

    protected $auth;

    protected $app;


    public function __construct(Guard $auth, Application $app)
    {
        $this->auth = $auth;
        $this->app = $app;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $openAmConfig = $this->app['config']['openam'];
        $user = $this->auth->user();
        if(!$request->hasCookie($openAmConfig['cookieName']) && isset($user))
        {
            $response = $next($request);
            return $response
                ->withCookie(
                    cookie($openAmConfig['cookieName'], $user->tokenId, 0,
                        $openAmConfig['cookiePath'], $openAmConfig['cookieDomain'])
                );
        }

        return $next($request);
    }
}
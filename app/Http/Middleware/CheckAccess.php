<?php

namespace App\Http\Middleware;

use App\Classes\Constants\StatusCodes;
use App\Models\AccessCode;
use Closure;
use Illuminate\Http\Request;

class CheckAccess
{
    const ACCESS_TOKEN_COOKIE_NAME = "access-token";
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasCookie(self::ACCESS_TOKEN_COOKIE_NAME)) {
            $token = $request->cookie(self::ACCESS_TOKEN_COOKIE_NAME);

            if (!is_null(AccessCode::where("code", $token)->first())) {
                return $next($request);
            }

            return redirect("/");
        }
        return redirect("/");
    }
}

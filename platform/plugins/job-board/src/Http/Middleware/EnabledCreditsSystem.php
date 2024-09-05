<?php

namespace Botble\JobBoard\Http\Middleware;

use Botble\JobBoard\Facades\JobBoardHelper;
use Closure;
use Illuminate\Http\Request;

class EnabledCreditsSystem
{
    public function handle(Request $request, Closure $next)
    {
        if (! JobBoardHelper::isEnabledCreditsSystem()) {
            abort(404);
        }

        return $next($request);
    }
}

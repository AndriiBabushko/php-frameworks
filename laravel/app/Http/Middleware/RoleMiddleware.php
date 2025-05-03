<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RoleMiddleware
{
    public function handle(Request $req, Closure $next, ...$roles)
    {
        $user = auth('api')->user();
        foreach ($roles as $r) {
            if (in_array($r, $user->getRoles(), true)) {
                return $next($req);
            }
        }
        throw new AccessDeniedHttpException;
    }
}

<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\ItRequestForm;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth('it')->user();
            $itDraftCount = ($user && !$user->isAdmin())
                ? ItRequestForm::where('submitted_by', $user->id)->where('status', 'Draft')->count()
                : 0;
            view()->share(['user' => $user, 'itDraftCount' => $itDraftCount]);
            return $next($request);
        });
    }
}

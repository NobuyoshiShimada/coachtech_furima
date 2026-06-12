<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest
use Illuminate\Http\Request;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

class LoginController extends Controller
{
    public function store(LoginRequest $request) {
        return app(AuthenticatedSessionController::class)->store($request);
    }
}

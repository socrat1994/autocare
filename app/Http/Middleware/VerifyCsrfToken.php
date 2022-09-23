<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'http://localhost:8000/employeedeledi',
        'http://localhost:8000/branchdeledi',
        'http://localhost:8000/employee',
        'http://localhost:8000/addvehicle',
        'http://localhost:8000/branch'

    ];
}

<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'api-routes/black-white-list',
        'api-routes/app-usage',
        'api-routes/net-usage',
        'api-routes/child-schedule',
        'api-routes/save-location',
        'api-routes/save-event',
        'api-routes/all-instaled-app',
        'api-routes/change-status-app',
        'api-routes/change-schedule-app',
        'api-routes/create-schedule-app',
        'api-routes/all-locations-child',
        'api-routes/store-parents',
        'api-routes/store-child',
        'api-routes/login-parents',
        'api-routes/delete-schedule-app'
    ];
}

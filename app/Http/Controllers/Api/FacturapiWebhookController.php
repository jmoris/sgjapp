<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FacturapiWebhookService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class FacturapiWebhookController extends Controller
{
    public function __invoke(Request $request, FacturapiWebhookService $webhooks): SymfonyResponse
    {
        return $webhooks->handleRequest($request);
    }
}

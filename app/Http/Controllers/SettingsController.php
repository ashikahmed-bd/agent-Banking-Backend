<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;

class SettingsController extends Controller
{
    public function reboot(): JsonResponse
    {
        artisan::call('optimize');
        artisan::call('optimize:clear');
        artisan::call('config:cache');
        artisan::call('event:cache');
        artisan::call('route:cache');
        artisan::call('view:cache');

        return response()->json([
            'success' => true,
            'message' => 'Reboot Successfully!',
        ], Response::HTTP_OK);
    }

    public function seed(): JsonResponse
    {
        artisan::call('migrate:fresh --seed');

        return response()->json([
            'success' => true,
            'message' => 'Database seeded Successfully!',
        ], Response::HTTP_OK);
    }
}

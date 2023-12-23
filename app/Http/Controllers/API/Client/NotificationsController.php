<?php

namespace App\Http\Controllers\API\Client;

use App\Http\Controllers\Controller;

class NotificationsController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => auth()->user()->notifications,
            'status' => true,
        ]);
    }
}

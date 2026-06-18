<?php

namespace App\Http\Controllers;

use App\Services\DashboardMetricsService;

class HomeController extends Controller
{
    public function index(DashboardMetricsService $metrics)
    {
        return view('admin.dashboard', $metrics->summary());
    }
}

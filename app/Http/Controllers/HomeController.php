<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DashboardMetricsService;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(DashboardMetricsService $metrics)
    {


        return view('admin.dashboard', $metrics->summary());
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Page;
use App\Models\Section;
use App\Models\Setting;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'pagesCount' => Page::count(),
            'sectionsCount' => Section::count(),
            'settingsCount' => Setting::count(),
            'unreadMessages' => Contact::where('is_read', false)->count(),
            'totalMessages' => Contact::count(),
            'recentMessages' => Contact::latest()->take(5)->get(),
        ]);
    }
}

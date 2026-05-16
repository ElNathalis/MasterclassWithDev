<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Registration;
use Illuminate\View\View;
class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::all();
        $myRegistrations = collect(); // пустая коллекция по умолчанию

        if (auth()->check() && auth()->user()->role === 'visitor') {
            $myRegistrations = Registration::where('user_id', auth()->id())
                ->with('masterClass.user', 'masterClass.category')
                ->whereHas('masterClass', function ($query) {
                    $query->whereDate('date', '>=', now());
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('home', compact('categories', 'myRegistrations'));
    }
}
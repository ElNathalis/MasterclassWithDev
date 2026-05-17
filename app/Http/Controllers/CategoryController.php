<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MasterClass;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
    public function show(Category $category): View
    {
        $masterClasses = MasterClass::where('category_id', $category->id)
            ->with('user') // ведущий
            ->whereDate('date', '>=', now()->toDateString())
            ->withCount('registrations') // количество записавшихся
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        return view('category', compact('category', 'masterClasses'));
    }
}

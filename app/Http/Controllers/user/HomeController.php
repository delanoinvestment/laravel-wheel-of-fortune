<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;
class HomeController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('user.welcome', compact([
            'products'
        ]));
    }
}

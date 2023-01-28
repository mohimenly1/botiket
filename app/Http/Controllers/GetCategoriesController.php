<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class GetCategoriesController extends Controller
{
   
    public function index(Request $requst){
    
        $categories = Category::with('subCategories')->get();
    
        return response()->json([
            "status" => 200,
            'data' => $categories,
        ]);
    }
}

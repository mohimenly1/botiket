<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Gender;
use Illuminate\Http\Request;

class GendersController extends Controller
{
    public function index()
    {
        $genders = Gender::all();
        return response()->json([
            "status" => 200,
            'data' => $genders,
        ]);
    }
}
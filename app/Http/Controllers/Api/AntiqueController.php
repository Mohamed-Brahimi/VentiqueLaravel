<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Antique;
class AntiqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $antiques = Antique::latest()->paginate(10);
        return response()->json($antiques, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $antique = $request->all();
        $request->validate([
            'name',
            'description',
            'price',
            'image',
        ]);
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Antique;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function index()
    {
        $antiques = Antique::paginate(5);
        return view('listantiques', compact('antiques'));
    }


}
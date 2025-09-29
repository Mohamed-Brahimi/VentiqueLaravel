<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Antique;

class AntiqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $antiques = Antique::all();
        return view(view: 'antiques.index', data: compact(var_name: 'antiques'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('antiques.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'description' => 'required',
                'price' => 'required',
            ]
        );

        Antique::create($request->all());
        return redirect("/")//->route('antiques.store')
            ->with('success', 'antique added succesfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $antique = Antique::findOrFail($id);
        return view('antiques.show', compact('antique'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $antique = Antique::findOrFail($id);
        return view('antiques.edit', compact('antique'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate(
            [
                'name' => 'required',
                'description' => 'required',
                'price' => 'required',
            ]
        );
        $antique = Antique::findOrFail($id);
        $antique->update($request->all());
        return redirect()->route('antiques.index')
            ->with('success', 'antique edited successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $antique = Antique::findOrFail($id);
        $antique->delete();
        return redirect()->route('antiques.index')
            ->with('success', 'antique deleted successfully');
    }
}

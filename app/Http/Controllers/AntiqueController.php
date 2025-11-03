<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Antique;

class AntiqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $query = Antique::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $antiques = $query->orderBy('created_at', 'desc')->get();

        return view('antiques.index', compact('antiques', 'search'));
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
                'image' => 'required|image|mimes:jpeg,png,jpg,gif'
            ]
        );

        $data = $request->all();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('antiques', 'public');
            $data['image'] = $imagePath;
        }

        Antique::create($data);
        return redirect("/")
            ->with('success', 'antique added successfully');
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
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric|min:0.01',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif'
        ]);

        $antique = Antique::findOrFail($id);
        $data = $request->except('image');

        // Handle image update
        if ($request->hasFile('image')) {

            $imagePath = $request->file('image')->store('antiques', 'public');
            $data['image'] = $imagePath;
        }

        $antique->update($data);

        return redirect()->route('antiques.show', $antique->id)
            ->with('success', 'Antique modifiée avec succès');
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
    public function autocomplete(Request $request)
    {
        $search = $request->input('search', '');
        if (trim($search) === '') {
            return response()->json([]);
        }

        $antiques = Antique::orderBy('name', 'ASC')
            ->select('id', 'name')
            ->where('name', 'LIKE', '%' . $search . '%')
            ->limit(10)
            ->get();

        $response = $antiques->map(function ($antique) {
            return [
                'value' => $antique->id,
                'label' => $antique->name,
            ];
        })->toArray();

        return response()->json($response);
    }
}
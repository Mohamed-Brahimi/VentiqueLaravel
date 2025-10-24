<?php

namespace App\Http\Controllers;
use App\Models\Offer;

use Illuminate\Http\Request;
use Validator;

class OfferController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'antique_id' => 'required',
            'user_id' => 'required',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('warning', 'Tous les champs sont requis');
        } else {
            Offer::create([
                'antique_id' => $request->antique_id,
                'user_id' => $request->user_id,
                'price' => $request->price,
                'dateOffered' => now(),
                'erased' => false,
            ]);
            return redirect()->back()->with('success', 'Offre créée avec succès');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $offer = Offer::findOrFail($id);
        $offer->delete();

        return redirect()->back()->with('success', 'Offer deleted successfully');
    }
}

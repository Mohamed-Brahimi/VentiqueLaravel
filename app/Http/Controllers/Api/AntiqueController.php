<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Antique;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AntiqueController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search', '');
            
            $query = Antique::query()->with('user');
            
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }
            
            $antiques = $query->orderBy('created_at', 'desc')->get();
            
            // Transform the data to include proper image URLs
            $antiques = $antiques->map(function ($antique) {
                return [
                    'id' => $antique->id,
                    'name' => $antique->name,
                    'description' => $antique->description,
                    'price' => $antique->price,
                    'image' => $this->getImageUrl($antique->image),
                    'image_url' => $this->getImageUrl($antique->image),
                    'created_at' => $antique->created_at,
                    'updated_at' => $antique->updated_at,
                    'user_id' => $antique->user_id,
                    'user' => $antique->user ? [
                        'id' => $antique->user->id,
                        'name' => $antique->user->name,
                    ] : null,
                ];
            });
            
            \Log::info('Returning ' . $antiques->count() . ' antiques');
            
            // Return as simple array
            return response()->json($antiques, 200);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching antiques: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching antiques',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $antique = Antique::with('user', 'offers')->findOrFail($id);
            
            $data = [
                'id' => $antique->id,
                'name' => $antique->name,
                'description' => $antique->description,
                'price' => $antique->price,
                'image' => $this->getImageUrl($antique->image),
                'image_url' => $this->getImageUrl($antique->image),
                'created_at' => $antique->created_at,
                'updated_at' => $antique->updated_at,
                'user_id' => $antique->user_id,
                'user' => $antique->user,
                'offers' => $antique->offers,
            ];
            
            return response()->json($data, 200);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching antique: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Antique not found'
            ], 404);
        }
    }
    
    /**
     * Get proper image URL for an antique
     */
    private function getImageUrl($imagePath)
    {
        if (!$imagePath) {
            return asset('images/default-antique.jpg');
        }
        
        // If it's already a full URL (from Faker), return as is
        if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            return $imagePath;
        }
        
        // If it's a local storage path
        return asset('storage/' . $imagePath);
    }

    public function store(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0.01',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('antiques', 'public');
            }

            // Create antique
            $antique = Antique::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'image' => $imagePath,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Antiquité ajoutée avec succès',
                'data' => [
                    'id' => $antique->id,
                    'name' => $antique->name,
                    'description' => $antique->description,
                    'price' => $antique->price,
                    'image_url' => $this->getImageUrl($antique->image),
                ]
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Antique creation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'antiquité',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}

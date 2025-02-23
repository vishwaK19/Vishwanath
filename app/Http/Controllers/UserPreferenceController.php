<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserPreferenceController extends Controller
{
    public function store(Request $request) {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id', 
                'preferred_sources' => 'nullable|array',
                'preferred_categories' => 'nullable|array', 
                'preferred_authors' => 'nullable|array',
            ]);
    
            $preference = UserPreference::updateOrCreate(
                ['user_id' => $request->user_id],
                $request->only(['preferred_sources', 'preferred_categories', 'preferred_authors'])
            );
    
            return response()->json($preference);
        } catch(ValidationException $e) {
            return response()->json(['errors' => $e->errors()],422);
        }
    }

    public function show($userId) {
        $user = User::with('preference')->where('uuid', $userId)->firstOrFail();

        if (!$user->preference) {
            return response()->json(['message' => 'Preferences not found'], 404);
        }

        return response()->json([
        'name' => $user->name,
        'uuid' => $user->uuid,
        'source' => $user->preference->preferred_sources,
        'categories' => $user->preference->preferred_categories,
        'authors' => $user->preference->preferred_authors
    ]);
    }

    public function personalizedFeed($userId) {
        try {
            $preference = UserPreference::where('user_id', $userId)->first();
            if (!$preference) {
                return response()->json(['message' => 'Preferences not found'], 404);
            }
        
            $query = Article::query();
        
            if ($preference->preferred_sources) {
                $query->whereIn('source', $preference->preferred_sources);
            }
        
            if ($preference->preferred_categories) {
                $query->whereIn('category', $preference->preferred_categories);
            }
        
            if ($preference->preferred_authors) {
                $query->whereIn('author', $preference->preferred_authors);
            }
        
            $articles = $query->paginate(10);
        
            return response()->json($articles);
        } catch(ValidationException $e) {
            return response()->json(['errors' => $e->errors()],422);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index(Request $request) {
        $query = Article::query();

        if($request->has('keyword')) {
            $query->where('title','like','%' . $request->keyword . '%')->orWhere('content','like','%' . $request->keyword . '%');
        }

        if($request->has('date')) {
            $query->whereDate('published_at',$request->date);
        }

        if($request->has('category')) {
            $query->where('category',$request->category);
        }

        if($request->has('source')) {
            $query->where('source',$request->source);
        }
        // return response()->json(['message'=>'Hello']);
        $articles = $query->paginate(10);
        return response()->json($articles);
    }

    public function show($articleId) {
        $article = Article::find($articleId);

        if(!$article) {
            return response()->json(['message' => 'Article Not Found'], 404);
        }

        return response()->json($article);
    }
}

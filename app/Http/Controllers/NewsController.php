<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{
    public function getNews(Request $request)
    {
        // Fetch news from API if 'fetch=true' is sent or if DB is empty
        if ($request->query('fetch') === 'true' || News::count() === 0) {
            $category = $request->query('category', 'general'); // Default to 'general'
            $this->fetchAndStoreNews($category);
        }

        // Query from database with filters
        $query = News::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('source')) {
            $query->where('source', $request->source);
        }

        if ($request->has('author')) {
            $query->where('author', $request->author);
        }

        $news = $query->orderBy('published_at', 'desc')->get();

        return response()->json([
            'message' => 'News fetched successfully!',
            'data' => $news
        ]);
    }

    private function fetchAndStoreNews($category)
    {
        $apiKey = env('NEWS_API_KEY'); 

        $response = Http::get("https://newsapi.org/v2/top-headlines", [
            'category' => $category,
            'country' => 'us',
            'apiKey' => $apiKey
        ]);

        if ($response->failed()) {
            return response()->json(['message' => 'Failed to fetch news'], 500);
        }

        $articles = $response->json()['articles'] ?? [];

        if (empty($articles)) {
            return response()->json(['message' => 'No articles found'], 404);
        }

        foreach ($articles as $article) {
            News::updateOrCreate(
                ['title' => $article['title']],
                [
                    'source' => $article['source']['name'],
                    'author' => $article['author'] ? $article['author'] : "",
                    'category' => ucfirst($category),
                    'content' => $article['content'] ? $article['content']:"",
                    'published_at' => $article['publishedAt'] ? now() : "",
                    'url' => $article['url'] ? 'https://example.com' : "" // Provide default URL
                ]
            );
        }
    }



    public function getPersonalizedNews()
    {
        $user = Auth::user();
        $preferences = Preference::where('user_id', $user->id)->first();

        if (!$preferences) {
            return response()->json(['message' => 'No preferences set'], 404);
        }

        $query = News::query();

        if (!empty($preferences->categories)) {
            $query->whereIn('category', $preferences->categories);
        }

        if (!empty($preferences->sources)) {
            $query->whereIn('source', $preferences->sources);
        }

        if (!empty($preferences->authors)) {
            $query->whereIn('author', $preferences->authors);
        }

        return response()->json($query->orderBy('published_at', 'desc')->get());
    }
}

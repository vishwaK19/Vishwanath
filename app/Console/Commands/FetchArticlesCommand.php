<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Article;
use Illuminate\Support\Facades\Log;

class FetchArticlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->fetchNewsApi();
        $this->fetchNewYorkTimes();
        $this->fetchGuardianNews();
        $this->info('News fetched successfully');
    }

    protected function fetchNewsApi() {
        // \Log::channel('single')->info('Fetching articles from NewsAPI');
        $apiKey = config('services.newsapi.key');
        // $apiUrl = 'https://newsapi.org/v2/everything?q=tesla&from=2025-02-10&sortBy=publishedAt&apiKey=1c4d1e64fcfc4f3eade532d79efb94b9';
        $apiUrl = "https://newsapi.org/v2/everything?q=tesla&from=2025-02-10&sortBy=publishedAt&apiKey={$apiKey}";
        $response = Http::get($apiUrl);

        if ($response->successful()) {
            $articles = $response->json()['articles'];
            // dd($articles);
            foreach ($articles as $article) {
                Article::updateOrCreate(
                    ['title' => $article['title']],
                    [
                        'content' => $article['description'] ?? '',
                        'category' => $article['category'] ?? 'general',
                        'source' => $article['source']['name'] ?? 'NewsAPI',
                        'author' => $article['author'] ?? 'Unknown',
                        'published_at' => $article['publishedAt'] ?? now(),
                    ]
                );
            }
        } else {
            Log::error('News API Error for NewsApi: ' . $response->body());
            return;
        }
    }

    protected function fetchNewYorkTimes() {
        // $apiUrl = 'https://api.nytimes.com/svc/archive/v1/2024/1.json?api-key=6v6S2J2XKUQCp9utjoe7PQjQbBXCeRUP';
        $apiKey = config('services.nytimes.key');
        $apiUrl = "https://api.nytimes.com/svc/archive/v1/2024/1.json?api-key={$apiKey}";
        $response = Http::get($apiUrl);

        if($response->successful()) {
            $articles = $response->json()['response']['docs'];
            foreach ($articles as $article) {
                Article::updateOrCreate(
                    ['title' => $article['headline']['main']],
                    [
                        'content' => $article['abstract'] ?? '',
                        'category' => $article['section_name'] ?? 'General',
                        'source' => 'New York Times',
                        'author' => $article['byline']['original'] ?? 'Unknown',
                        'published_at' => $article['pub_date'] ?? now(),
                        ]
                );
            }
        } else {
            Log::error('News API Error for NewYorkTimes: ' . $response->body());
            return;
        }
    }

    protected function fetchGuardianNews() {
        $apiKey = config('services.theguardian.key');
        // $apiUrl = 'https://content.guardianapis.com/search?api-key=840c7b39-90b5-4a12-a29e-22c692f42a68';
        $apiUrl = "https://content.guardianapis.com/search?api-key={$apiKey}";
        $response = Http::get($apiUrl);

        if (!$response->successful()) {
            // Log the error and stop execution
            Log::error('News API Error: ' . $response->body());
            return;
        }
        if($response->successful()) {
            $articles = $response->json()['response']['results'];
            foreach ($articles as $article) {
                Article::updateOrCreate(
                    ['title' => $article['webTitle']],
                    [
                        'content' => $article['fields']['body'] ?? '',
                        'category' => $article['sectionId'] ?? 'General',
                        'source' => 'The Guardian',
                        'author' => $article['webUrl'] ?? 'Unknown',
                        'published_at' => $article['webPublicationDate'] ?? now(),
                    ]
                );
            }
        } else {
            Log::error('News API Error for GuradinaTimes: ' . $response->body());
            return;
        }
    }
}

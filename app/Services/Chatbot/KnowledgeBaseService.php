<?php

namespace App\Services\Chatbot;

use App\Models\HotelTerm;
use App\Models\KnowledgeBase;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class KnowledgeBaseService
{
    public function search(string $query, string $language = 'en', int $limit = 4): array
    {
        $keywords = $this->extractKeywords($query);

        $entries = KnowledgeBase::query()
            ->where('is_active', true)
            ->get()
            ->map(function (KnowledgeBase $entry) use ($language, $keywords) {
                $title = $language === 'ar' && $entry->title_ar ? $entry->title_ar : $entry->title;
                $content = $language === 'ar' && $entry->content_ar ? $entry->content_ar : $entry->content;

                return [
                    'source' => 'knowledge_base',
                    'title' => $title,
                    'content' => $content,
                    'score' => $this->score($title, $content, $keywords, collect($entry->keywords ?? [])),
                ];
            });

        $terms = HotelTerm::query()
            ->where('is_active', true)
            ->orderBy('order_no')
            ->get()
            ->map(function (HotelTerm $term) use ($keywords) {
                return [
                    'source' => 'hotel_terms',
                    'title' => 'Hotel Policy',
                    'content' => $term->description,
                    'score' => $this->score('Hotel Policy', $term->description, $keywords, collect()),
                ];
            });

        $results = $entries
            ->concat($terms)
            ->sortByDesc('score')
            ->values();

        if ($keywords->isEmpty()) {
            return $results->take($limit)->all();
        }

        $matched = $results
            ->filter(fn (array $item) => $item['score'] > 0)
            ->take($limit)
            ->values();

        if ($matched->isEmpty()) {
            return $results->take($limit)->all();
        }

        return $matched->all();
    }

    public function promptContext(string $query, string $language = 'en', int $limit = 4): string
    {
        $articles = $this->search($query, $language, $limit);

        if ($articles === []) {
            return $language === 'ar'
                ? 'لا توجد نتائج مطابقة في قاعدة المعرفة الداخلية.'
                : 'No matching internal knowledge base entries were found.';
        }

        return collect($articles)
            ->map(function (array $item, int $index) {
                $content = Str::limit(preg_replace('/\s+/', ' ', $item['content']) ?? '', 500);

                return ($index + 1).'. '.$item['title'].': '.$content;
            })
            ->implode("\n");
    }

    public function articlesForPolicy(string $topic, string $language = 'en', int $limit = 4): array
    {
        return $this->search($topic, $language, $limit);
    }

    private function extractKeywords(string $query): Collection
    {
        $normalized = Str::of($query)
            ->lower()
            ->replaceMatches('/[^\pL\pN\s]+/u', ' ')
            ->squish()
            ->toString();

        return collect(explode(' ', $normalized))
            ->filter(fn (string $token) => mb_strlen($token) >= 3)
            ->values();
    }

    private function score(string $title, string $content, Collection $keywords, Collection $entryKeywords): int
    {
        if ($keywords->isEmpty()) {
            return 1;
        }

        $haystackTitle = Str::lower($title);
        $haystackContent = Str::lower($content);
        $normalizedEntryKeywords = $entryKeywords
            ->map(fn ($keyword) => Str::lower((string) $keyword))
            ->filter();

        return $keywords->reduce(function (int $score, string $keyword) use ($haystackTitle, $haystackContent, $normalizedEntryKeywords) {
            if (Str::contains($haystackTitle, $keyword)) {
                $score += 5;
            }

            if (Str::contains($haystackContent, $keyword)) {
                $score += 2;
            }

            if ($normalizedEntryKeywords->contains($keyword)) {
                $score += 3;
            }

            return $score;
        }, 0);
    }
}

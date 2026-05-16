<?php

namespace App\Support;

use Illuminate\Support\Str;

class SupportTicketRichText
{
    public static function sanitize(?string $html): string
    {
        $html = strip_tags((string) $html, '<p><br><strong><b><em><i><u><ul><ol><li><a><blockquote><code><pre>');

        return trim(preg_replace_callback('/<([a-z0-9]+)(\s[^>]*)?>/i', function (array $match): string {
            $tag = strtolower($match[1]);

            if ($tag === 'a') {
                $href = '#';

                if (preg_match('/href\s*=\s*([\'"])(.*?)\1/i', $match[2] ?? '', $hrefMatch)) {
                    $candidate = trim(html_entity_decode($hrefMatch[2], ENT_QUOTES | ENT_HTML5, 'UTF-8'));

                    if (Str::startsWith($candidate, ['http://', 'https://', 'mailto:', 'tel:', '/', '#'])) {
                        $href = $candidate;
                    }
                }

                return '<a href="'.htmlspecialchars($href, ENT_QUOTES, 'UTF-8').'" target="_blank" rel="noopener noreferrer">';
            }

            if ($tag === 'br') {
                return '<br>';
            }

            return '<'.$tag.'>';
        }, $html));
    }

    public static function plainText(string $html): string
    {
        return trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
}

<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class ContentHelper
{
    /**
     * Tự động thêm ID vào các thẻ h2 trong nội dung HTML.
     *
     * @param string|null $content
     * @return string
     */
    public static function addIdsToHeadings(?string $content): string
    {
        if (is_null($content)) {
            return '';
        }

        return preg_replace_callback('/<h2.*?>(.*?)<\/h2>/', function ($matches) {
            $title = $matches[1];
            $slug = Str::slug(strip_tags($title));
            return '<h2 id="' . $slug . '">' . $title . '</h2>';
        }, $content);
    }
}
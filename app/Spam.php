<?php

namespace App;

class Spam
{
    public function detect($text)
    {
        $this->detectInvalidKeywords($text);

        return false;
    }

    public function detectInvalidKeywords($text)
    {
        $spamKeywords = collect([
            'yahoo customer support',
            'google pays me',
            'nigerian prince',
        ]);

        $spamKeywords->each(function ($keyword) use ($text) {
            if (stripos($text, $keyword) !== false) {
                throw new \Exception('Your reply contains spam');
            }
        });
    }
}

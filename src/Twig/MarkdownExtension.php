<?php

namespace App\Twig;

use Parsedown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownExtension extends AbstractExtension
{
    private Parsedown $parsedown;
    
    public function __construct()
    {
        $this->parsedown = new Parsedown();
        $this->parsedown->setSafeMode(true);
    }
    
    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown_to_html', [$this, 'markdownToHtml'], ['is_safe' => ['html']]),
        ];
    }
    
    public function markdownToHtml(string $markdown): string
    {
        return $this->parsedown->text($markdown);
    }
}

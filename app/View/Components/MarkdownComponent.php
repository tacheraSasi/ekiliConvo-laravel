<?php

namespace App\View\Components;

use Illuminate\View\Component;
use League\CommonMark\CommonMarkConverter;

class MarkdownComponent extends Component
{
    public $markdown;

    public function __construct($markdown)
    {
        $this->markdown = $markdown;
    }

    public function render()
    {
        return view('components.markdown-component');
    }

    public function parsedMarkdown()
    {
        $converter = new CommonMarkConverter();
        return $converter->convertToHtml($this->markdown);
    }
}

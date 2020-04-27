<?php

namespace common\components\purifier\filter;

/**
 * Class FilterShortCodeToId
 *
 * @property string|null $content
 */
class FilterShortCodeToId implements Filter
{
    private $content;

    public function filter(?string $content): ?string
    {
        if ($content === null) {
            return null;
        }
        $this->content = $content;
        $this->processLead();
        $this->processCase();
        $this->processQaTask();
        return $this->content;
    }

    public function processLead(): void
    {
        $this->content = preg_replace_callback('|{lead-([\d]+)-([a-z0-9]+)}|iU', static function ($matches) {
            return $matches[1];
        }, $this->content);
    }

    public function processCase(): void
    {
        $this->content = preg_replace_callback('|{case-([\d]+)-([a-z0-9]+)}|iU', static function ($matches) {
            return $matches[1];
        }, $this->content);
    }

    public function processQaTask(): void
    {
        $this->content = preg_replace_callback('|{qa-task-([\d]+)-([a-z0-9]+)}|iU', static function ($matches) {
            return $matches[1];
        }, $this->content);
    }
}

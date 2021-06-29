<?php

namespace common\components\purifier\filter;

use yii\bootstrap4\Html;

/**
 * Class FilterShortCodeToId
 *
 * @property string|null $content
 */
class FilterShortCodeToId implements Filter
{
    private $content;

    /**
     * Ex. {case-10-qwerty} => 10
     *
     * @param string|null $content
     * @return string|null
     */
    public function filter(?string $content): ?string
    {
        if ($content === null) {
            return null;
        }
        $this->content = $content;
        $this->processLead();
        $this->processCase();
        $this->processQaTask();
        $this->processChat();
        $this->processSms();
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

    public function processChat(): void
    {
        $this->content = preg_replace_callback('|{chat-([\d]+)}|iU', static function ($matches) {
            return $matches[1];
        }, $this->content);
    }

    private function processSms(): void
    {
        $this->content = preg_replace_callback('|{sms-([\d]+)}|iU', function ($matches) {
            return $matches[1];
        }, $this->content);
    }
}

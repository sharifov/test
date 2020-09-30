<?php

namespace common\components\purifier\filter;

/**
 * Class FilterShortCodeToIdUrl
 *
 * @property string|null $content
 * @property string |null $host
 */
class FilterShortCodeToIdUrl implements Filter
{
    private $host;
    private $content;

    public function __construct()
    {
        $this->host = \Yii::$app->params['url_address'];
    }

    /**
     * Ex. {case-10-qwerty} => 10 https://....com/cases/view/qwerty
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
        return $this->content;
    }

    private function processLead(): void
    {
        $this->content = preg_replace_callback('|{lead-([\d]+)-([a-z0-9]+)}|iU', function ($matches) {
            return $matches[1] . ' ' . $this->host . '/lead/view/' . $matches[2];
        }, $this->content);
    }

    private function processCase(): void
    {
        $this->content = preg_replace_callback('|{case-([\d]+)-([a-z0-9]+)}|iU', function ($matches) {
            return $matches[1] . ' ' . $this->host . '/cases/view/' . $matches[2];
        }, $this->content);
    }

    private function processQaTask(): void
    {
        $this->content = preg_replace_callback('|{qa-task-([\d]+)-([a-z0-9]+)}|iU', function ($matches) {
            return $matches[1] . ' ' . $this->host . '/qa-task/qa-task/view?gid=' . $matches[2];
        }, $this->content);
    }

	private function processChat(): void
	{
		$this->content = preg_replace_callback('|{chat-([\d]+)}|iU', function ($matches) {
			return $this->host . '/client-chat/index?chid=' . $matches[1];
		}, $this->content);
	}
}

<?php

namespace common\components\purifier\filter;

use yii\bootstrap4\Html;

/**
 * Class FilterShortCodeToLink
 *
 * @property string|null $content
 * @property string |null $host
 */
class FilterShortCodeToLink implements Filter
{
    private $host;
    private $content;

    public function __construct()
    {
        $this->host = \Yii::$app->params['url_address'];
    }

    /**
     * Ex. {case-10-qwerty} => <a href="https://....com/cases/view/qwerty">10</a>
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
        $this->processNotification();
        return $this->content;
    }

    private function processLead(): void
    {
        $this->content = preg_replace_callback('|{lead-([\d]+)-([a-z0-9]+)}|iU', function ($matches) {
            return Html::a($matches[1], $this->host . '/lead/view/' . $matches[2]);
        }, $this->content);
    }

    private function processCase(): void
    {
        $this->content = preg_replace_callback('|{case-([\d]+)-([a-z0-9]+)}|iU', function ($matches) {
            return Html::a($matches[1], $this->host . '/cases/view/' . $matches[2]);
        }, $this->content);
    }

    private function processQaTask(): void
    {
        $this->content = preg_replace_callback('|{qa-task-([\d]+)-([a-z0-9]+)}|iU', function ($matches) {
            return Html::a($matches[1], $this->host . '/qa-task/qa-task/view?gid=' . $matches[2]);
        }, $this->content);
    }
    private function processNotification(): void
    {
        $this->content = preg_replace_callback('|id:([\d]+)|', function ($matches) {
            return Html::a($matches[1], $this->host . '/setting/view?id=' . $matches[1]);
        }, $this->content);

        $this->content = preg_replace_callback('/from(.*?)to/', function ($matches) {
            return '<br><pre><code>from: ' . $matches[1] . '</pre></code>to';
        }, $this->content);

        $this->content = preg_replace_callback('/to(.*?)by/', function ($matches) {
            return '<br><pre><code>to: ' . $matches[1] . '</pre></code>by';
        }, $this->content);
    }
}

<?php

namespace common\components\purifier\filter;

use src\helpers\phone\MaskPhoneHelper;
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
        $this->host = \Yii::$app->params['url'];
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
        $this->processChat();
        $this->processNotification();
        $this->processSms();
        $this->processCall();
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

    private function processChat(): void
    {
        $this->content = preg_replace_callback('|{chat-([\d]+)}|iU', function ($matches) {
            return Html::a('Chat Link', $this->host . '/client-chat/dashboard-v2?chid=' . $matches[1]);
        }, $this->content);
    }
    private function processNotification(): void
    {
        $this->content = preg_replace_callback('|id:([\d]+)|', function ($matches) {
            return Html::a($matches[1], $this->host . '/setting/view?id=' . $matches[1]);
        }, $this->content);

        $this->content = preg_replace_callback('/from(.*?)to/', function ($matches) {
            $detectPhone = self::detectPhones($matches[1]);
            if ($detectPhone !== $matches[1]) {
                return '<br><pre><code>from: ' . $detectPhone . '</pre></code>to';
            }
            return $matches[0];
        }, $this->content);

        $this->content = preg_replace_callback('/to(.*?)by/', function ($matches) {
            return '<br><pre><code>to: ' . $matches[1] . '</pre></code>by';
        }, $this->content);
    }

    private function detectPhones(string $phone): string
    {
        if (preg_match("/^\+[1-9]\d{6,13}$/", trim($phone))) {
            return MaskPhoneHelper::masking($phone);
        }
        return $phone;
    }

    private function processSms(): void
    {
        $this->content = preg_replace_callback('|{sms-([\d]+)}|iU', function ($matches) {
            return Html::a($matches[1], $this->host . '/sms/view2?id=' . $matches[1]);
        }, $this->content);
    }

    private function processCall(): void
    {
        $this->content = preg_replace_callback('|{call-([\d]+)}|iU', function ($matches) {
            return Html::a($matches[1], $this->host . '/call/view?id=' . $matches[1]);
        }, $this->content);
    }
}

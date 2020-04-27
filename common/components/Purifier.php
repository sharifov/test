<?php

namespace common\components;

use common\models\Lead;
use modules\qaTask\src\entities\qaTask\QaTask;
use sales\entities\cases\Cases;
use yii\bootstrap4\Html;

/**
 * Class Content
 *
 * Lead Filter: {lead-id-gid} => <a href="/lead/view/gid">id</a>
 * Case Filter: {case-id-gid} => <a href="/cases/view/gid">id</a>
 * Case Filter: {qa-task-id-gid} => <a href="/qa-task/qa-task/view?gid=gid">id</a>
 *
 * @property string|null $content
 * @property string $host
 */
class Purifier
{
    private $content;
    private $host;

    public function __construct(?string $content)
    {
        $this->host = \Yii::$app->params['url_address'];
        $this->content = $content;
    }

    public static function purify(?string $content): ?string
    {
        if ($content === null) {
            return null;
        }
        $filter = new self($content);
        $filter->replaceShortCodesToLink();
        return $filter->getContent();
    }

    public static function replaceCodesToId(?string $content): ?string
    {
        if ($content === null) {
            return null;
        }
        $filter = new self($content);
        $filter->replaceShortCodesToId();
        return $filter->getContent();
    }

    public function replaceShortCodesToLink(): void
    {
        $this->replaceShortCodeToLeadLink();
        $this->replaceShortCodeToCaseLink();
        $this->replaceShortCodeToQaTaskLink();
    }

    public function replaceShortCodesToId(): void
    {
        $this->replaceShortCodeToLeadId();
        $this->replaceShortCodeToCaseId();
        $this->replaceShortCodeToQaTaskId();
    }

    public static function createLeadShortLink(Lead $lead): string
    {
        return '{lead-' . $lead->id . '-' . $lead->gid . '}';
    }

    public static function createCaseShortLink(Cases $cases): string
    {
        return '{case-' . $cases->cs_id . '-' . $cases->cs_gid . '}';
    }

    public static function createQaTaskShortLink(QaTask $task): string
    {
        return '{qa-task-' . $task->t_id . '-' . $task->t_gid . '}';
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function replaceShortCodeToLeadLink(): void
    {
        $this->content = preg_replace_callback('|{lead-([\d]+)-([a-z0-9]+)}|iU', function ($matches) {
            return Html::a($matches[1], $this->host . '/lead/view/' . $matches[2]);
        }, $this->content);
    }

    public function replaceShortCodeToLeadId(): void
    {
        $this->content = preg_replace_callback('|{lead-([\d]+)-([a-z0-9]+)}|iU', static function ($matches) {
            return $matches[1];
        }, $this->content);
    }

    public function replaceShortCodeToCaseLink(): void
    {
        $this->content = preg_replace_callback('|{case-([\d]+)-([a-z0-9]+)}|iU', function ($matches) {
            return Html::a($matches[1], $this->host . '/cases/view/' . $matches[2]);
        }, $this->content);
    }

    public function replaceShortCodeToCaseId(): void
    {
        $this->content = preg_replace_callback('|{case-([\d]+)-([a-z0-9]+)}|iU', static function ($matches) {
            return $matches[1];
        }, $this->content);
    }

    public function replaceShortCodeToQaTaskLink(): void
    {
        $this->content = preg_replace_callback('|{qa-task-([\d]+)-([a-z0-9]+)}|iU', function ($matches) {
            return Html::a($matches[1], $this->host . '/qa-task/qa-task/view?gid=' . $matches[2]);
        }, $this->content);
    }

    public function replaceShortCodeToQaTaskId(): void
    {
        $this->content = preg_replace_callback('|{qa-task-([\d]+)-([a-z0-9]+)}|iU', static function ($matches) {
            return $matches[1];
        }, $this->content);
    }
}

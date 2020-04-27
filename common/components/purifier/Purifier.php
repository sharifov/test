<?php

namespace common\components\purifier;

use common\components\purifier\filter\Filter;
use common\models\Lead;
use modules\qaTask\src\entities\qaTask\QaTask;
use sales\entities\cases\Cases;

/**
 * Class Purifier
 *
 */
class Purifier
{
    public static function purify(?string $content, Filter ...$filters): ?string
    {
        if ($content === null) {
            return null;
        }

        if (!$filters) {
            $filters[] = PurifierFilter::shortCodeToLink();
        }

        foreach ($filters as $filter) {
            $content = $filter->filter($content);
        }

        return $content;
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
}

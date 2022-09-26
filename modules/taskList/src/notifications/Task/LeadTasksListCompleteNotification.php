<?php

namespace modules\taskList\src\notifications\Task;

/**
 * {@inheritdoc}
 */
class LeadTasksListCompleteNotification extends AbstractLeadTaskListListNotification
{
    protected const NOTIFY_TYPE = 'complete';
}

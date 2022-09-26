<?php

namespace modules\taskList\src\notifications\Task;

/**
 * {@inheritdoc}
 */
class LeadTasksListSavedNotification extends AbstractLeadTaskListListNotification
{
    protected const NOTIFY_TYPE = 'saved';
}

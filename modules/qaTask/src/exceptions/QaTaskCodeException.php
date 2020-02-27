<?php

namespace modules\qaTask\src\exceptions;

use common\CodeExceptionsModule as Module;

class QaTaskCodeException
{
    public const QA_TASK_NOT_FOUND = Module::QA_TASK . 100;
    public const QA_TASK_SAVE = Module::QA_TASK . 101;
    public const QA_TASK_REMOVE = Module::QA_TASK . 102;

    public const QA_TASK_CATEGORY_NOT_FOUND = Module::QA_TASK . 200;
    public const QA_TASK_CATEGORY_SAVE = Module::QA_TASK . 201;
    public const QA_TASK_CATEGORY_REMOVE = Module::QA_TASK . 202;

    public const QA_TASK_STATUS_NOT_FOUND = Module::QA_TASK . 300;
    public const QA_TASK_STATUS_SAVE = Module::QA_TASK . 301;
    public const QA_TASK_STATUS_REMOVE = Module::QA_TASK . 302;

    public const QA_TASK_ACTION_REASON_NOT_FOUND = Module::QA_TASK . 400;
    public const QA_TASK_ACTION_REASON_SAVE = Module::QA_TASK . 401;
    public const QA_TASK_ACTION_REASON_REMOVE = Module::QA_TASK . 402;

    public const QA_TASK_STATUS_LOG_SAVE = Module::QA_TASK . 500;
    public const QA_TASK_STATUS_LOG_REMOVE = Module::QA_TASK . 501;
}

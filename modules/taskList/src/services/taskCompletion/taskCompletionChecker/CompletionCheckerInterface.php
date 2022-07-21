<?php

namespace modules\taskList\src\services\taskCompletion\taskCompletionChecker;

interface CompletionCheckerInterface
{
    public function check(): bool;
}

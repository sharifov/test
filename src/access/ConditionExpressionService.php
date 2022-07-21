<?php

namespace src\access;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ConditionExpressionService
{
    public static function isValidCondition(string $condition, array $data): bool
    {
        return (bool) (new ExpressionLanguage())->evaluate($condition, $data);
    }
}

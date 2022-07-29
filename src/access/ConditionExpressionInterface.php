<?php

namespace src\access;

interface ConditionExpressionInterface
{
    public const OP_EQUAL               = 'equal';
    public const OP_NOT_EQUAL           = 'not_equal';
    public const OP_IN                  = 'in';
    public const OP_NOT_IN              = 'not_in';
    public const OP_IN_ARRAY            = 'in_array';
    public const OP_NOT_IN_ARRAY        = 'not_in_array';
    public const OP_LESS                = 'less';
    public const OP_LESS_OR_EQUAL       = 'less_or_equal';
    public const OP_GREATER             = 'greater';
    public const OP_GREATER_OR_EQUAL    = 'greater_or_equal';
    public const OP_BETWEEN             = 'between';
    public const OP_NOT_BETWEEN         = 'not_between';
    public const OP_BEGINS_WITH         = 'begins_with';
    public const OP_NOT_BEGINS_WITH     = 'not_begins_with';
    public const OP_CONTAINS            = 'contains';
    public const OP_NOT_CONTAINS        = 'not_contains';
    public const OP_ENDS_WITH           = 'ends_with';
    public const OP_NOT_ENDS_WITH       = 'not_ends_with';
    public const OP_IS_EMPTY            = 'is_empty';
    public const OP_IS_NOT_EMPTY        = 'is_not_empty';
    public const OP_IS_NULL             = 'is_null';
    public const OP_IS_NOT_NULL         = 'is_not_null';
    public const OP_MATCH               = 'match';
    public const OP_EQUAL2              = '==';
    public const OP_NOT_EQUAL2          = '!=';
}

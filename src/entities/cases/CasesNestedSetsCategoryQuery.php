<?php

namespace src\entities\cases;

use creocoder\nestedsets\NestedSetsQueryBehavior;

class CasesNestedSetsCategoryQuery extends \yii\db\ActiveQuery
{
    public function behaviors(): array
    {
        return [
            NestedSetsQueryBehavior::class,
        ];
    }
}

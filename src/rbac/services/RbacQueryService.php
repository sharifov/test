<?php

namespace src\rbac\services;

use yii\db\Query;
use yii\rbac\Item;
use yii2mod\rbac\models\AuthItemModel;
use yii2mod\rbac\models\search\AuthItemSearch;

class RbacQueryService
{
    public static function getRoleByName(string $name)
    {
        return AuthItemModel::find($name);
    }

    public static function getRolesList()
    {
        return (new Query())
            ->select('a.name')
            ->from(['a' => 'auth_item'])
            ->where(['a.type' => Item::TYPE_ROLE])
            ->column();
    }
}

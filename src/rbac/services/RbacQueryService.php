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

    public static function getRolesListWithPermissionsCount()
    {
        $columns =  (new Query())
            ->select(['a.name', 'count(*)'])
            ->from(['a' => 'auth_item'])
            ->where(['a.type' => Item::TYPE_ROLE])
            ->innerJoin('auth_item_child aic', 'aic.parent = a.name')
            ->groupBy('a.name')
            ->all();
        $result = [];
        foreach ($columns as $column) {
            $result[$column['name']] = $column['name'] . '  - permissions count  - ' . $column['count(*)'];
        }
        return $result;
    }
}

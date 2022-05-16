<?php

namespace common\models;

use Yii;
use yii\caching\TagDependency;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for "user roles".
 *
 */
class UserRole
{
    public const CACHE_KEY = 'user_role';
    public const CACHE_TAG_DEPENDENCY = 'user_role-tag-dependency';

    /**
     * @return array
     */
    public static function getEnvListWOCache(): array
    {
        $query = (new Query())->select(['name', 'CONCAT(description, " (", name, ")") as description_name'])
            ->from('auth_item')
            ->where(['type' => 1]);

        return ArrayHelper::map($query->all(), 'name', 'description_name');
    }


    /**
     * @return array
     */
    public static function getEnvList(): array
    {
        if (self::CACHE_KEY) {
            $list = Yii::$app->cache->get(self::CACHE_KEY);
            if ($list === false) {
                $list = self::getEnvListWOCache();

                Yii::$app->cache->set(
                    self::CACHE_KEY,
                    $list,
                    0,
                    new TagDependency(['tags' => self::CACHE_TAG_DEPENDENCY])
                );
            }
        } else {
            $list = self::getEnvListWOCache();
        }
        return $list;
    }
}

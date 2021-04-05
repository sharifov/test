<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "log".
 *
 * @property int $id
 * @property int $level
 * @property string $category
 * @property double $log_time
 * @property string $prefix
 * @property string $message
 */
class Log extends \yii\db\ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * @return object|\yii\db\Connection|null
     * @throws \yii\base\InvalidConfigException
     */
    public static function getDb()
    {
        return \Yii::$app->get('db_postgres');
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['level', 'id'], 'integer'],
            [['log_time'], 'number'],
            [['prefix', 'message'], 'string'],
            [['category'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'level' => 'Level',
            'category' => 'Category',
            'log_time' => 'Log Time',
            'prefix' => 'Prefix',
            'message' => 'Message',
        ];
    }

    /**
     * @inheritdoc
     * @return LogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LogQuery(get_called_class());
    }

    /**
     * @param int|null $level
     * @param bool $countGroup
     * @return array
     */
    public static function getCategoryFilter(?int $level = null, bool $countGroup = false): array
    {
        $arr = [];

        if ($countGroup) {
            $query = self::find()->select(["COUNT(*) AS cnt", "category"])
                ->where('category IS NOT NULL')
                ->groupBy(["category"])
                ->orderBy('cnt DESC')
                ->cache(60)
                ->asArray();

            if ($level) {
                $query->andWhere(['level' => (int) $level]);
            }

            $data = $query->all();

            if ($data) {
                foreach ($data as $v) {
                    $arr[$v['category']] = $v['category'] . ' - [' . $v['cnt'] . ']';
                }
            }
        } else {
            $query = self::find()->select("DISTINCT(category) AS category")
                ->cache(60)
                ->orderBy('category')
                ->asArray();

            if ($level) {
                $query->andWhere(['level' => (int) $level]);
            }

            $data = $query->all();

            if ($data) {
                foreach ($data as $v) {
                    $arr[$v['category']] = $v['category'];
                }
            }
        }

        return $arr;
    }

    /**
     * @return array
     */
    public static function getCategoryFilterByCnt(): array
    {
        $arr = [];
        $data = self::find()->select(["COUNT(*) AS cnt", "category"])
            ->where('category IS NOT NULL')
            ->groupBy(["category"])
            ->orderBy('cnt DESC')
            ->cache(60)
            ->asArray()->all();

        if ($data) {
            foreach ($data as $v) {
                $arr[] = [
                    'hash' => md5($v['category']),
                    'name' => $v['category'],
                    'cnt' => $v['cnt']
                ];
            }
        }

        return $arr;
    }

    /**
     * @param null $condition
     * @param array $params
     * @return int
     */
    public static function deleteAll($condition = null, $params = []): int
    {
        $command = self::getDb()->createCommand();
        $command->delete(self::tableName(), $condition, $params);

        return $command->execute();
    }


    /**
     * @param $model Log
     * @return int|string
     */
    public static function removeSysLogs($model)
    {
        $beforeDelete = self::find()->count();

        if ($model->level != '' && $model->category == '' && $model->days == '') {
            self::deleteAll('level = :l', [':l' => $model->level]);
        }
        if ($model->level == '' && $model->category != '' && $model->days == '') {
            self::deleteAll('category = :c', [':c' => $model->category]);
        }
        if ($model->level == '' && $model->category == '' && $model->days != '') {
            self::deleteAll('log_time <= :d', [':d' => strtotime('-' . $model->days . ' day')]);
        }
        if ($model->level != '' && $model->category != '' && $model->days == '') {
            self::deleteAll(['AND','level = :l', 'category = :c'], [':l' => $model->level, ':c' => $model->category]);
        }
        if ($model->level != '' && $model->category == '' && $model->days != '') {
            self::deleteAll(['AND','level = :l', 'log_time <= :d'], [':l' => $model->level, ':d' => strtotime('-' . $model->days . ' day')]);
        }
        if ($model->level == '' && $model->category != '' && $model->days != '') {
            self::deleteAll(['AND','category = :c', 'log_time <= :d'], [':c' => $model->category, ':d' => strtotime('-' . $model->days . ' day')]);
        }
        if ($model->level != '' && $model->category != '' && $model->days != '') {
            self::deleteAll('level = :l AND category = :c AND log_time <= :d', [':l' => $model->level, ':c' => $model->category, ':d' => strtotime('-' . $model->days . ' day')]);
        }
        $afterDelete = self::find()->count();

        return $beforeDelete - $afterDelete;
    }
}

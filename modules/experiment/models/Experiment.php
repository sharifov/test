<?php

namespace modules\experiment\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "experiment".
 * @todo Maybe is need to add created_date and created_user
 * @property int $ex_id
 * @property string $ex_code
 *
 * @property ExperimentTarget[] $experimentTargets
 */
class Experiment extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'experiment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['ex_code'], 'required'],
            [['ex_code'], 'string', 'max' => 255],
            [['ex_code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'ex_id'   => Yii::t('experiment-crud', 'ID'),
            'ex_code' => Yii::t('experiment-crud', 'Code'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getExperimentTargets(): ActiveQuery
    {
        return $this->hasMany(ExperimentTarget::class, ['ext_experiment_id' => 'ex_id']);
    }

    /**
     * @param string $code
     * @return ActiveRecord|array|null
     */
    public static function getExperimentByCode(string $code)
    {
        return self::find()->where(['ex_code' => $code])->limit(1)->one();
    }

    /**
     * @param string $targetType
     * @param int $targetId
     * @return bool
     */
    public function addTarget(string $targetType, int $targetId): bool
    {
        if (
            !$this->getExperimentTargets()->where([
                                                      'ext_target_type' => $targetType,
                                                      'ext_target_id'   => $targetId
                                                  ])->exists()
        ) {
            $experimentTarget = new ExperimentTarget([
                                                         'ext_target_type'   => $targetType,
                                                         'ext_target_id'     => $targetId,
                                                         'ext_experiment_id' => $this->ex_id
                                                     ]);
            return $experimentTarget->save();
        }
        return true;
    }
}

<?php

namespace modules\experiment\models;

use src\model\leadData\entity\LeadData;
use src\traits\FieldsTrait;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "experiment".
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
            'ex_id'   => 'ID',
            'ex_code' => 'Code',
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
     * @return array|Experiment|ActiveRecord|null
     */
    public static function getExperimentByCode(string $code)
    {
        return self::find()->where(['ex_code' => $code])->limit(1)->one();
    }

    /**
     * @param int $ex_id
     * @return array|Experiment|ActiveRecord|null
     */
    public static function getExperimentById(int $ex_id)
    {
        return self::find()->where(['ex_id' => $ex_id])->limit(1)->one();
    }

    /**
     * @param string $targetTypeId
     * @param int $targetId
     * @return bool
     */
    public function saveTarget(string $targetTypeId, int $targetId): bool
    {
        $experimentTarget = new ExperimentTarget([
                                                     'ext_target_type_id' => $targetTypeId,
                                                     'ext_target_id'      => $targetId,
                                                     'ext_experiment_id'  => $this->ex_id,
                                                     'ext_created_dt'     => date('Y-m-d H:i:s')
                                                 ]);
        return $experimentTarget->save();
    }
}

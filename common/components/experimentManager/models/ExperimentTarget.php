<?php

declare(strict_types=1);

namespace common\components\experimentManager\models;

use common\models\Lead;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "experiment_target".
 * * @todo Maybe is need to add created_date and created_user
 *
 * @property int $ext_id
 * @property int $ext_target_id
 * @property string $ext_target_type
 * @property int $ext_experiment_id
 *
 * @property Experiment $experiment
 */
class ExperimentTarget extends ActiveRecord
{
    public const EXT_TYPE_LEAD = 'common\models\Lead';
    public const EXT_TYPE_CASE = 'src\entities\cases\Cases';
    public const EXT_TYPE_CHAT = 'src\model\clientChat\entity\ClientChat';
    public const EXT_TYPE_CALL = 'common\models\Call';
    public const EXT_TYPE_LIST = [
        self::EXT_TYPE_LEAD           => 'Lead',
        self::EXT_TYPE_CASE           => 'Case',
        self::EXT_TYPE_CHAT           => 'Chat',
        self::EXT_TYPE_CALL           => 'Call'
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'experiment_target';
    }
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['ext_target_id', 'ext_target_type', 'ext_experiment_id'], 'required'],
            [['ext_target_id', 'ext_experiment_id'], 'integer'],
            [['ext_target_type'], 'string', 'max' => 255],
            [['ext_target_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['ext_target_id' => 'id'], 'message' => 'Target object instance (with this ID and type) not found in DB'],
            [['ext_experiment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Experiment::class, 'targetAttribute' => ['ext_experiment_id' => 'ex_id']],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'ext_id' => Yii::t('experiment-manager', 'ID'),
            'ext_target_id' => Yii::t('experiment-manager', 'Target instance ID'),
            'ext_target_type' => Yii::t('experiment-manager', 'Target instance Type'),
            'ext_experiment_id' => Yii::t('experiment-manager', 'Experiment ID'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getExperiment(): ActiveQuery
    {
        return $this->hasOne(Experiment::class, ['ex_id' => 'ext_experiment_id']);
    }

    /**
     * @return array
     */
    public static function getList(): array
    {
        return self::EXT_TYPE_LIST;
    }
}

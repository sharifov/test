<?php

namespace modules\experiment\models;

use common\models\Lead;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "experiment_target".
 *
 * @property int $ext_id
 * @property int $ext_target_id
 * @property string $ext_target_type_id
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
    public const EXT_TYPE_CALL_LOG = 'common\models\CallLog';
    public const EXT_TYPE_LIST = [
        1           => 'Lead',
        2           => 'Case',
        3           => 'Chat',
        4           => 'Call',
        5           => 'Call log'
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
            [['ext_target_id', 'ext_target_type_id', 'ext_experiment_id'], 'required'],
            [['ext_target_id', 'ext_experiment_id'], 'integer'],
            [['ext_target_type_id'], 'integer', 'max' => 255],
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
            'ext_target_type_id' => Yii::t('experiment-manager', 'Target instance Type'),
            'ext_experiment_id' => Yii::t('experiment-manager', 'Experiment'),
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

    /**
     * @return array
     */
    public static function saveExperimentList(string $class, int $targetId, array $experimentCodes = []): void
    {
        $mergedExperimentCodes = array_unique(array_column($experimentCodes, 'cross_ex_code'));
        foreach ($mergedExperimentCodes as $ex_code) {
            if ($ex_code != '') {
                self::saveExperiment($class, $targetId, $ex_code);
            }
        }
    }

    public static function saveExperiment(string $class, int $targetId, string $experimentCode): void
    {
        $experimentRecord = Experiment::getExperimentByCode($experimentCode);
        if (empty($experimentRecord)) {
            $experimentRecord = new Experiment(['ex_code' => $experimentCode]);
            $experimentRecord->save();
        }
        $experimentRecord->addTarget($class, $targetId);
    }
}

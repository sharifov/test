<?php

namespace modules\experiment\models;

use common\models\Lead;
use src\entities\cases\Cases;
use src\model\clientChat\entity\ClientChat;
use common\models\Call;
use common\models\CallLog;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "experiment_target".
 *
 * @property int $ext_id
 * @property int $ext_target_id
 * @property int $ext_target_type_id
 * @property int $ext_experiment_id
 *
 * @property Experiment $experiment
 */
class ExperimentTarget extends ActiveRecord
{
    public const EXT_TYPE_LEAD     = 1;
    public const EXT_TYPE_CASE     = 2;
    public const EXT_TYPE_CHAT     = 3;
    public const EXT_TYPE_CALL     = 4;
    public const EXT_TYPE_CALL_LOG = 5;
    public const EXT_TYPE_LIST = [
        EXT_TYPE_LEAD     => 'Lead',
        EXT_TYPE_CASE     => 'Case',
        EXT_TYPE_CHAT     => 'Chat',
        EXT_TYPE_CALL     => 'Call',
        EXT_TYPE_CALL_LOG => 'Call log'
    ];
    public const EXT_TYPE_NAMESPACES = [
        EXT_TYPE_LEAD     => Lead::class,
        EXT_TYPE_CASE     => Cases::class,
        EXT_TYPE_CHAT     => ClientChat::class,
        EXT_TYPE_CALL     => Call::class,
        EXT_TYPE_CALL_LOG => CallLog::class
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
            [['ext_experiment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Experiment::class, 'targetAttribute' => ['ext_experiment_id' => 'ex_id']],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'ext_id' => 'ID',
            'ext_target_id' => 'Target object ID',
            'ext_target_type_id' => 'Target object Type',
            'ext_experiment_id' => 'Experiment',
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
    public static function saveExperimentList(string $target_type_id, int $targetId, array $experimentCodes = []): void
    {
        $mergedExperimentCodes = array_unique(array_column($experimentCodes, 'cross_ex_code'));
        foreach ($mergedExperimentCodes as $ex_code) {
            if ($ex_code != '') {
                self::saveExperiment($target_type_id, $targetId, $ex_code);
            }
        }
    }

    public static function saveExperiment(string $target_type_id, int $targetId, string $experimentCode): void
    {
        $experimentRecord = Experiment::getExperimentByCode($experimentCode);
        if (empty($experimentRecord)) {
            $experimentRecord = new Experiment(['ex_code' => $experimentCode]);
            $experimentRecord->save();
        }
        $experimentRecord->addTarget($target_type_id, $targetId);
    }
}

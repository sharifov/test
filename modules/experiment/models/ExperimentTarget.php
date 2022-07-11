<?php

namespace modules\experiment\models;

use common\models\Lead;
use src\entities\cases\Cases;
use src\model\clientChat\entity\ClientChat;
use src\model\phoneList\entity\PhoneList;
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
    public const EXT_TYPE_PHONE_LIST   = 4;

    public const EXT_TYPE_LIST = [
        self::EXT_TYPE_LEAD         => 'Lead',
        self::EXT_TYPE_CASE         => 'Case',
        self::EXT_TYPE_CHAT         => 'Chat',
        self::EXT_TYPE_PHONE_LIST   => 'Phone List'
    ];
    public const EXT_TYPE_NAMESPACES = [
        self::EXT_TYPE_LEAD           => Lead::class,
        self::EXT_TYPE_CASE           => Cases::class,
        self::EXT_TYPE_CHAT           => ClientChat::class,
        self::EXT_TYPE_PHONE_LIST     => PhoneList::class
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
            'ext_experiment_id' => 'Experiment ID',
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
     * @return string[]
     */
    public static function getTypeList(): array
    {
        return self::EXT_TYPE_LIST;
    }

    /**
     * @param int $target_type_id
     * @param int $targetId
     * @return array
     */
    public static function getExperimentIds(int $target_type_id, int $targetId): array
    {
        $targetExperiments = self::find()
                                 ->select('ex_id')
                                 ->where(['ext_target_type_id' => $target_type_id, 'ext_target_id' => $targetId])->asArray()->all();
        $experiment_array  = [];
        foreach ($targetExperiments as $experiment) {
            $experiment_array[] = $experiment->ext_experiment_id;
        }
        return $experiment_array;
    }

    /**
     * @param int $target_type_id
     * @param int $targetId
     * @return array
     */
    public static function getExperimentObjects(int $target_type_id, int $targetId): array
    {
        $targetExperiments = self::find()
                             ->select('ex_code')
                             ->joinWith(Experiment::tableName())
                             ->where(['ext_target_type_id' => $target_type_id, 'ext_target_id' => $targetId])->asArray()->all();
        $experiment_array = [];
        foreach ($targetExperiments as $experiment) {
            $experiment_array[] = ['ex_code' => $experiment['ex_code']];
        }

        return $experiment_array;
    }

    /**
     * @param int $target_type_id
     * @param int $targetId
     * @param array|null $experimentObjects
     * @return void
     */
    public static function processExperimentObjects(int $target_type_id, int $targetId, ?array $experimentObjects): void
    {
        if (!empty($experimentObjects)) {
            self::processExperimentsCodes($target_type_id, $targetId, array_unique(array_column($experimentObjects, 'ex_code')));
        }
    }

    /**
     * @param int $target_type_id
     * @param int $targetId
     * @param array $experimentsCodesArray
     * @return void
     */
    public static function processExperimentsCodes(int $target_type_id, int $targetId, array $experimentsCodesArray): void
    {
        foreach ($experimentsCodesArray as $experimentCode) {
            $experimentRecord = Experiment::getExperimentByCode($experimentCode);
            if (empty($experimentRecord)) {
                $experimentRecord = new Experiment(['ex_code' => $experimentCode]);
                $experimentRecord->save();
            }
            $experimentRecord->saveTarget($target_type_id, $targetId);
        }
    }
}

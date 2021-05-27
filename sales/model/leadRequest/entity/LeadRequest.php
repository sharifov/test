<?php

namespace sales\model\leadRequest\entity;

use common\components\validators\CheckJsonValidator;
use common\models\Project;
use sales\entities\EventTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "lead_request".
 *
 * @property int $lr_id
 * @property string $lr_type
 * @property int|null $lr_job_id
 * @property string|null $lr_json_data
 * @property string|null $lr_created_dt
 * @property int $lr_project_id
 * @property int $lr_source_id
 */
class LeadRequest extends \yii\db\ActiveRecord
{
    use EventTrait;

    public const TYPE_GOOGLE = 'google';

    public const TYPE_LIST = [
        self::TYPE_GOOGLE => 'Google'
    ];

    public function rules(): array
    {
        return [
            [['lr_type', 'lr_project_id'], 'required'],
            ['lr_type', 'string', 'max' => 50],
            ['lr_type', 'in', 'range' => array_keys(self::TYPE_LIST)],

            ['lr_job_id', 'integer'],

            ['lr_project_id', 'integer'],
            ['lr_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['lr_project_id' => 'id']],

            ['lr_source_id', 'integer'],

            ['lr_json_data', CheckJsonValidator::class, 'skipOnEmpty' => true],

            ['lr_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['lr_created_dt', 'lr_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['lr_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function attributeLabels(): array
    {
        return [
            'lr_id' => 'ID',
            'lr_type' => 'Type',
            'lr_job_id' => 'Job ID',
            'lr_json_data' => 'Json Data',
            'lr_created_dt' => 'Created Dt',
            'lr_project_id' => 'Project',
            'lr_source_id' => 'Source'
        ];
    }

    public static function find(): LeadRequestScopes
    {
        return new LeadRequestScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'lead_request';
    }

    /**
     * @param string $type
     * @param int $projectId
     * @param int $sourceId
     * @param $jsonData
     * @return LeadRequest
     */
    public static function create(
        string $type,
        int $projectId,
        int $sourceId,
        $jsonData
    ): LeadRequest {
        $model = new self();
        $model->lr_type = $type;
        $model->lr_project_id = $projectId;
        $model->lr_source_id = $sourceId;
        $model->lr_json_data = $jsonData;
        return $model;
    }

    public function setJobId(?int $lr_job_id): LeadRequest
    {
        $this->lr_job_id = $lr_job_id;
        return $this;
    }
}

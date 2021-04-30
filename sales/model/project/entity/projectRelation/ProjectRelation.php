<?php

namespace sales\model\project\entity\projectRelation;

use common\models\Employee;
use common\models\Project;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "project_relation".
 *
 * @property int $prl_project_id
 * @property int $prl_related_project_id
 * @property int|null $prl_created_user_id
 * @property int|null $prl_updated_user_id
 * @property string|null $prl_created_dt
 * @property string|null $prl_updated_dt
 *
 * @property Project $prlProject
 * @property Project $prlRelatedProject
 * @property Employee $prlCreatedUser
 * @property Employee $prlUpdatedUser
 */
class ProjectRelation extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['prl_project_id', 'prl_related_project_id'], 'unique', 'targetAttribute' => ['prl_project_id', 'prl_related_project_id']],

            ['prl_project_id', 'required'],
            ['prl_project_id', 'integer'],
            ['prl_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['prl_project_id' => 'id']],

            ['prl_related_project_id', 'required'],
            ['prl_related_project_id', 'integer'],
            ['prl_related_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['prl_related_project_id' => 'id']],

            ['prl_project_id', 'compare', 'compareAttribute' => 'prl_related_project_id', 'operator' => '!='],

            [['prl_created_user_id', 'prl_updated_user_id'], 'integer'],
            ['prl_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['prl_created_user_id' => 'id']],
            ['prl_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['prl_created_user_id' => 'id']],

            [['prl_created_dt', 'prl_updated_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['prl_created_dt', 'prl_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['prl_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['prl_created_user_id', 'prl_updated_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['prl_updated_user_id'],
                ]
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getPrlProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'prl_project_id']);
    }

    public function getPrlRelatedProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'prl_related_project_id']);
    }

    public function getPrlCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'prl_created_user_id']);
    }

    public function getPrlUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'prl_updated_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'prl_project_id' => 'Project',
            'prl_related_project_id' => 'Related Project',
            'prl_created_user_id' => 'Created User',
            'prl_updated_user_id' => 'Updated User',
            'prl_created_dt' => 'Created Dt',
            'prl_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): ProjectRelationScopes
    {
        return new ProjectRelationScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'project_relation';
    }

    public static function create(int $projectId, int $relatedId): ProjectRelation
    {
        $model = new self();
        $model->prl_project_id = $projectId;
        $model->prl_related_project_id = $relatedId;
        return $model;
    }
}

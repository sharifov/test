<?php

namespace src\model\phoneNumberRedial\entity;

use common\models\Employee;
use common\models\Project;
use src\model\phoneList\entity\PhoneList;
use src\model\phoneNumberRedial\entity\Scopes\PhoneNumberRedialQuery;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "phone_number_redial".
 *
 * @property int $pnr_id
 * @property int $pnr_project_id
 * @property string $pnr_phone_pattern
 * @property int $pnr_pl_id
 * @property string|null $pnr_name
 * @property int|null $pnr_enabled
 * @property int|null $pnr_priority
 * @property string|null $pnr_created_dt
 * @property string|null $pnr_updated_dt
 * @property int|null $pnr_updated_user_id
 *
 * @property PhoneList $phoneList
 * @property Project $pnrProject
 * @property Employee $pnrUpdatedUser
 */
class PhoneNumberRedial extends \yii\db\ActiveRecord
{
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pnr_created_dt', 'pnr_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pnr_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'attribute' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pnr_updated_user_id'],
                ],
                'value' => \Yii::$app->user->id ?? null,
            ],
        ];
    }

    public function rules(): array
    {
        return [
            ['pnr_created_dt', 'safe'],

            ['pnr_enabled', 'integer'],

            ['pnr_name', 'string', 'max' => 255],

            ['pnr_phone_pattern', 'required'],
            ['pnr_phone_pattern', 'string', 'max' => 30],

            ['pnr_pl_id', 'required'],
            ['pnr_pl_id', 'integer'],
            ['pnr_pl_id', 'exist', 'skipOnError' => true, 'targetClass' => PhoneList::class, 'targetAttribute' => ['pnr_pl_id' => 'pl_id']],

            ['pnr_priority', 'integer'],

            ['pnr_project_id', 'required'],
            ['pnr_project_id', 'integer'],
            ['pnr_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['pnr_project_id' => 'id']],

            ['pnr_updated_dt', 'safe'],

            ['pnr_updated_user_id', 'integer'],
            ['pnr_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pnr_updated_user_id' => 'id']],
        ];
    }

    public function getPhoneList(): \yii\db\ActiveQuery
    {
        return $this->hasOne(PhoneList::class, ['pl_id' => 'pnr_pl_id']);
    }

    public function getPnrProject(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'pnr_project_id']);
    }

    public function getPnrUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pnr_updated_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'pnr_id' => 'ID',
            'pnr_project_id' => 'Project ID',
            'pnr_phone_pattern' => 'Phone Pattern',
            'pnr_pl_id' => 'Phone List ID',
            'pnr_name' => 'Name',
            'pnr_enabled' => 'Enabled',
            'pnr_priority' => 'Priority',
            'pnr_created_dt' => 'Created Dt',
            'pnr_updated_dt' => 'Updated Dt',
            'pnr_updated_user_id' => 'Updated User ID',
        ];
    }

    public static function find(): PhoneNumberRedialQuery
    {
        return new PhoneNumberRedialQuery(static::class);
    }

    public static function tableName(): string
    {
        return 'phone_number_redial';
    }

    public static function create(
        int $projectId,
        ?string $name,
        string $pattern,
        int $phoneId,
        ?int $priority,
        ?int $enabled
    ): self {
        $model = new self();
        $model->pnr_project_id = $projectId;
        $model->pnr_name = $name;
        $model->pnr_phone_pattern = $pattern;
        $model->pnr_pl_id = $phoneId;
        $model->pnr_priority = $priority;
        $model->pnr_enabled = $enabled;
        return $model;
    }
}

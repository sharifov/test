<?php

namespace common\models;

use common\components\CommunicationService;
use common\models\query\SmsTemplateTypeQuery;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "sms_template_type".
 *
 * @property int $stp_id
 * @property string $stp_key
 * @property string $stp_origin_name
 * @property string $stp_name
 * @property int $stp_hidden
 * @property int $stp_created_user_id
 * @property int $stp_updated_user_id
 * @property string $stp_created_dt
 * @property string $stp_updated_dt
 * @property int $stp_dep_id
 *
 * @property Sms[] $sms
 * @property Employee $stpCreatedUser
 * @property Department $stpDep
 * @property Employee $stpUpdatedUser
 * @property SmsTemplateTypeDepartment[] $smsTemplateTypeDepartments
 * @property Department[] $sttdDepartments
 * @property SmsTemplateTypeProject[] $smsTemplateTypeProjects
 * @property Project[] $sttpProjects
 */
class SmsTemplateType extends \yii\db\ActiveRecord
{
    public $departmentIds;
    public $projectIds;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sms_template_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stp_key', 'stp_origin_name', 'stp_name'], 'required'],
            [['stp_hidden', 'stp_created_user_id', 'stp_updated_user_id', 'stp_dep_id'], 'integer'],
            [['stp_created_dt', 'stp_updated_dt', 'departmentIds', 'projectIds'], 'safe'],
            [['stp_key'], 'string', 'max' => 50],
            [['stp_origin_name', 'stp_name'], 'string', 'max' => 100],
            [['stp_key'], 'unique'],
            [['stp_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['stp_created_user_id' => 'id']],
            [['stp_dep_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['stp_dep_id' => 'dep_id']],
            [['stp_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['stp_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'stp_id' => 'ID',
            'stp_key' => 'Key',
            'stp_origin_name' => 'Origin Name',
            'stp_name' => 'Name',
            'stp_hidden' => 'Hidden',
            'stp_created_user_id' => 'Created User ID',
            'stp_updated_user_id' => 'Updated User ID',
            'stp_created_dt' => 'Created Dt',
            'stp_updated_dt' => 'Updated Dt',
            'stp_dep_id' => 'Department',
            'departmentIds' => 'Departments',
            'projectIds' => 'Projects'
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['stp_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['stp_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'stp_created_user_id',
                'updatedByAttribute' => 'stp_updated_user_id',
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSms()
    {
        return $this->hasMany(Sms::class, ['s_template_type_id' => 'stp_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStpCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'stp_created_user_id']);
    }

    /**
     * Gets query for [[SmsTemplateTypeDepartments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSmsTemplateTypeDepartments()
    {
        return $this->hasMany(SmsTemplateTypeDepartment::class, ['sttd_stp_id' => 'stp_id']);
    }

    /**
     * Gets query for [[SttdDepartments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSttdDepartments()
    {
        return $this->hasMany(Department::class, ['dep_id' => 'sttd_department_id'])->viaTable('sms_template_type_department', ['sttd_stp_id' => 'stp_id']);
    }

    /**
     * Gets query for [[SmsTemplateTypeProjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSmsTemplateTypeProjects()
    {
        return $this->hasMany(SmsTemplateTypeProject::class, ['sttp_stp_id' => 'stp_id']);
    }

    /**
     * Gets query for [[SttpProjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSttpProjects()
    {
        return $this->hasMany(Project::class, ['id' => 'sttp_project_id'])->viaTable('sms_template_type_project', ['sttp_stp_id' => 'stp_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStpDep()
    {
        return $this->hasOne(Department::class, ['dep_id' => 'stp_dep_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStpUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'stp_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return SmsTemplateTypeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SmsTemplateTypeQuery(static::class);
    }

    /**
     * @return array
     * @throws \yii\httpclient\Exception
     */
    public static function synchronizationTypes(): array
    {
        $data = [
            'created' => [],
            'updated' => [],
            'error' => false
        ];

        /** @var CommunicationService $communication */
        $communication = Yii::$app->comms;
        $smsTypes = $communication->smsTypes();

        if ($smsTypes && isset($smsTypes['data']['types'])) {
            foreach ($smsTypes['data']['types'] as $type) {
                $t = self::findOne($type['stp_id']);
                if (!$t) {
                    $t = new self();
                    $t->stp_id = $type['stp_id'];
                    $t->stp_created_dt = date('Y-m-d H:i:s');
                    $t->stp_name = $type['stp_name'];

                    if (isset(Yii::$app->user) && Yii::$app->user->id) {
                        $t->stp_created_user_id = Yii::$app->user->id;
                    }

                    $data['created'][] = $type['stp_id'];
                } else {
                    $data['updated'][] = $type['stp_id'];
                }

                $t->stp_key = $type['stp_key'];
                $t->stp_origin_name = $type['stp_name'];
                $t->stp_updated_dt = date('Y-m-d H:i:s');

                if (isset(Yii::$app->user) && Yii::$app->user->id) {
                    $t->stp_updated_user_id = Yii::$app->user->id;
                }
                if (!$t->save()) {
                    Yii::error(VarDumper::dumpAsString($t->errors), 'SmsTemplateType:synchronizationTypes:save');
                }
            }
        } else {
            $data['error'] = 'Not found response data';
        }

        return $data;
    }

    /**
     * @param bool $withHidden
     * @return array
     */
    public static function getList($withHidden = true): array
    {
        $query = self::find()->orderBy(['stp_name' => SORT_ASC]);
        if (!$withHidden) {
            $query->andWhere(['stp_hidden' => false]);
        }
        $data = $query->asArray()->all();
        return ArrayHelper::map($data, 'stp_id', 'stp_name');
    }


    /**
     * @param bool $withHidden
     * @param int|null $dep_id
     * @return array
     */
    public static function getKeyList($withHidden = true, ?int $dep_id = null, ?int $pr_id = null): array
    {
        $query = self::find()->orderBy(['stp_name' => SORT_ASC]);
        $query->joinWith(['smsTemplateTypeDepartments']);
        $query->joinWith(['smsTemplateTypeProjects']);
        $query->groupBy(['stp_id', 'sttp_project_id']);

        if (!$withHidden) {
            $query->andWhere(['stp_hidden' => false]);
        }

        if ($dep_id !== null) {
            $query->andWhere(['sttd_department_id' => $dep_id]);
        }

        if ($pr_id !== null) {
            $query->orHaving(['sttp_project_id' => $pr_id]);
            $query->orHaving(['=', 'COUNT(sttp_project_id)', 0]);
        }

        $data = $query->asArray()->all();
        return ArrayHelper::map($data, 'stp_key', 'stp_name');
    }
}

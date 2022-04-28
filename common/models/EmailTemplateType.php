<?php

namespace common\models;

use common\components\CommunicationService;
use common\models\query\EmailTemplateTypeQuery;
use modules\featureFlag\FFlag;
use src\services\abtesting\email\EmailTemplateOfferABTestingService;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "email_template_type".
 *
 * @property int $etp_id
 * @property string $etp_key
 * @property string $etp_name
 * @property string $etp_origin_name
 * @property boolean $etp_hidden
 * @property int $etp_created_user_id
 * @property int $etp_updated_user_id
 * @property string $etp_created_dt
 * @property string $etp_updated_dt
 * @property int $etp_dep_id
 * @property int $etp_ignore_unsubscribe
 * @property mixed $etp_params_json
 *
 * @property Email[] $emails
 * @property Employee $etpCreatedUser
 * @property Employee $etpUpdatedUser
 * @property Department $etpDep
 * @property EmailTemplateTypeDepartment[] $emailTemplateTypeDepartments
 * @property Department[] $ettdDepartments
 * @property EmailTemplateTypeProject[] $emailTemplateTypeProjects
 * @property Project[] $ettpProjects
 */
class EmailTemplateType extends \yii\db\ActiveRecord
{
    public $departmentIds;
    public $projectIds;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->etp_params_json = self::etpParamsInit();
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'email_template_type';
    }

    public static function etpParamsInit(): array
    {
        return [
            'quotes' => [
                'selectRequired' => false,
                'originalRequired' => false,
                'minSelectedCount' => 0,
                'maxSelectedCount' => 0
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['etp_key', 'etp_name', 'etp_origin_name'], 'required'],
            [['etp_created_user_id', 'etp_updated_user_id', 'etp_dep_id', 'etp_ignore_unsubscribe'], 'integer'],
            [['etp_created_dt', 'etp_updated_dt', 'etp_params_json', 'departmentIds', 'projectIds'], 'safe'],
            [['etp_key'], 'string', 'max' => 50],
            [['etp_name', 'etp_origin_name'], 'string', 'max' => 100],
            [['etp_key'], 'unique'],
            [['etp_hidden'], 'boolean'],
            [['etp_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['etp_created_user_id' => 'id']],
            [['etp_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['etp_updated_user_id' => 'id']],
            [['etp_dep_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['etp_dep_id' => 'dep_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'etp_id' => 'ID',
            'etp_key' => 'Key',
            'etp_name' => 'Name',
            'etp_origin_name' => 'Original Name',
            'etp_hidden'    => 'Hidden',
            'etp_created_user_id' => 'Created User ID',
            'etp_updated_user_id' => 'Updated User ID',
            'etp_created_dt' => 'Created Dt',
            'etp_updated_dt' => 'Updated Dt',
            'etp_dep_id' => 'Department',
            'etp_ignore_unsubscribe' => 'Ignore Unsubscribe',
            'etp_params_json' => 'Params',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['etp_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['etp_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'etp_created_user_id',
                'updatedByAttribute' => 'etp_updated_user_id',
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmails()
    {
        return $this->hasMany(Email::class, ['e_template_type_id' => 'etp_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEtpCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'etp_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEtpUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'etp_updated_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEtpDep()
    {
        return $this->hasOne(Department::class, ['dep_id' => 'etp_dep_id']);
    }

    /**
     * {@inheritdoc}
     * @return EmailTemplateTypeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new EmailTemplateTypeQuery(static::class);
    }

    /**
     * Gets query for [[EmailTemplateTypeDepartments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmailTemplateTypeDepartments()
    {
        return $this->hasMany(EmailTemplateTypeDepartment::class, ['ettd_etp_id' => 'etp_id']);
    }

    /**
     * Gets query for [[EttdDepartments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEttdDepartments()
    {
        return $this->hasMany(Department::class, ['dep_id' => 'ettd_department_id'])->viaTable('email_template_type_department', ['ettd_ett_id' => 'etp_id']);
    }

    /**
     * Gets query for [[EmailTemplateTypeProjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmailTemplateTypeProjects(): \yii\db\ActiveQuery
    {
        return $this->hasMany(EmailTemplateTypeProject::class, ['ettp_etp_id' => 'etp_id']);
    }

    /**
     * Gets query for [[EttpProjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEttpProjects(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Project::class, ['id' => 'ettp_project_id'])->viaTable('email_template_type_project', ['ettp_etp_id' => 'etp_id']);
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
        $communication = Yii::$app->communication;
        $mailTypes = $communication->mailTypes();

        if ($mailTypes && isset($mailTypes['data']['types'])) {
            foreach ($mailTypes['data']['types'] as $type) {
                $t = self::findOne($type['etp_id']);
                if (!$t) {
                    $t = new self();
                    $t->etp_id = $type['etp_id'];
                    $t->etp_created_dt = date('Y-m-d H:i:s');
                    $t->etp_name = $type['etp_name'];

                    if (isset(Yii::$app->user) && Yii::$app->user->id) {
                        $t->etp_created_user_id = Yii::$app->user->id;
                    }

                    $data['created'][] = $type['etp_id'];
                } else {
                    $data['updated'][] = $type['etp_id'];
                }

                $t->etp_key = $type['etp_key'];
                $t->etp_origin_name = $type['etp_name'];
                $t->etp_updated_dt = date('Y-m-d H:i:s');

                if (isset(Yii::$app->user) && Yii::$app->user->id) {
                    $t->etp_updated_user_id = Yii::$app->user->id;
                }
                if (!$t->save()) {
                    Yii::error(VarDumper::dumpAsString($t->errors), 'EmailTemplateType:synchronizationTypes:save');
                }
            }
        } else {
            $data['error'] = 'Not found response data';
        }

        return $data;
    }


    /**
     * @param bool $withHidden
     * @param int|null $dep_id
     * @return array
     */
    public static function getList(bool $withHidden, ?int $dep_id): array
    {
        $query = self::find()->orderBy(['etp_name' => SORT_ASC]);
        if (!$withHidden) {
            $query->andWhere(['etp_hidden' => false]);
        }

        if ($dep_id !== null) {
            $query->andWhere(['etp_dep_id' => $dep_id]);
        }

        $data = $query->asArray()->all();
        return ArrayHelper::map($data, 'etp_id', 'etp_name');
    }

    /**
     * @param bool $withHidden
     * @param int|null $dep_id
     * @return array
     */
    public static function getKeyList(bool $withHidden, ?int $dep_id): array
    {
        $query = self::find()->orderBy(['etp_name' => SORT_ASC]);
        if (!$withHidden) {
            $query->andWhere(['etp_hidden' => false]);
        }

        if ($dep_id !== null) {
            $query->andWhere(['etp_dep_id' => $dep_id]);
        }

        $data = $query->asArray()->all();
        return ArrayHelper::map($data, 'etp_key', 'etp_name');
    }

    /**
     * @param bool $withHidden
     * @param int|null $dep_id
     * @return array
     */
    public static function getEmailTemplateTypesList(bool $withHidden, ?int $dep_id, ?int $pr_id, ?Lead $lead = null): array
    {
        $query = self::find()->select(['etp_id', 'etp_key', 'etp_name', 'etp_ignore_unsubscribe'])->orderBy(['etp_name' => SORT_ASC]);
        $query->joinWith(['emailTemplateTypeDepartments']);
        $query->joinWith(['emailTemplateTypeProjects']);
        $query->groupBy(['etp_id', 'ettp_project_id']);

        if (!$withHidden) {
            $query->andWhere(['etp_hidden' => false]);
        }

        if ($dep_id !== null) {
            $query->andWhere(['ettd_department_id' => $dep_id]);
        }

        if ($pr_id !== null) {
            $query->orHaving(['ettp_project_id' => $pr_id]);
            $query->orHaving(['=', 'COUNT(ettp_project_id)', 0]);
        }
        /** @fflag FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES, A/B testing for email offer templates enable/disable */
        if (Yii::$app->ff->can(FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES) && $lead) {
            $emailAbTestingService = new EmailTemplateOfferABTestingService();
            $etpId = $emailAbTestingService->assignEmailOfferTemplateToLead($lead);
            if ($etpId) {
                $defaultTemplateId = $emailAbTestingService->getDefaultOfferTemplateId();
                if ($etpId !== $defaultTemplateId) {
                    $query->andWhere(['<>', 'etp_id', $defaultTemplateId])
                          ->orWhere(['etp_id' => $etpId]);
                }
            }
        }

        return $query->asArray()->all();
    }

    public static function isJson($content): bool
    {
        if (!is_array($content)) {
            json_decode($content);
            return (json_last_error() === JSON_ERROR_NONE);
        }

        return false;
    }

    public function beforeSave($insert): bool
    {
        if (self::isJson($this->etp_params_json)) {
            $this->etp_params_json = json_decode($this->etp_params_json);
        } else {
            $this->etp_params_json = $this->getOldAttribute('etp_params_json') ?? self::etpParamsInit();
        }

        return parent::beforeSave($insert);
    }

    public function getParams(): array
    {
        if ($this->etp_params_json !== null) {
            return $this->etp_params_json;
        }

        return self::etpParamsInit();
    }
}

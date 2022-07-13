<?php

namespace src\model\sms\entity\smsDistributionList;

use common\models\Client;
use common\models\ClientPhone;
use common\models\Employee;
use common\models\Project;
use src\helpers\app\AppHelper;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "sms_distribution_list".
 *
 * @property int $sdl_id
 * @property int|null $sdl_com_id
 * @property int|null $sdl_project_id
 * @property string $sdl_phone_from
 * @property string $sdl_phone_to
 * @property int|null $sdl_client_id
 * @property string $sdl_text
 * @property string|null $sdl_start_dt
 * @property string|null $sdl_end_dt
 * @property int|null $sdl_status_id
 * @property int|null $sdl_priority
 * @property string|null $sdl_error_message
 * @property string|null $sdl_message_sid
 * @property int|null $sdl_num_segments
 * @property float|null $sdl_price
 * @property int|null $sdl_created_user_id
 * @property int|null $sdl_updated_user_id
 * @property string|null $sdl_created_dt
 * @property string|null $sdl_updated_dt
 *
 * @property Client $sdlClient
 * @property Employee $sdlCreatedUser
 * @property Project $sdlProject
 * @property Employee $sdlUpdatedUser
 */
class SmsDistributionList extends ActiveRecord
{
    public const STATUS_NEW     = 1;
    public const STATUS_PENDING = 2;
    public const STATUS_PROCESS = 3;
    public const STATUS_CANCEL  = 4;
    public const STATUS_DONE    = 5;
    public const STATUS_ERROR   = 6;
    public const STATUS_SENT    = 7;



    public const STATUS_LIST = [
        self::STATUS_NEW        => 'New',
        self::STATUS_PENDING    => 'Pending',
        self::STATUS_PROCESS    => 'Processing',
        self::STATUS_SENT       => 'Send',
        self::STATUS_ERROR      => 'Error',
        self::STATUS_DONE       => 'Done',
        self::STATUS_CANCEL     => 'Cancel',
    ];

    public const STATUS_LIST_LABEL = [
        self::STATUS_NEW        => '<span class="badge badge-info">' . self::STATUS_LIST[self::STATUS_NEW] . '</span>',
        self::STATUS_PENDING    => '<span class="badge badge-warning">' . self::STATUS_LIST[self::STATUS_PENDING] . '</span>',
        self::STATUS_PROCESS    => '<span class="badge badge-white">' . self::STATUS_LIST[self::STATUS_PROCESS] . '</span>',
        self::STATUS_SENT       => '<span class="badge badge-green">' . self::STATUS_LIST[self::STATUS_SENT] . '</span>',
        self::STATUS_ERROR      => '<span class="badge badge-danger">' . self::STATUS_LIST[self::STATUS_ERROR] . '</span>',
        self::STATUS_DONE       => '<span class="badge badge-green">' . self::STATUS_LIST[self::STATUS_DONE] . '</span>',
        self::STATUS_CANCEL     => '<span class="badge badge-danger">' . self::STATUS_LIST[self::STATUS_CANCEL] . '</span>',
    ];

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'sms_distribution_list';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['sdl_com_id', 'sdl_project_id', 'sdl_client_id', 'sdl_status_id', 'sdl_priority', 'sdl_num_segments', 'sdl_created_user_id', 'sdl_updated_user_id'], 'integer'],
            [['sdl_phone_from', 'sdl_phone_to', 'sdl_text'], 'required'],
            [['sdl_text', 'sdl_error_message'], 'string'],
            [['sdl_start_dt', 'sdl_end_dt', 'sdl_created_dt', 'sdl_updated_dt'], 'safe'],
            [['sdl_price'], 'number'],
            [['sdl_phone_from', 'sdl_phone_to'], 'string', 'max' => 20],
            [['sdl_message_sid'], 'string', 'max' => 40],
            [['sdl_client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['sdl_client_id' => 'id']],
            [['sdl_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['sdl_created_user_id' => 'id']],
            [['sdl_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['sdl_created_user_id' => 'id']],
            [['sdl_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['sdl_updated_user_id' => 'id']],
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['sdl_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['sdl_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
//            'user' => [
//                'class' => BlameableBehavior::class,
//                'createdByAttribute' => 'sdl_created_user_id',
//                'updatedByAttribute' => 'sdl_updated_user_id',
//            ],
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            if (!$this->sdl_client_id) {
                $clientPhone = ClientPhone::find()->where(['phone' => $this->sdl_phone_to])->orderBy(['id' => SORT_DESC])->one();
                if ($clientPhone) {
                    $this->sdl_client_id = $clientPhone->client_id;
                }
            }
            return true;
        }
        return false;
    }


    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'sdl_id' => 'ID',
            'sdl_com_id' => 'Communication ID',
            'sdl_project_id' => 'Project',
            'sdl_phone_from' => 'Phone From',
            'sdl_phone_to' => 'Phone To',
            'sdl_client_id' => 'Client ID',
            'sdl_text' => 'Text',
            'sdl_start_dt' => 'Start DateTime',
            'sdl_end_dt' => 'End DateTime',
            'sdl_status_id' => 'Status',
            'sdl_priority' => 'Priority',
            'sdl_error_message' => 'Error Message',
            'sdl_message_sid' => 'Message Sid',
            'sdl_num_segments' => 'Num Segments',
            'sdl_price' => 'Price',
            'sdl_created_user_id' => 'Created User ID',
            'sdl_updated_user_id' => 'Updated User ID',
            'sdl_created_dt' => 'Created Date',
            'sdl_updated_dt' => 'Updated Date',
        ];
    }

    /**
     * Gets query for [[SdlClient]].
     *
     * @return ActiveQuery|ClientsQuery
     */
    public function getSdlClient()
    {
        return $this->hasOne(Client::class, ['id' => 'sdl_client_id']);
    }

    /**
     * Gets query for [[SdlCreatedUser]].
     *
     * @return ActiveQuery|EmployeesQuery
     */
    public function getSdlCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'sdl_created_user_id']);
    }

    /**
     * Gets query for [[SdlProject]].
     *
     * @return ActiveQuery|ProjectsQuery
     */
    public function getSdlProject()
    {
        return $this->hasOne(Project::class, ['id' => 'sdl_project_id']);
    }

    /**
     * Gets query for [[SdlUpdatedUser]].
     *
     * @return ActiveQuery|EmployeesQuery
     */
    public function getSdlUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'sdl_updated_user_id']);
    }

    /**
     * @return SmsDistributionListQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SmsDistributionListQuery(static::class);
    }

    /**
     * @return array
     */
    public static function getStatusList(): array
    {
        return self::STATUS_LIST;
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return self::STATUS_LIST[$this->sdl_status_id] ?? '-';
    }

    /**
     * @return string
     */
    public function getStatusLabel(): string
    {
        return self::STATUS_LIST_LABEL[$this->sdl_status_id] ?? '-';
    }

    /**
     * @param int|null $limit
     * @return array|SmsDistributionList[]
     */
    public static function getSmsListForJob(?int $limit = 0)
    {
        $query = self::find();
        $query->where(['sdl_status_id' => self::STATUS_PENDING])
            ->andWhere(['OR', ['sdl_start_dt' => null], ['<=', 'sdl_start_dt', date('Y-m-d H:i:s')]])
            ->andWhere(['OR', ['sdl_end_dt' => null], ['>=', 'sdl_end_dt', date('Y-m-d H:i:s')]]);

        $query->orderBy(['sdl_priority' => SORT_ASC]);
        if ($limit > 0) {
            $query->limit($limit);
        }
        return $query->all();
    }

    /**
     * @return array
     */
    public function sendSms()
    {
        if ($this->sdlProject && !$this->sdlProject->getParams()->sms->isEnabled()) {
            $this->sdl_status_id = self::STATUS_ERROR;
            $this->sdl_error_message = 'Sms disabled for project ' . $this->sdlProject->name;
            if (!$this->save()) {
                Yii::error(VarDumper::dumpAsString($this->errors), 'SmsDistributionList:sendSms:smsDisabled');
            }
            $out = ['error' => $this->sdl_error_message];
            return $out;
        }

        $out = ['error' => false];
        $communication = Yii::$app->comms;
        $data = [];

        $data['project_id'] = $this->sdl_project_id;
        $content_data['sms_text'] = $this->sdl_text;


        try {
            $str = 'ProjectId: ' . $this->sdl_project_id . ' From:' . $this->sdl_phone_from . ' To:' . $this->sdl_phone_to;
            //VarDumper::dump($str); exit;

            $request = $communication->smsSend($this->sdl_project_id, null, $this->sdl_phone_from, $this->sdl_phone_to, $content_data, $data, 'en-US', 0);

            if ($request && isset($request['data']['sq_status_id'])) {
                $this->sdl_status_id        = $request['data']['sq_status_id'];
                $this->sdl_com_id           = $request['data']['sq_id'];
                $this->sdl_message_sid      = $request['data']['sq_tw_message_id'] ?? null;
                $this->sdl_num_segments     = $request['data']['sq_tw_num_segments'] ?? null;
                $this->sdl_price            = $request['data']['sq_tw_price'] ?? null;

                if (!$this->save()) {
                    Yii::error(VarDumper::dumpAsString($this->errors), 'SmsDistributionList:sendSms:save1');
                }
            }

            //VarDumper::dump($request, 10, true); exit;

            if ($request && isset($request['error']) && $request['error']) {
                $this->sdl_status_id = self::STATUS_ERROR;
                $errorData = @json_decode($request['error'], true);
                $this->sdl_error_message = 'Communication error: ' . ($errorData['message'] ?: $request['error']);
                if (!$this->save()) {
                    Yii::error(VarDumper::dumpAsString($this->errors), 'SmsDistributionList:sendSms:save2');
                }
                $out['error'] = $this->sdl_error_message;
                Yii::error($str . "\r\n" . $out['error'], 'SmsDistributionList:sendSms:smsSend:CommunicationError');
            }
        } catch (\Throwable $throwable) {
            $error = AppHelper::throwableFormatter($throwable);
            $out['error'] = $error;
            Yii::error($str . "\r\n" . $error, 'SmsDistributionList:sendSms:smsSend:Throwable');
            $this->sdl_error_message = 'Communication error: ' . $error;
            if (!$this->save()) {
                Yii::error(VarDumper::dumpAsString($this->errors), 'SmsDistributionList:sendSms:save3');
            }
        }

        // VarDumper::dump($request, 10, true); exit;

        return $out;
    }
}

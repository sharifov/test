<?php

namespace modules\requestControl\models;

use common\models\Employee;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_site_activity".
 *
 * @property int $usa_id
 * @property int $usa_user_id
 * @property string $usa_request_url
 * @property string $usa_page_url
 * @property string $usa_ip
 * @property int $usa_request_type
 * @property string $usa_request_get
 * @property string $usa_request_post
 * @property string $usa_created_dt
 *
 * @property Employee $usaUser
 */
class UserSiteActivity extends ActiveRecord
{
    public const REQUEST_TYPE_GET = 1;
    public const REQUEST_TYPE_POST = 2;

    public const REQUEST_TYPE_LIST = [
        self::REQUEST_TYPE_GET => 'GET',
        self::REQUEST_TYPE_POST => 'POST',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'user_site_activity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usa_user_id', 'usa_request_type'], 'integer'],
            [['usa_request_get', 'usa_request_post'], 'string'],
            [['usa_created_dt'], 'safe'],
            [['usa_request_url'], 'string', 'max' => 500],
            [['usa_page_url'], 'string', 'max' => 255],
            [['usa_ip'], 'string', 'max' => 40],
            [['usa_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['usa_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'usa_id' => 'ID',
            'usa_user_id' => 'User ID',
            'usa_request_url' => 'Request Url',
            'usa_page_url' => 'Page Url',
            'usa_ip' => 'IP',
            'usa_request_type' => 'Request Type',
            'usa_request_get' => 'Request Get',
            'usa_request_post' => 'Request Post',
            'usa_created_dt' => 'Created Dt',
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['usa_created_dt'],
                    //ActiveRecord::EVENT_BEFORE_UPDATE => ['ugs_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsaUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'usa_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return UserSiteActivityQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserSiteActivityQuery(get_called_class());
    }

    /**
     * @return string
     */
    public function getRequestTypeName(): string
    {
        return self::REQUEST_TYPE_LIST[$this->usa_request_type] ?? '-';
    }

    /**
     * @return int
     */
    public static function clearHistoryLogs(): int
    {
        $days = (int) Yii::$app->params['settings']['user_site_activity_log_history_days'] ?? 0;
        if ($days > 0) {
            return self::deleteAll(['<=', 'usa_created_dt', date('Y-m-d', strtotime('-' . $days . ' days'))]);
        }
        return 0;
    }
}

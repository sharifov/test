<?php

namespace common\models;

use src\entities\EventTrait;
use src\events\client\ClientEmailCreatedEvent;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;

/**
 * This is the model class for table "client_email".
 *
 * @property int $id
 * @property int $client_id
 * @property string $email
 * @property string $created
 * @property string $updated
 * @property string $comments
 * @property int $type
 * @property string $ce_title
 *
 * @property Client $client
 */
class ClientEmail extends \yii\db\ActiveRecord
{
    use EventTrait;

    public const EMAIL_VALID = 1;
    public const EMAIL_FAVORITE = 2;
    public const EMAIL_INVALID = 9;
    public const EMAIL_NOT_SET = 0;

    public const EMAIL_TYPE = [
        self::EMAIL_NOT_SET => '-',
        self::EMAIL_VALID => 'Valid',
        self::EMAIL_FAVORITE => 'Favorite',
        self::EMAIL_INVALID => 'Invalid',
    ];

    public const EMAIL_TYPE_ICONS = [
        self::EMAIL_VALID => "<i title=" . self::EMAIL_TYPE[self::EMAIL_VALID] . " class='fa fa-envelope success'></i> ",
        self::EMAIL_FAVORITE => "<i title=" . self::EMAIL_TYPE[self::EMAIL_FAVORITE] . " class='fa fa-envelope warning'></i> ",
        self::EMAIL_INVALID => "<i title=" . self::EMAIL_TYPE[self::EMAIL_INVALID] . " class='fa fa-envelope danger'></i> ",
        self::EMAIL_NOT_SET => "<i title=" . self::EMAIL_TYPE[self::EMAIL_NOT_SET] . " class='fa fa-envelope'></i> "
    ];

    public const EMAIL_TYPE_LABELS = [
        self::EMAIL_VALID => '<span class="label label-success">{type}</span>',
        self::EMAIL_FAVORITE => '<span class="label label-warning">{type}</span>',
        self::EMAIL_INVALID => '<span class="label label-danger">{type}</span>',
        self::EMAIL_NOT_SET => '{type}'
    ];

    public const EMAIL_TYPE_TEXT_DECORATION = [
        self::EMAIL_INVALID => 'text-line-through'
    ];

    public $emails;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_email';
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created', 'updated'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @param string $email
     * @param int $clientId
     * @param int $emailType
     * @param string|null $ceTitle
     * @return static
     */
    public static function create(string $email, int $clientId, int $emailType = null, string $ceTitle = null): self
    {
        $clientEmail = new static();
        $clientEmail->email = $email;
        $clientEmail->client_id = $clientId;
        $clientEmail->type = $emailType;
        $clientEmail->ce_title = $ceTitle;
        $clientEmail->recordEvent(new ClientEmailCreatedEvent($clientEmail));
        return $clientEmail;
    }

    /**
     * @param string $email
     * @param int|null $emailType
     * @param string|null $ceTitle
     */
    public function edit(string $email, int $emailType = null, string $ceTitle = null): void
    {
        $this->email = $email;
        $this->type = $emailType;
        $this->ce_title = $ceTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 100],
            ['ce_title', 'string', 'max' => 150],

            [['client_id', 'type'], 'integer'],

            [['created', 'updated', 'comments'], 'safe'],

            ['client_id', 'required'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['client_id' => 'id']],

            [['email', 'client_id'], 'unique', 'targetAttribute' => ['email', 'client_id']],

            ['type', 'required'],
            ['type', 'in', 'range' => array_keys(self::EMAIL_TYPE)],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'email' => 'Email',
            'created' => 'Created',
            'updated' => 'Updated',
            'type' => 'Email Type',
            'ce_title' => 'Title'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }

//    public function beforeValidate()
//    {
//        $this->updated = date('Y-m-d H:i:s');
//
//        if (strpos($this->email, 'wowfare') !== false) {
//            $this->addError('email', 'Email is invalid!');
//        }
//
//        return parent::beforeValidate();
//    }

    /**
     * @return int
     */
    public function countUsersSameEmail(): int
    {
        $subQuery = (new Query())->select(['client_id'])->distinct()
            ->from(ClientEmail::tableName())
            ->where(['email' => $this->email]);

        $query = (new Query())->select(['id'])->distinct()
            ->from(Client::tableName())
            ->where(['<>', 'id', $this->client_id])
            ->andWhere(['IN', 'id', $subQuery]);

        return (int)$query->count();
    }

    /**
     * @param int|null $type
     * @return mixed|string
     */
    public static function getEmailType(?int $type)
    {
        return self::EMAIL_TYPE[$type] ?? '';
    }

    /**
     * @return array
     */
    public static function getEmailTypeList(): array
    {
        return self::EMAIL_TYPE;
    }

    /**
     * @param int|null $type
     * @return mixed|string
     */
    public static function getEmailTypeTextDecoration(?int $type)
    {
        return self::EMAIL_TYPE_TEXT_DECORATION[$type] ?? '';
    }

    /**
     * @param int|null $type
     * @return mixed|string
     */
    public static function getEmailTypeIcon(?int $type)
    {
        return self::EMAIL_TYPE_ICONS[$type] ?? '';
    }

    /**
     * @param int|null $type
     * @return string
     */
    public static function getEmailTypeLabel(?int $type): string
    {
        if (isset(self::EMAIL_TYPE_LABELS[$type], self::EMAIL_TYPE[$type])) {
            return str_replace('{type}', self::EMAIL_TYPE[$type], self::EMAIL_TYPE_LABELS[$type]);
        }
        return '';
    }

    public static function getEmailListByClient(int $clientId): array
    {
        return self::find()->select(['email', 'id'])->where(['client_id' => $clientId])->indexBy('id')->asArray()->column();
    }

    public static function find()
    {
        return new ClientEmailQuery(static::class);
    }

    public static function getGeneralEmail(int $clientId): ?string
    {
        $result = self::find()->select(['email'])
            ->andWhere(['client_id' => $clientId])
            ->andWhere([
                'OR',
                ['IS', 'type', null],
                ['<>', 'type', self::EMAIL_INVALID],
            ])
            ->orderBy(new Expression('FIELD (type, ' . self::EMAIL_FAVORITE . ', ' . self::EMAIL_VALID . ', ' . self::EMAIL_NOT_SET . ', null)'))
            ->asArray()
            ->one();

        if ($result) {
            return $result['email'];
        }
        return null;
    }
}

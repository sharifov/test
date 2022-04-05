<?php

namespace modules\user\userFeedback\entity;

use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\helpers\Html;
use common\models\Employee;

/**
 * This is the model class for table "user_feedback".
 *
 * @property int $uf_id
 * @property int $uf_type_id
 * @property int $uf_status_id
 * @property string $uf_title
 * @property string|null $uf_message
 * @property string $uf_data_json
 * @property string $uf_created_dt
 * @property string|null $uf_updated_dt
 * @property int|null $uf_created_user_id
 * @property int|null $uf_updated_user_id *
 * @property string|null $uf_resolution
 * @property int|null $uf_resolution_user_id
 * @property string|null $uf_resolution_dt *
 *
 * @property Employee $ufCreatedUser
 * @property Employee $ufResolutionUser
 */
class UserFeedback extends ActiveRecord
{
    public const TYPE_BUG       = 1;
    public const TYPE_FEATURE   = 2;
    public const TYPE_QUESTION  = 3;

    public const STATUS_NEW         = 1;
    public const STATUS_PENDING     = 2;
    public const STATUS_CANCEL      = 3;
    public const STATUS_DONE        = 4;

    public const STATUS_LIST = [
        self::STATUS_NEW => 'New',
        self::STATUS_PENDING => 'Pending',
        self::STATUS_CANCEL => 'Cancel',
        self::STATUS_DONE => 'Done',
    ];

    public const FINAL_STATUS_LIST = [
        self::STATUS_CANCEL => 'Cancel',
        self::STATUS_DONE => 'Done',
    ];

    public const TYPE_LIST = [
        self::TYPE_BUG => 'Bug Report',
        self::TYPE_FEATURE => 'Feature',
        self::TYPE_QUESTION => 'Question'
    ];

    public const STATUS_LABEL_LIST = [
        self::STATUS_NEW => 'label-default',
        self::STATUS_PENDING => 'label-warning',
        self::STATUS_CANCEL => 'label-danger',
        self::STATUS_DONE => 'label-success',
    ];

    public const TYPE_LABEL_LIST = [
        self::TYPE_BUG => 'label-danger',
        self::TYPE_FEATURE => 'label-info',
        self::TYPE_QUESTION => 'label-warning'
    ];

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['uf_created_dt', 'uf_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['uf_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['uf_created_user_id', 'uf_updated_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['uf_updated_user_id'],
                ]
            ]
        ];
    }


    /**
     * @inheritdoc
     */
    public static function primaryKey(): array
    {
        return ["uf_id"];
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'user_feedback';
    }

    /**
     * @return Connection the database connection used by this AR class.
     * @throws InvalidConfigException
     */
    public static function getDb(): Connection
    {
        return Yii::$app->get('db_postgres');
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['uf_type_id', 'uf_status_id', 'uf_title', 'uf_data_json'], 'required'],
            [['uf_type_id', 'uf_status_id', 'uf_created_user_id', 'uf_updated_user_id', 'uf_resolution_user_id'], 'default', 'value' => null],
            [['uf_type_id', 'uf_status_id', 'uf_created_user_id', 'uf_updated_user_id', 'uf_resolution_user_id'], 'integer'],
            [['uf_message'], 'string'],
            [['uf_data_json', 'uf_created_dt', 'uf_updated_dt', 'uf_resolution_dt'], 'safe'],
            [['uf_title'], 'string', 'max' => 255],
            [['uf_id', 'uf_created_dt'], 'unique', 'targetAttribute' => ['uf_id', 'uf_created_dt']],
            [['uf_resolution'], 'string', 'max' => 500],
            [['uf_title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'uf_id' => 'ID',
            'uf_type_id' => 'Type ID',
            'uf_status_id' => 'Status ID',
            'uf_title' => 'Title',
            'uf_message' => 'Message',
            'uf_data_json' => 'Data Json',
            'uf_created_dt' => 'Created Dt',
            'uf_updated_dt' => 'Updated Dt',
            'uf_created_user_id' => 'Created User ID',
            'uf_updated_user_id' => 'Updated User ID',
            'uf_resolution' => 'Resolution',
            'uf_resolution_user_id' => 'Resolution User ID',
            'uf_resolution_dt' => 'Resolution Dt'
        ];
    }

    /**
     * @return string[]
     */
    public static function getTypeList(): array
    {
        return self::TYPE_LIST;
    }

    public static function create(
        ?string $title,
        ?string $message,
        ?array $data
    ): self {
        $self = new self();
        $self->uf_title = $title;
        $self->uf_message = $message;
        $self->uf_data_json = $data;
        return $self;
    }

    public static function createNewFeedback(
        ?string $title,
        ?string $message,
        ?int $type,
        ?array $data
    ): self {
        $self = self::create($title, $message, $data);
        $self->setType($type);
        $self->setStatusNew();
        return $self;
    }

    public function getUfCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'uf_created_user_id']);
    }

    public function getUfResolutionUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'uf_resolution_user_id']);
    }

    public function setType(?int $type): void
    {
        switch ($type) {
            case self::TYPE_BUG:
                $this->setTypeBug();
                break;
            case self::TYPE_FEATURE:
                $this->setTypeFeature();
                break;
            case self::TYPE_QUESTION:
                $this->setTypeQuestion();
                break;
        }
    }

    public function setTypeBug(): void
    {
        $this->uf_type_id = self::TYPE_BUG;
    }

    public function setTypeFeature(): void
    {
        $this->uf_type_id = self::TYPE_FEATURE;
    }

    public function setTypeQuestion(): void
    {
        $this->uf_type_id = self::TYPE_QUESTION;
    }

    public function setStatusNew(): void
    {
        $this->uf_status_id = self::STATUS_NEW;
    }

    public function getStatusName(): ?string
    {
        return self::getStatusList()[$this->uf_status_id] ?? null;
    }

    public function getClassStatusLabel(): string
    {
        return self::STATUS_LABEL_LIST[$this->uf_status_id] ?? '';
    }

    public function getClassTypeLabel(): string
    {
        return self::TYPE_LABEL_LIST[$this->uf_type_id] ?? '';
    }

    public function getStatusLabel(): string
    {
        return Html::tag('span', $this->getStatusName(), ['class' => 'label ' . $this->getClassStatusLabel()]);
    }

    public function getTypeLabel(): string
    {
        return Html::tag('span', $this->getTypeName(), ['class' => 'label ' . $this->getClassTypeLabel()]);
    }

    public function getTypeName(): ?string
    {
        return self::TYPE_LIST[$this->uf_type_id] ?? null;
    }

    public static function getStatusList(): array
    {
        return self::STATUS_LIST;
    }

    public function isOwner(int $userId): bool
    {
        return $this->uf_created_user_id === $userId;
    }

    public static function partitionDatesFrom(\DateTime $date): array
    {
        $monthBegin = date('Y-m-d', strtotime(date_format($date, 'Y-m-1')));
        if (!$monthBegin) {
            throw new \RuntimeException("invalid partition start date");
        }

        $partitionStartDate = date_create_from_format('Y-m-d', $monthBegin);
        $partitionEndDate = date_create_from_format('Y-m-d', $monthBegin);

        date_add($partitionEndDate, date_interval_create_from_date_string("1 month"));

        return [$partitionStartDate, $partitionEndDate];
    }

    public static function createMonthlyPartition(\DateTime $partFromDateTime, \DateTime $partToDateTime): string
    {
        $db = self::getDb();
        $partTableName = self::tableName() . "_" . date_format($partFromDateTime, "Y_m");
        $cmd = $db->createCommand("create table " . $partTableName . " PARTITION OF " . self::tableName() .
            " FOR VALUES FROM ('" . date_format($partFromDateTime, "Y-m-d") . "') TO ('" . date_format($partToDateTime, "Y-m-d") . "')");
        $cmd->execute();
        return $partTableName;
    }
}

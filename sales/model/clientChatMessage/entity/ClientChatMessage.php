<?php

namespace sales\model\clientChatMessage\entity;

use DateTime;
use Exception;
use Yii;

/**
 * This is the model class for table "client_chat_message".
 *
 * @property int $ccm_id
 * @property string $ccm_rid
 * @property int|null $ccm_client_id
 * @property int|null $ccm_user_id
 * @property string $ccm_sent_dt
 * @property array $ccm_body
 * @property int $ccm_has_attachment
 */
class ClientChatMessage extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_chat_message';
    }

    /**
     * @return object
     */
    public static function getDb()
    {
        return Yii::$app->get('db_postgres');
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ["ccm_id"];
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ccm_rid', 'ccm_sent_dt', 'ccm_body'], 'required'],
            [['ccm_client_id', 'ccm_user_id'], 'default', 'value' => null],
            [['ccm_client_id', 'ccm_user_id', 'ccm_has_attachment'], 'integer'],
            [['ccm_sent_dt', 'ccm_body'], 'safe'],
            [['ccm_rid'], 'string', 'max' => 150],
        ];
    }


    /**
     * Create a partition table with indicated from and to date
     *
     * @param DateTime $partFromDateTime partition start date
     * @param DateTime $partToDateTime partition end date
     * @return string table_name created table
     * @throws Exception any errors occurred during execution
     */
    public static function createMonthlyPartition(DateTime $partFromDateTime, DateTime $partToDateTime) : string
    {
        $db = self::getDb();
        $partTableName = self::tableName()."_".date_format($partFromDateTime, "Y_m");
        $cmd = $db->createCommand("create table ".$partTableName." PARTITION OF ".self::tableName().
            " FOR VALUES FROM ('". date_format($partFromDateTime, "Y-m-d") . "') TO ('".date_format($partToDateTime,"Y-m-d")."')");
        $cmd->execute();
        return $partTableName;
    }

    /**
     * Calculate from and to dates from a given date.
     * Given date -> from = start of the month, to = next month start date
     *
     * @param DateTime $date partition start date
     * @return array DateTime table_name created table
     * @throws Exception any errors occurred during execution
     */
    public static function partitionDatesFrom(DateTime $date) : array
    {
        $monthBegin = date('Y-m-d', strtotime(date_format($date,'Y-m-1')));
        if (!$monthBegin) {
            throw new Exception("invalid partition start date");
        }

        $partitionStartDate = date_create_from_format('Y-m-d', $monthBegin);
        $partitionEndDate = date_create_from_format('Y-m-d', $monthBegin);

        date_add($partitionEndDate, date_interval_create_from_date_string("1 month"));

        return [$partitionStartDate, $partitionEndDate];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ccm_id' => 'ID',
            'ccm_rid' => 'Room ID',
            'ccm_client_id' => 'Client ID',
            'ccm_user_id' => 'User ID',
            'ccm_sent_dt' => 'Sent',
            'ccm_body' => 'Message',
            'ccm_has_attachment' => 'Has Attachment',
            'files' => 'Files',
        ];
    }
}

<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "quote_communication_open_log".
 *
 * @property int $qcol_id
 * @property int $qcol_quote_communication_id
 * @property string|null $qcol_created_dt
 *
 * @property QuoteCommunication $quoteCommunication
 */
class QuoteCommunicationOpenLog extends ActiveRecord
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['qcol_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quote_communication_open_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qcol_quote_communication_id'], 'required'],
            [['qcol_quote_communication_id'], 'integer'],
            [['qcol_created_dt'], 'safe'],
            [['qcol_quote_communication_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuoteCommunication::class, 'targetAttribute' => ['qcol_quote_communication_id' => 'qc_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'qcol_id' => 'ID',
            'qcol_quote_communication_id' => 'Communication ID',
            'qcol_created_dt' => 'Created Dt'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuoteCommunication()
    {
        return $this->hasOne(QuoteCommunication::class, ['qc_id' => 'qcol_quote_communication_id']);
    }

    /**
     * @param $data
     */
    public static function createByRequestData($data): void
    {
        if (isset($data['queryParams']['qc']) && isset($data['uid'])) {
            $quoteCommunication = QuoteCommunication::find()
                ->alias('t')
                ->join('LEFT OUTER JOIN', ['q' => Quote::tableName()], 't.qc_quote_id=q.id')
                ->where('t.qc_uid=:qcUid AND q.uid=:quoteUid', [':qcUid' => $data['queryParams']['qc'], ':quoteUid' => $data['uid']])
                ->one();
            if (!is_null($quoteCommunication)) {
                $model = new self();
                $model->qcol_quote_communication_id = $quoteCommunication->getPrimaryKey();
                $model->save();
            }
        }
    }
}

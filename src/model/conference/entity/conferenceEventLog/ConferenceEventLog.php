<?php

namespace src\model\conference\entity\conferenceEventLog;

/**
 * This is the model class for table "{{%conference_event_log}}".
 *
 * @property int $cel_id
 * @property string $cel_event_type
 * @property string $cel_conference_sid
 * @property int|null $cel_sequence_number
 * @property string $cel_created_dt
 * @property string $cel_data
 */
class ConferenceEventLog extends \yii\db\ActiveRecord
{
    public static function create(string $conferenceSid, \DateTimeImmutable $createdDt, string $data, string $eventType, int $sequenceNumber): self
    {
        $log = new self();
        $log->cel_conference_sid = $conferenceSid;
        $log->cel_created_dt = $createdDt->format('Y-m-d H:i:s');
        $log->cel_data = $data;
        $log->cel_event_type = $eventType;
        $log->cel_sequence_number = $sequenceNumber;
        return $log;
    }

    public function rules(): array
    {
        return [
            ['cel_conference_sid', 'required'],
            ['cel_conference_sid', 'string', 'max' => 34],

            ['cel_created_dt', 'required'],
            ['cel_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['cel_data', 'required'],
            ['cel_data', 'string', 'max' => 65000],

            ['cel_event_type', 'required'],
            ['cel_event_type', 'string', 'max' => 50],

            ['cel_sequence_number', 'integer', 'max' => 32000],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'cel_id' => 'ID',
            'cel_event_type' => 'Event Type',
            'cel_conference_sid' => 'Conference Sid',
            'cel_sequence_number' => 'Sequence Number',
            'cel_created_dt' => 'Created Dt',
            'cel_data' => 'Data',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%conference_event_log}}';
    }

    public static function getPrevModels($prevId, $limit, $filters = null): array
    {
        if (isset($filters)) {
            $mainQuery = self::find()
                ->where(['>', 'cel_id', $prevId])
                ->andFilterWhere($filters)
                ->orderBy(['cel_id' => SORT_ASC])
                ->limit($limit + 1);
            return self::find()
                ->from(['C' => $mainQuery])
                ->orderBy(['cel_id' => SORT_DESC])
                ->all();
        }

        $mainQuery = self::find()
            ->where(['>', 'cel_id', $prevId])
            ->orderBy(['cel_id' => SORT_ASC])
            ->limit($limit + 1);
        return self::find()
            ->from(['C' => $mainQuery])
            ->orderBy(['cel_id' => SORT_DESC])
            ->limit($limit + 1)
            ->all();
    }
}

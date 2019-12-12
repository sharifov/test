<?php

namespace sales\services\sms\incoming;

use common\models\Project;
use yii\base\Model;

/**
 * Class SmsIncomingForm
 *
 * @property $si_id
 * @property $si_sent_dt
 * @property $si_phone_to
 * @property $si_phone_from
 * @property $si_project_id
 * @property $si_sms_text
 * @property $si_created_dt
 * @property $si_message_sid
 * @property $si_num_segments
 * @property $si_to_country
 * @property $si_to_state
 * @property $si_to_city
 * @property $si_to_zip
 * @property $si_from_country
 * @property $si_from_city
 * @property $si_from_state
 * @property $si_from_zip
 */
class SmsIncomingForm extends Model
{
    public $si_id;
    public $si_sent_dt;
    public $si_phone_to;
    public $si_phone_from;
    public $si_project_id;
    public $si_sms_text;
    public $si_created_dt;
    public $si_message_sid;
    public $si_num_segments;
    public $si_to_country;
    public $si_to_state;
    public $si_to_city;
    public $si_to_zip;
    public $si_from_country;
    public $si_from_city;
    public $si_from_state;
    public $si_from_zip;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['si_id', 'safe'],

            ['si_sent_dt', 'string'],
            ['si_sent_dt', 'filter', 'filter' => static function($value) {
                return date('Y-m-d H:i:s', strtotime($value));
            }, 'skipOnEmpty' => true],

            ['si_phone_to', 'required'],
            ['si_phone_to', 'string', 'max' => 255],

            ['si_phone_from', 'required'],
            ['si_phone_from', 'string', 'max' => 255],

            ['si_project_id', 'integer'],
            ['si_project_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['si_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['si_project_id' => 'id']],
            ['si_project_id', function () {
                if (!$this->si_project_id && $this->si_project_id !== 0) {
                    $this->si_project_id = null;
                }
            }],

            ['si_sms_text', 'string'],

            ['si_created_dt', 'safe'],

            ['si_message_sid', 'string', 'max' => 400],

            ['si_num_segments', 'integer'],
            ['si_num_segments', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['si_to_country', 'string', 'max' => 5],

            ['si_to_state', 'string', 'max' => 30],

            ['si_to_city', 'string', 'max' => 30],

            ['si_to_zip', 'string', 'max' => 10],

            ['si_from_country', 'string', 'max' => 5],

            ['si_from_city', 'string', 'max' => 30],

            ['si_from_state', 'string', 'max' => 30],

            ['si_from_zip', 'string', 'max' => 10],
        ];
    }

    /**
     * @param int|null $projectId
     */
    public function replaceProject(?int $projectId): void
    {
        $this->si_project_id = $projectId;
    }
}

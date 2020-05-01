<?php

namespace frontend\widgets\newWebPhone\sms\dto;

use common\models\Client;
use common\models\Employee;
use common\models\Sms;
use yii\helpers\Html;

/**
 * Class SmsDto
 * @property $id
 * @property $type
 * @property $from
 * @property $to
 * @property $status
 * @property $text
 * @property $date
 * @property $time
 * @property $avatar
 * @property $name
 * @property $user
 * @property $contact
 * @property $group
 */
class SmsDto
{
    public $id;
    public $type;
    public $from;
    public $to;
    public $status;
    public $text;
    public $date;
    public $time;
    public $avatar;
    public $name;
    public $user;
    public $contact;

    private $group;

    /**
     * @param Sms|array $sms
     * @param Employee $user
     * @param Client $contact
     */
    public function __construct($sms, Employee $user, Client $contact)
    {
        $createdDt = Employee::convertTimeFromUtcToUserTime($user, strtotime($sms['s_created_dt']));
        $this->id = $sms['s_id'];
        $this->type = (int)$sms['s_type_id'];
        $this->from = $sms['s_phone_from'];
        $this->to = $sms['s_phone_to'];
        $this->status = (int)$sms['s_status_id'];
        $this->text = $sms['s_sms_text'];
        $this->date = $createdDt;
        $this->time = date('h:i A', strtotime($createdDt));
        $this->avatar = $this->isOut() ? $user->getAvatar() : $contact->getAvatar();
        $this->name = $this->isOut() ? 'Me' : $contact->getNameByType();
        $this->group = date('d M Y', strtotime($createdDt));
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'from' => $this->from,
            'to' => $this->to,
            'status' => $this->status,
            'text' => Html::encode($this->text),
            'date' => $this->date,
            'time' => $this->time,
            'avatar' => $this->avatar,
            'name' => $this->name,
            'group' => $this->group,
        ];
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    private function isOut(): bool
    {
        return $this->type === Sms::TYPE_OUTBOX;
    }
}

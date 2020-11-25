<?php

namespace sales\model\clientChat\useCase\hold;

use frontend\helpers\JsonHelper;
use sales\model\clientChat\entity\actionReason\ClientChatActionReasonQuery;
use sales\model\clientChat\useCase\close\ReasonDto;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use Yii;
use yii\base\Model;
use yii\helpers\Json;

/**
 * Class ClientChatTransferForm
 *
 * @property int $cchId
 * @property int $minuteToDeadline
 * @property string $comment
 * @property int|null $reasonId
 * @property ReasonDto[] $reasons
 */
class ClientChatHoldForm extends Model
{
    public $cchId;
    public $minuteToDeadline;
    public $comment;
    public $reasonId;

    public $reasons;

    /**
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->reasons = ClientChatActionReasonQuery::getReasons(ClientChatStatusLog::ACTION_HOLD);
    }

    public function rules(): array
    {
        return [
            [['cchId', 'minuteToDeadline'], 'required'],
            [['cchId', 'minuteToDeadline', 'reasonId'], 'integer'],
            [['cchId', 'minuteToDeadline'], 'filter', 'filter' => 'intval'],

            ['minuteToDeadline', 'validateMinuteToDeadline'],

            ['comment', 'string', 'max' => 255],

            ['reasonId', 'in', 'range' => array_keys($this->getReasonList())],

            ['comment', 'required', 'when' => function () {
                return (isset($this->reasons[$this->reasonId]) && $this->reasons[$this->reasonId]->isCommentRequired());
            }, 'skipOnError' => true],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'cchId' => 'Client Chat Id',
            'minuteToDeadline' => 'Deadline',
        ];
    }

    /**
     * @param $attribute
     */
    public function validateMinuteToDeadline($attribute): void
    {
        $deadlineOptions = JsonHelper::decode(Yii::$app->params['settings']['client_chat_hold_deadline_options']);

        if (!in_array($this->minuteToDeadline, $deadlineOptions, false)) {
            $this->addError($attribute, 'Minutes before Deadline id not valid');
        }
    }

    public function getReasonList(): array
    {
        $list = [];
        foreach ($this->reasons as $reason) {
            $list[$reason->id] = $reason->name;
        }
        return $list;
    }
}

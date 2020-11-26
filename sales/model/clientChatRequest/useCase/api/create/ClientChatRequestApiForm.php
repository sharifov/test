<?php

namespace sales\model\clientChatRequest\useCase\api\create;

use common\models\Project;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class ClientChatRequestApiForm
 * @package sales\model\clientChatRequest\useCase\api\create
 *
 * @property string $event
 * @property int $eventId
 * @property array $data
 * @property string|null $rid
 */
class ClientChatRequestApiForm extends Model
{
    public string $event;
    public ?int $eventId;
    public array $data;
    public ?string $rid;

    private const SCENARIO_ROOM_CONNECTED = 'ROOM_CONNECTED';

    private const SCENARIO_LIST = [
        self::SCENARIO_ROOM_CONNECTED
    ];

    public function rules(): array
    {
        return [
            [['event', 'rid'], 'string'],
            [['event'], 'in', 'range' => ClientChatRequest::getEventList()],

            ['data', 'safe'],
            ['data', 'validateTsParam'],
            ['data', 'validateProjectParam', 'on' => self::SCENARIO_ROOM_CONNECTED],
            ['data', 'validateMessageFromBot'],

            [['event'], 'required'],
            [['rid'], 'required', 'on' => self::SCENARIO_ROOM_CONNECTED],

            [['eventId'], 'integer'],
            [['eventId'], 'in', 'range' => ClientChatRequest::getEventIdList()],

        ];
    }

    public function fillIn(string $event, array $data): self
    {
        $this->event = $event;
        $this->data = $data;
        $this->eventId = ClientChatRequest::getEventIdByName($event);
        $this->rid = $data['rid'] ?? null;
        $this->scenario = in_array($this->event, self::SCENARIO_LIST) ? $this->event : 'default';
        return $this;
    }

    public function validateTsParam($attributes): void
    {
        if (ClientChatRequest::isMessage($this->eventId) && !isset($this->data['timestamp'])) {
            $this->addError('data', 'Undefined index: timestamp in data request');
        }
    }

    public function validateProjectParam($attribute): void
    {
        if (empty(trim($this->data['visitor']['project']))) {
            $this->addError('data', 'Project parameter is empty');
            return;
        }

        $project = Project::findOne(['project_key' => $this->data['visitor']['project']]);
        if (!$project) {
            $this->addError('data', 'Invalid Project');
            return;
        }
    }

    public function validateMessageFromBot()
    {
        if (
            ClientChatRequest::isMessage($this->eventId)
            && $this->eventId === ClientChatRequest::EVENT_AGENT_UTTERED
            && !empty($this->data['u']['username']) && $this->data['u']['username'] === 'bot'
        ) {
            $this->addError('data', 'Messages from the bot are not processed');
        }
    }
}

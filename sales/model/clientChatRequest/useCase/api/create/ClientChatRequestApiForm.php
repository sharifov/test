<?php
namespace sales\model\clientChatRequest\useCase\api\create;

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

	public function rules(): array
	{
		return [
			[['event', 'rid'], 'string'],
			[['event'], 'in', 'range' => ClientChatRequest::getEventList()],

			['data', 'safe'],
			['data', 'validateTsParam'],

			[['event'], 'required'],

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
		return $this;
	}

	public function validateTsParam($attributes): void
	{
		if (!isset($this->data['timestamp'])) {
			$this->addError('data', 'Undefined index: timestamp in data request');
		}
	}
}
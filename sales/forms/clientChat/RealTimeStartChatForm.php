<?php
namespace sales\forms\clientChat;

use sales\repositories\project\ProjectRepository;
use yii\helpers\Json;

/**
 * Class RealTimeStartChatForm
 * @package sales\forms\clientChat
 *
 * @property $rid string
 * @property $visitorId string
 * @property $message string
 * @property $projectId int|null
 * @property $projectName string
 * @property $visitorName string
 * @property $channelId int
 */
class RealTimeStartChatForm extends \yii\base\Model
{
	public string $rid = '';

	public string $visitorId = '';

	public string $message = '';

	public $projectId;

	public string $projectName = '';

	public int $channelId = 0;

	public string $visitorName = '';

	public function __construct(string $visitorId, string $projectName, ProjectRepository $projectRepository, string $visitorName, $config = [])
	{
		$this->visitorId = $visitorId;
		$this->projectName = $projectName;
		$this->projectId = $projectName ? $projectRepository->getIdByProjectKey($projectName) : null;
		$this->visitorName = $visitorName;
		parent::__construct($config);
	}

	public function rules(): array
	{
		return [
			[['rid', 'visitorId', 'message', 'visitorName'], 'string'],
			[['channelId', 'projectId'], 'integer'],
			[['visitorId', 'message', 'channelId'], 'required'],
			[['channelId'], 'filter', 'filter' => 'intval'],
			[['projectId'], 'default', 'value' => null],
			[['projectId'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
		];
	}

	public function dataToJson(): string
	{
		return Json::encode([
			'rid' => $this->rid,
			'visitor' => [
				'id' => $this->visitorId,
				'project' => $this->projectName,
				'name' => $this->visitorName
			]
		]);
	}
}
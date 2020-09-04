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

	public function __construct(string $rid, string $visitorId, string $projectName, ProjectRepository $projectRepository, $config = [])
	{
		$this->rid = $rid;
		$this->visitorId = $visitorId;
		$this->projectName = $projectName;
		$this->projectId = $projectRepository->getIdByName($projectName);
		parent::__construct($config);
	}

	public function rules(): array
	{
		return [
			[['rid', 'visitorId', 'message'], 'string'],
			[['channelId', 'projectId'], 'integer'],
			[['rid', 'visitorId', 'message', 'channelId'], 'required'],
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
			]
		]);
	}
}
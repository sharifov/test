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
 * @property $visitorEmail string
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

    public string $visitorEmail = '';

    public function __construct(string $visitorId, string $projectName, string $visitorName, string $visitorEmail, $config = [])
    {
        $this->visitorId = $visitorId;
        $this->projectName = $projectName;
        $this->visitorName = $visitorName;
        $this->visitorEmail = $visitorEmail;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['rid', 'visitorId', 'message', 'visitorName', 'visitorEmail'], 'string'],
            [['channelId', 'projectId'], 'integer'],
            [['visitorId', 'message', 'channelId'], 'required'],
            [['channelId'], 'filter', 'filter' => 'intval'],
            [['projectId'], 'default', 'value' => null],
            [['projectId'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            [['visitorEmail'], 'email', 'skipOnEmpty' => true]
        ];
    }

    public function dataToJson(): string
    {
        return Json::encode([
            'rid' => $this->rid,
            'visitor' => [
                'id' => $this->visitorId,
                'project' => $this->projectName,
                'name' => $this->visitorName,
                'email' => $this->visitorEmail
            ]
        ]);
    }
}

<?php

namespace sales\model\lead\useCases\lead\create;

use common\models\Project;
use common\models\Sources;
use common\models\VisitorLog;
use sales\model\clientChat\entity\ClientChat;
use yii\base\Model;

/**
 * Class LeadCreateByChatForm
 *
 * @property integer $source
 * @property integer $projectId
 * @property int|null $userId
 * @property ClientChat $chat
 */
class LeadCreateByChatForm extends Model
{
    public $source;
    public $projectId;

    private $userId;
    private $chat;

    /**
     * @param int $userId
     * @param ClientChat $chat
     * @param array $config
     */
    public function __construct(int $userId, ClientChat $chat, $config = [])
    {
        $this->userId = $userId;
        $this->chat = $chat;
        $this->setProjectId();
        $this->setSource();
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['projectId', 'required'],
            ['source', 'integer'],
            ['source', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['source', 'sourceProjectValidate'],
            ['source', 'emptyStringToNull', 'skipOnEmpty' => false],

            ['projectId', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['projectId' => 'id']],
            ['projectId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
        ];
    }

    public function emptyStringToNull(): void
    {
        $this->source = $this->source === '' ? null : $this->source;
    }

    public function sourceProjectValidate(): void
    {
        if ($this->source && !Sources::findOne(['id' => $this->source, 'project_id' => $this->projectId])) {
            $this->source = null;
        }
    }

    private function setProjectId(): LeadCreateByChatForm
    {
        $this->projectId = $this->chat->cch_project_id;
        return $this;
    }

    private function setSource(): LeadCreateByChatForm
    {
        if ($sourceId = $this->getSourceIdByLogs()) {
            $this->source = $sourceId;
            return $this;
        }
        return $this;
    }

    private function getSourceIdByLogs(): ?int
    {
        if ($this->chat->ccv && $clientChatVisitorData = $this->chat->ccv->ccvCvd) {
            /** @var VisitorLog $visitorLog */
            if (($visitorLog = $this->getVisitorLogByCvdId($clientChatVisitorData->cvd_id)) && $visitorLog->vl_source_cid) {
                if ($source = Sources::findOne(['cid' => $visitorLog->vl_source_cid, 'project_id' => $this->projectId])) {
                    return $source->id;
                }
            }
        }
        return null;
    }

    /**
     * @param int $cvdId
     * @return array|\yii\db\ActiveRecord|null
     */
    private function getVisitorLogByCvdId(int $cvdId)
    {
        return VisitorLog::find()
            ->byCvdId($cvdId)
            ->orderBy(['vl_id' => SORT_DESC])
            ->one();
    }

    public function attributeLabels(): array
    {
        return [
            'source' => 'Marketing Info:',
        ];
    }
}
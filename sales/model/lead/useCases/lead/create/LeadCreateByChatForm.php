<?php

namespace sales\model\lead\useCases\lead\create;

use common\models\Sources;
use sales\access\EmployeeProjectAccess;
use sales\access\ListsAccess;
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

	public function __construct(int $userId, ClientChat $chat, $config = [])
	{
        $this->userId = $userId;
        $this->chat = $chat;
        parent::__construct($config);
    }

	public function rules(): array
	{
		return [
			['source', 'required'],
			['source', 'integer'],
			['source', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
			['source', 'exist', 'skipOnError' => true, 'targetClass' => Sources::class, 'targetAttribute' => ['source' => 'id']],
			['source', function () {
				if ($projectId = Sources::find()->select('project_id')->where(['id' => $this->source])->asArray()->limit(1)->one()) {
					$this->projectId = $projectId['project_id'];
					if (!EmployeeProjectAccess::isInProject($this->projectId, $this->userId)) {
						$this->addError('source', 'Access denied for this project');
					}
				} else {
					$this->addError('source', 'Project not found');
				}
			}],
            ['source', 'listSourceValidate', 'skipOnError' => true, 'skipOnEmpty' => true],
		];
	}

    public function listSourceValidate(): void
    {
        foreach ($this->listSources() as $projectName => $sources) {
            foreach ($sources as $key => $source) {
                if ($this->source === $key) {
                    return;
                }
            }
        }
        $this->addError('source', 'Invalid source');
	}

	public function listSources(): array
	{
	    $sources = (new ListsAccess($this->userId))->getSources();

	    if ($project = $this->chat->cchProject) {
	        foreach ($sources as $projectName => $source) {
	            if (strtolower($project->name) === strtolower($projectName)) {
                    return [
                        $projectName => $source
                    ];
                }
            }
        }

		return $sources;
	}

	public function attributeLabels(): array
	{
		return [
			'source' => 'Marketing Info:',
		];
	}
}

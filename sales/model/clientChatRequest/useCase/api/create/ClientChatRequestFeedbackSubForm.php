<?php

namespace sales\model\clientChatRequest\useCase\api\create;

use common\models\Project;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatFeedback\entity\ClientChatFeedback;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class ClientChatRequestFeedbackSubForm
 *
 * @property array $data
 * @property string|null $rid
 * @property string|null $comment
 * @property int|null $rating
 * @property string|null $visitorId
 * @property string|null $projectKey
 */
class ClientChatRequestFeedbackSubForm extends Model
{
    public array $data;
    public ?string $rid;
    public $comment;
    public $rating;
    public $visitorId;
    public $projectKey;

    public function rules(): array
    {
        return [
            [['rid', 'projectKey'], 'required'],
            ['data', 'safe'],

            [['comment', 'rid', 'visitorId', 'projectKey'], 'string'],

            [['rating'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            [['rating'], 'integer'],
            [['rating'], 'in', 'range' => ClientChatFeedback::RATING_LIST],

            [['projectKey'], 'validateProject'],
            [['rid'], 'validateClientChat'],
            [['comment', 'rating'], 'validateCommentRating', 'skipOnEmpty' => false],
        ];
    }

    public function fillIn(array $data): self
    {
        $this->data = $data;
        $this->rid = $data['rid'] ?? null;
        $this->comment = $data['comment'] ?? null;
        $this->rating = $data['rating'] ?? null;
        $this->rid = $data['rid'] ?? null;
        $this->visitorId = $data['visitor']['id'] ?? null;
        $this->projectKey = $data['visitor']['project'] ?? null;

        return $this;
    }

    /**
     * @param $attribute
     */
    public function validateClientChat($attribute): void
    {
        if (!ClientChat::findOne(['cch_rid' => $this->rid])) {
            $this->addError($attribute, 'ClientChat not found.');
        }
    }

    /**
     * @param $attribute
     */
    public function validateProject($attribute): void
    {
        if (!Project::findOne(['project_key' => $this->projectKey])) {
            $this->addError($attribute, 'Project not found.');
        }
    }

    public function validateCommentRating(): void
    {
        if (empty($this->comment) && empty($this->rating)) {
            $this->addError('comment', 'Comment or rating must be filled.');
            $this->addError('rating', 'Rating or comment must be filled.');
        }
    }
}

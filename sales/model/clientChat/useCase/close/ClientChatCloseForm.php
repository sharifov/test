<?php


namespace sales\model\clientChat\useCase\close;


use sales\model\clientChat\entity\actionReason\ClientChatActionReason;
use sales\model\clientChat\entity\actionReason\ClientChatActionReasonQuery;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use yii\base\Model;

/**
 * Class ClientChatCloseForm
 * @package sales\model\clientChat\useCase\close
 *
 * @property int $cchId
 * @property string|null $comment
 * @property ReasonDto[] $reasons
 * @property int $reasonId
 */
class ClientChatCloseForm extends Model
{
	/**
	 * @var int $cchId
	 */
	public $cchId;
	/**
	 * @var string|null $comment
	 */
	public $comment;
	/**
	 * @var int $reasonId
	 */
	public $reasonId;
	/**
	 * @var ReasonDto[] $reasons
	 */
	public $reasons;

	public function __construct($config = [])
	{
		parent::__construct($config);
		$this->reasons = ClientChatActionReasonQuery::getReasons(ClientChatStatusLog::ACTION_CLOSE);
	}

	public function rules(): array
	{
		return [
			['cchId', 'integer'],
			['cchId', 'required'],
			['cchId', 'filter', 'filter' => 'intval'],
			['reasonId', 'in', 'range' => array_keys($this->getReasonList())],
			['comment', 'string', 'max' => 100],
			['comment', 'required', 'when' => function () {
				return (isset($this->reasons[$this->reasonId]) && $this->reasons[$this->reasonId]->isCommentRequired());
			}, 'skipOnError' => true],
		];
	}

	public function getReasonList(): array
	{
		$list = [];
		foreach ($this->reasons as $reason) {
			$list[$reason->id] = $reason->name;
		}
		return $list;
	}

	public function attributeLabels(): array
	{
		return [
			'reasonId' => 'Reason',
			'comment' => 'Comment'
		];
	}
}
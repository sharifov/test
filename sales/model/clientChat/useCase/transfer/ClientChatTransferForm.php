<?php
namespace sales\model\clientChat\useCase\transfer;

use sales\model\clientChat\entity\actionReason\ClientChatActionReasonQuery;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use yii\base\Model;

/**
 * Class ClientChatTransferForm
 * @package sales\model\clientChat\useCase\transfer
 *
 * @property int $cchId
 * @property int|null $depId
 * @property int|null $isOnline
 * @property int|null $pjaxReload
 * @property array|null $agentId
 */
class ClientChatTransferForm extends Model
{
	/**
	 * @var int
	 */
	public $pjaxReload;

	/**
	 * @var int
	 */
	public $cchId;

	/**
	 * @var int|null
	 */
	public $depId;

	/**
	 * @var int|null
	 */
	public $isOnline;

	/**
	 * @var array|null
	 */
	public $agentId;

	/**
	 * @var int
	 */
	public $reasonId;

	/**
	 * @var ReasonDto[]
	 */
	public $reasons;

	/**
	 * @var string|null
	 */
	public $comment;

	public function __construct($config = [])
	{
		parent::__construct($config);
		$this->reasons = ClientChatActionReasonQuery::getReasons(ClientChatStatusLog::ACTION_TRANSFER);
	}

	public function rules(): array
	{
		return [
			[['cchId', 'depId', 'isOnline', 'pjaxReload'], 'integer'],
			[['cchId', 'depId', 'isOnline', 'pjaxReload'], 'default', 'value' => null],
			[['cchId', 'depId'], 'required'],
			[['cchId', 'depId', 'isOnline'], 'filter', 'filter' => 'intval'],
			[['agentId'], 'filter', 'filter' => static function ($value) {
				if (empty($value)) {
					return [];
				}
				return $value;
			}],
			['agentId', 'each', 'rule' => ['integer']],
			['agentId', 'each', 'rule' => ['filter', 'filter' => 'intval']],
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
			'cchId' => 'Client Chat Id',
			'depId' => 'Department',
			'isOnline' => 'Client Network Status',
			'agentId' => 'Agents',
			'reasonId' => 'Reason',
			'comment' => 'Comment'
		];
	}
}
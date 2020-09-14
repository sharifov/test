<?php
namespace sales\model\clientChat\useCase\transfer;

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
		];
	}

	public function attributeLabels(): array
	{
		return [
			'cchId' => 'Client Chat Id',
			'depId' => 'Department',
			'isOnline' => 'Client Network Status',
			'agentId' => 'Agents'
		];
	}
}
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
 */
class ClientChatTransferForm extends Model
{
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

	public function rules(): array
	{
		return [
			[['cchId', 'depId', 'isOnline'], 'integer'],
			[['cchId', 'depId', 'isOnline'], 'default', 'value' => null],
			[['cchId', 'depId'], 'required'],
			[['cchId', 'depId', 'isOnline'], 'filter', 'filter' => 'intval'],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'cchId' => 'Client Chat Id',
			'depId' => 'Department',
			'isOnline' => 'Client Network Status'
		];
	}
}
<?php
namespace sales\model\clientChat\useCase\transfer;

use yii\base\Model;

/**
 * Class ClientChatTransferForm
 * @package sales\model\clientChat\useCase\transfer
 *
 * @property int $cchId
 * @property int|null $depId
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

	public function rules(): array
	{
		return [
			[['cchId', 'depId'], 'integer'],
			[['cchId', 'depId'], 'default', 'value' => null],
			[['cchId', 'depId'], 'required'],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'cchId' => 'Client Chat Id',
			'depId' => 'Department'
		];
	}
}
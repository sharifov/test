<?php


namespace sales\model\clientChat\useCase\close;


use yii\base\Model;

/**
 * Class ClientChatCloseForm
 * @package sales\model\clientChat\useCase\close
 *
 * @property int $cchId
 * @property string|null $comment
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

	public function rules(): array
	{
		return [
			['cchId', 'integer'],
			['cchId', 'required'],
			['cchId', 'filter', 'filter' => 'intval'],
			['comment', 'string', 'max' => 255]
		];
	}
}
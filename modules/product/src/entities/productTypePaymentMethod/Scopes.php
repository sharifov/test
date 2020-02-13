<?php
namespace modules\product\src\entities\productTypePaymentMethod;

/**
 * Class Scopes
 * @package modules\product\src\entities\productTypePaymentMethod
 */
class Scopes extends \yii\db\ActiveQuery
{
	/**
	 * {@inheritdoc}
	 * @return ProductTypePaymentMethod[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * {@inheritdoc}
	 * @return ProductTypePaymentMethod|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}
}
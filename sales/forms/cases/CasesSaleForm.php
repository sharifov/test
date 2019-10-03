<?php

namespace sales\forms\cases;

use yii\base\Model;
use yii\helpers\Html;
use Yii;
use yii\validators\Validator;

/**
 * Class CasesSaleForm
 * @package sales\forms\cases
 */
class CasesSaleForm extends Model
{
	/**
	 * @var array
	 */
	public $passengers;

	/**
	 * @var integer
	 */
	public $ff_numbers;

	/**
	 * @var string
	 */
	public $ktn_numbers;

	/**
	 * @var string
	 */
	private $dateFormat = 'php:Y-m-d';

	/**
	 * @var array
	 */
	private $filters = [
		'birth_date' => 'birthDateFilter',
	];

	/**
	 * @var array
	 */
	private $validators = [
		'ff_numbers' => 'onlyNumbers'
	];

	/**
	 * @var array
	 */
	public $validatedData = [];

	/**
	 * CasesSaleForm constructor.
	 * @param array $config
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);
	}

	/**
	 * @return array
	 */
	public function rules()
	{
		return [
			[['passengers'], 'each', 'rule' => ['filter', 'filter' => function ($value) {
				foreach ($value as $key => $item) {
					if (isset($this->filters[$key]) && is_callable($this->{$this->filters[$key]})) {
						$this->{$this->filters[$key]}($value, $key);
					}
				}
				return $value;
			}]],
			[['passengers'], 'each', 'rule' =>[function () {
				if (is_array($this->passengers)) {
					foreach ($this->passengers as $key => $passenger) {
						if (isset($this->validators[$key]) && is_callable($this->{$this->validators[$key]}) && !empty($passenger)) {
							$this->{$this->validators[$key]}($key, $passenger);
						}
					}
				}
			}]],
		];
	}

	public function afterValidate()
	{
		parent::afterValidate();
		$this->validatedData['passengers'] = $this->passengers;
	}

	/**
	 * @return array
	 */
	public function attributeLabels(): array
	{
		return [
			'passengers' => 'Passengers',
			'ff_numbers' => 'Frequent Fayer',
			'ktn_numbers' => 'KTN'
		];
	}

	/**
	 * @param $value
	 * @param $key
	 * @throws \yii\base\InvalidConfigException
	 */
	private function birthDateFilter(&$value, $key) {
		$value[$key] = Yii::$app->formatter->asDate(Html::encode($value[$key]), $this->dateFormat);
	}

	/**
	 * @param $attribute
	 * @param $value
	 */
	private function onlyNumbers($attribute, $value) {
		if (!preg_match('/^[0-9]+$/', Html::encode($value))) {
			$this->addError($attribute, $this->getAttributeLabel($attribute) . ' should contain only numbers.');
		}
	}

	/**
	 * @param $attribute
	 * @param $value
	 */
	private function onlyNumbersAndLetters($attribute, $value) {
		if (!preg_match('/^[0-9A-Za-z]+$/', Html::encode($value))) {
			$this->addError($attribute, $this->getAttributeLabel($attribute) . ' should contain only numbers and letters.');
		}
	}
}
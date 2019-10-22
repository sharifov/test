<?php

namespace sales\forms\cases;

use common\models\CaseSale;
use sales\services\cases\CasesSaleService;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\Html;
use Yii;

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
	private $dateFormat = 'Y-m-d';

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
		'birth_date' => 'birthDateRangeValidator',
		'ff_numbers' => 'onlyNumbers',
		'kt_numbers' => 'onlyNumbersAndLetters',
	];

	/**
	 * @var CasesSaleService
	 */
	private $caseSaleService;

	/**
	 * @var CaseSale
	 */
	private $caseSale;

	/**
	 * @var array
	 */
	public $validatedData = [];

	/**
	 * CasesSaleForm constructor.
	 * @param CaseSale $caseSale
	 * @param CasesSaleService $casesSaleService
	 * @param array $config
	 * @throws \Exception
	 */
	public function __construct(CaseSale $caseSale, CasesSaleService $casesSaleService, $config = [])
	{
		parent::__construct($config);

		if (!$caseSale) throw new \Exception('Error occurred when validate case sale: Data of the Case Sale is not found;');

		$this->caseSale = $caseSale;
		$this->caseSaleService = $casesSaleService;
	}

	/**
	 * @return array
	 */
	public function rules()
	{
		return [
			[['passengers'], 'each', 'rule' => ['filter', 'filter' => function ($value) {
				foreach ($value as $key => $item) {

					if (isset($this->filters[$key]) && method_exists($this, $this->filters[$key])) {
						$value[$key] = trim($value[$key]);
						$this->{$this->filters[$key]}($value, $key);
					}
				}

				return $value;
			}]],
			[['passengers'], 'each', 'rule' =>[function () {
				if (is_array($this->passengers)) {
					foreach ($this->passengers as $key => $passenger) {
						if (isset($this->validators[$key]) && method_exists($this, $this->validators[$key]) && !empty($passenger)) {
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

		$this->caseSaleService->setSegments($this->caseSale);

		foreach ($this->passengers as $key => $passenger) {
			$this->caseSaleService->formatPassengersData($this->passengers[$key]);
		}

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
			'kt_numbers' => 'KTN'
		];
	}

	/**
	 * @param $value
	 * @param $key
	 * @throws InvalidConfigException
	 */
	private function birthDateFilter(&$value, $key)
	{
		$value[$key] = date($this->dateFormat, strtotime(Html::encode($value[$key])));
	}

	/**
	 * @param $attribute
	 * @param $value
	 */
	private function onlyNumbers($attribute, $value)
	{
		if (!preg_match('/^[0-9]+$/', Html::encode($value))) {
			$this->addError($attribute, $this->getAttributeLabel($attribute) . ' should contain only numbers.');
		}
	}

	/**
	 * @param $attribute
	 * @param $value
	 */
	private function onlyNumbersAndLetters($attribute, $value)
	{
		if (!preg_match('/^[0-9A-Za-z]+$/', Html::encode($value))) {
			$this->addError($attribute, $this->getAttributeLabel($attribute) . ' should contain only numbers and letters.');
		}
	}

	/**
	 * @param $attribute
	 * @param $value
	 * @throws \Exception
	 */
	private function birthDateRangeValidator($attribute, $value)
	{
		$currentDate = date('Y-m-d');

		$birthDate = new \DateTime($value);
		$type = $this->passengers['type'] ?? null;

		if (!$type) {
			$this->addError($attribute, $this->getAttributeLabel($attribute) . ': cant validate, passenger type is not provided.');
		}

		$passengerBirthDateRange = CaseSale::PASSENGER_TYPE_BIRTH_DATE_RANGE[$type] ?? null;

		if (!$passengerBirthDateRange) {
			$this->addError($attribute, $this->getAttributeLabel($attribute) . ': cant validate, passenger birth date range is not found.');
		}

		$segments = $this->caseSaleService->getSegments($this->caseSale);

		if (empty($segments)) {
			$this->addError($attribute, $this->getAttributeLabel($attribute) . ': segments missing from case sales information;');
		}

		$lastDepartureTime = end($segments)['departureTime'] ?? null;

		if (!$lastDepartureTime) {
			$this->addError($attribute, $this->getAttributeLabel($attribute) . ': Departure Time of last segment is missing;');
		}

		$age = $birthDate->diff(new \DateTime($lastDepartureTime))->y;

		if ($age > $passengerBirthDateRange['max']) {
			$this->addError($attribute, $this->getAttributeLabel($attribute) . ': you cant set birth date that is not in range;');
		}
	}
}
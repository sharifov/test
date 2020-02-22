<?php

namespace modules\flight\models\forms;

use modules\product\src\entities\product\Product;
use modules\flight\models\Flight;
use modules\flight\models\FlightSegment;
use modules\flight\src\helpers\FlightFormatHelper;
use sales\forms\CompositeForm;

/**
 * Class ItineraryEditForm
 * @package modules\flight\models\forms
 *
 * @property int $flightId
 * @property int $productId
 * @property string $cabin
 * @property int $adults
 * @property int $children
 * @property int $infants
 * @property int $tripType
 * @property FlightSegmentEditForm[] $segments
 * @property $pr_market_price
 * @property $pr_client_budget
 */
class ItineraryEditForm extends CompositeForm
{
	public $flightId;
	public $productId;
	public $cabin;
	public $adults;
	public $children;
	public $infants;
	public $tripType;
	public $pr_market_price;
	public $pr_client_budget;

//	public $segments;

	/**
	 * ItineraryEditForm constructor.
	 * @param Flight $flight
	 * @param int|null $countSegmentForms
	 * @param array $config
	 */
	public function __construct(Flight $flight, int $countSegmentForms = null, $config = [])
	{
		$this->flightId = $flight->fl_id;
		$this->productId = $flight->fl_product_id;
		$this->cabin = $flight->fl_cabin_class;
		$this->adults = $flight->fl_adults;
		$this->children = $flight->fl_children;
		$this->infants = $flight->fl_infants;
		$this->tripType = $flight->fl_trip_type_id;
		$this->pr_market_price = $flight->flProduct->pr_market_price;
		$this->pr_client_budget = $flight->flProduct->pr_client_budget;

		$this->segments = array_map(static function ($segment) {
			return new FlightSegmentEditForm($segment);
		}, $this->getSegmentsForms($flight, $countSegmentForms));

		parent::__construct($config);
	}

	/**
	 * @return array
	 */
	public function rules(): array
	{
		return [
			[['flightId', 'productId'], 'required'],
			[['flightId', 'productId'], 'integer'],
			['flightId', 'exist', 'targetClass' => Flight::class, 'targetAttribute' => ['flightId' => 'fl_id']],
			['productId', 'exist', 'targetClass' => Product::class, 'targetAttribute' => ['productId' => 'pr_id']],

			['cabin', 'required'],
			['cabin', 'string', 'max' => 1],
			['cabin', 'in', 'range' => array_keys(Flight::getCabinClassList())],

			[['adults', 'children', 'infants'], 'required'],
			[['adults', 'children', 'infants'], 'integer', 'min' => 0, 'max' => 9],
			[['adults', 'children', 'infants'], 'in', 'range' => array_keys(FlightFormatHelper::adultsChildrenInfantsList())],

			['adults', function () {
				if (!$this->adults && !$this->children) {
					$this->addError('adults', 'Adults or Children must be more 0.');
				}
			}],

			['infants', function () {
				if ($this->infants > $this->adults) {
					$this->addError('infants', 'Infants must be no greater than Adults');
				}
			}],

			['segments', function () {
				if ( !is_array($this->segments)) {
					return;
				}
				foreach ($this->segments as $key => $segment) {
					if (isset($this->segments[$key - 1])) {
						$dateFrom = strtotime($this->segments[$key - 1]->fs_departure_date);
						$dateTo = strtotime($this->segments[$key]->fs_departure_date);
						if ($dateTo < $dateFrom) {
							$this->addError('segments[' . $key . '][fs_departure_date]', 'Date can not be less than the date of the previous segment');
						}
					}
				}
			}],

            ['pr_market_price', 'default', 'value' => null],
            ['pr_market_price', 'number'],

            ['pr_client_budget', 'default', 'value' => null],
            ['pr_client_budget', 'number'],
		];
	}

    public function attributeLabels(): array
    {
        return [
            'pr_market_price' => 'Market price',
            'pr_client_budget' => 'Client budget',
        ];
    }

	/**
	 * @inheritDoc
	 */
	protected function internalForms(): array
	{
		return ['segments'];
	}

	/**
	 * @param Flight $flight
	 * @param int|null $countSegmentForms
	 * @return array
	 */
	private function getSegmentsForms(Flight $flight, int $countSegmentForms = null): array
	{
		$countRelations = $flight->getFlightSegments()->count();

		if ($countSegmentForms === null || ($countSegmentForms === $countRelations)) {
			return $flight->getFlightSegments()->orderBy(['fs_departure_date' => SORT_ASC])->all();
		}
		if ($countSegmentForms > $countRelations) {
			$segmentForms = $flight->getFlightSegments()->orderBy(['fs_departure_date' => SORT_ASC])->all();
			for ($i = 0; $i < ($countSegmentForms - $countRelations); $i++) {
				$segmentForms[] = new FlightSegment();
			}
			return $segmentForms;
		}
		return $flight->getFlightSegments()->orderBy(['fs_departure_date' => SORT_ASC])->limit($countSegmentForms)->all();
	}
}
<?php

namespace modules\order\src\services\createFromSale;

use common\components\SearchService;
use common\components\validators\IsArrayValidator;
use common\models\Currency;
use common\models\Project;
use modules\flight\src\useCases\sale\FlightFromSaleService;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class OrderCreateFromSaleForm
 *
 * @property $saleId
 * @property $project
 * @property $bookingId
 * @property string|null $baseBookingId
 * @property $pnr
 * @property $price
 * @property $tripType
 * @property $validatingCarrier
 * @property $gds
 * @property $pcc
 * @property $fareType
 * @property $phone
 * @property $email
 *
 * @property $projectId
 * @property $tripTypeId
 * @property $gdsId
 * @property $currency
 */
class OrderCreateFromSaleForm extends Model
{
    public $saleId;
    public $project;
    public $bookingId;
    public $baseBookingId;
    public $pnr;
    public $price;
    public $tripType;
    public $validatingCarrier;
    public $gds;
    public $pcc;
    public $fareType;
    public $phone;
    public $email;

    private ?int $projectId;
    private ?string $tripTypeId;
    private ?string $gdsId;
    private ?string $currency = null;

    public function rules(): array
    {
        return [
            [['saleId'], 'required'],
            [['saleId'], 'integer'],
            [['saleId'], 'filter', 'filter' => 'intval', 'skipOnError' => true],

            [['project'], 'required'],
            [['project'], 'string'],
            [['project'], 'detectProjectId'],

            [['bookingId', 'baseBookingId'], 'string', 'max' => 50],
            [['baseBookingId'], 'baseBookingIdHandle'],

            [['pnr'], 'string', 'max' => 70],

            [['tripType'], 'string', 'max' => 50],
            [['tripType'], 'detectTripTypeId'],

            [['price'], IsArrayValidator::class],
            [['price'], 'detectCurrency'],

            [['validatingCarrier'], 'string', 'max' => 2],

            [['gds'], 'string', 'max' => 50],
            [['gds'], 'detectGdsId'],

            [['pcc'], 'string', 'max' => 70],

            [['fareType'], 'string', 'max' => 255],

            ['phone', 'string', 'skipOnEmpty' => true],
            ['email', 'email', 'skipOnEmpty' => true],
        ];
    }

    public function baseBookingIdHandle(): void
    {
        if (empty($this->baseBookingId)) {
            $this->baseBookingId = $this->bookingId;
        }
    }

    public function detectProjectId($attribute): void
    {
        if ($project = Project::findOne(['name' => $this->project])) {
            $this->projectId = $project->id;
        } else {
            $this->addError($attribute, 'Project not found by name (' . $this->project . ')');
        }
    }

    public function detectCurrency(): void
    {
        $this->currency = ArrayHelper::getValue($this->price, 'currency');
    }

    public function detectTripTypeId($attribute): void
    {
        if (!$this->tripTypeId = FlightFromSaleService::getFlightTripIdByName($this->tripType)) {
            $this->addError($attribute, 'Trip tape ID not detected by (' . $this->tripType . ')');
        }
    }

    public function detectGdsId($attribute): void
    {
        $this->gdsId = SearchService::getGDSKeyByName($this->gds);
    }

    public function getTripTypeId(): ?string
    {
        return $this->tripTypeId;
    }

    public function getProjectId(): ?int
    {
        return $this->projectId;
    }

    public function getGdsId(): ?string
    {
        return $this->gdsId;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function formName(): string
    {
        return '';
    }
}

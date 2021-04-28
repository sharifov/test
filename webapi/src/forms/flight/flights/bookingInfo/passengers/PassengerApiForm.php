<?php

namespace  webapi\src\forms\flight\flights\bookingInfo\passengers;

use frontend\helpers\JsonHelper;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class PassengerApiForm
 *
 * @property $fullName
 * @property $first_name
 * @property $middle_name
 * @property $last_name
 * @property $birth_date
 * @property $nationality
 * @property $gender
 * @property $tktNumber
 * @property $paxType
 *
 * @property int $keyIdentity
 */
class PassengerApiForm extends Model
{
    public $fullName;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $birth_date;
    public $nationality;
    public $gender;
    public $tktNumber;
    public $paxType;

    private int $keyIdentity;

    /**
     * @param int $keyIdentity
     * @param array $config
     */
    public function __construct(int $keyIdentity, $config = [])
    {
        $this->keyIdentity = $keyIdentity;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['paxType', 'tktNumber'], 'required'],

            [['gender', 'nationality'], 'filter', 'filter' => 'trim', 'skipOnEmpty' => true],
            [['gender', 'nationality'], 'filter', 'filter' => 'strtoupper', 'skipOnEmpty' => true],

            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 40],
            [['fullName'], 'string', 'max' => 120],

            [['birth_date'], 'date', 'format' => 'php:Y-m-d'],

            [['nationality'], 'string', 'max' => 5],

            [['gender'], 'string', 'max' => 1],

            [['tktNumber'], 'string', 'max' => 50],

            [['paxType'], 'string', 'max' => 3],
        ];
    }

    public function formName(): string
    {
        return '';
    }

    public function getKeyIdentity(): int
    {
        return $this->keyIdentity;
    }

    public function getHashIdentity(): string
    {
        return md5(
            $this->paxType .
            $this->gender .
            $this->first_name .
            $this->middle_name .
            $this->last_name .
            $this->birth_date
        );
    }
}

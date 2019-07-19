<?php

namespace sales\forms\api\communication\voice\incoming;

use yii\base\Model;

/**
 * Class CallForm
 * @property $Called
 * @property $ToState
 * @property $CallerCountry
 * @property $Direction
 * @property $CallerState
 * @property $ToZip
 * @property $CallSid
 * @property $ParentCallSid
 * @property $To
 * @property $From
 * @property $CallerZip
 * @property $ToCountry
 * @property $ApiVersion
 * @property $CalledZip
 * @property $CalledCity
 * @property $CallStatus
 * @property $AccountSid
 * @property $CalledCountry
 * @property $CallerCity
 * @property $ApplicationSid
 * @property $Caller
 * @property $FromCountry
 * @property $ToCity
 * @property $FromCity
 * @property $CalledState
 * @property $FromZip
 * @property $FromState
 * @property $Digits
 *
 * @property $callerPhone
 * @property $calledPhone
 */
class CallForm extends Model
{
    public $Called;
    public $ToState;
    public $CallerCountry;
    public $Direction;
    public $CallerState;
    public $ToZip;
    public $CallSid;
    public $ParentCallSid;
    public $To;
    public $From;
    public $CallerZip;
    public $ToCountry;
    public $ApiVersion;
    public $CalledZip;
    public $CalledCity;
    public $CallStatus;
    public $AccountSid;
    public $CalledCountry;
    public $CallerCity;
    public $ApplicationSid;
    public $Caller;
    public $FromCountry;
    public $ToCity;
    public $FromCity;
    public $CalledState;
    public $FromZip;
    public $FromState;
    public $Digits;

    public $callerPhone;
    public $calledPhone;

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        try {
            foreach ((new \ReflectionClass($this))->getProperties() as $property) {
                if ($this->{$property->name} !== null) {
                    return false;
                }
            }
        } catch (\Exception $e) {}
        return true;
    }

    /**
     * @param array $data
     * @param null $formName
     * @return bool
     */
    public function load($data, $formName = null): bool
    {
        if (parent::load($data, $formName)) {
            $this->setPhones();
            return true;
        }
        return false;
    }

    private function setPhones(): void
    {
        if ($this->From) {
            $this->callerPhone = $this->From;
        }
        if ($this->To) {
            $this->calledPhone = $this->Called;
        }
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['Called', 'string'],
            ['ToState', 'string'],
            ['CallerCountry', 'string'],
            ['Direction', 'in', 'range' => ['inbound']],
            ['CallerState', 'string'],
            ['ToZip', 'string'],
            ['CallSid', 'string'],
            ['ParentCallSid', 'string'],
            ['To', 'string'],
            ['From', 'string'],
            ['CallerZip', 'string'],
            ['ToCountry', 'string'],
            ['ApiVersion', 'date', 'format' => 'Y-m-d'],
            ['CalledZip', 'string'],
            ['CalledCity', 'string'],
            ['CallStatus', 'in', 'range' => ['ringing']],
            ['AccountSid', 'string'],
            ['CalledCountry', 'string'],
            ['CallerCity', 'string'],
            ['ApplicationSid', 'string'],
            ['Caller', 'string'],
            ['FromCountry', 'string'],
            ['ToCity', 'string'],
            ['FromCity', 'string'],
            ['CalledState', 'string'],
            ['FromZip', 'string'],
            ['FromState', 'string'],
            ['Digits', 'string'],
            ['callerPhone', 'string'],
            ['calledPhone', 'string'],
        ];
    }

}
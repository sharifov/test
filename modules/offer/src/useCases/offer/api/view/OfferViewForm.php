<?php

namespace modules\offer\src\useCases\offer\api\view;

use sales\yii\validators\IsNotArrayValidator;
use yii\base\Model;

/**
 * Class OfferViewForm
 *
 * @property string $offerGid
 * @property string $visitorId
 * @property string $ipAddress
 * @property string $userAgent
 */
class OfferViewForm extends Model
{
    public $offerGid;
    public $visitorId;
    public $ipAddress;
    public $userAgent;

    public function rules(): array
    {
        return [
            ['offerGid', 'required'],
            ['offerGid', 'string'],

            ['visitorId', 'string', 'max' => '32'],
            ['visitorId', IsNotArrayValidator::class],

            ['ipAddress', 'string', 'max' => 40],
            ['ipAddress', IsNotArrayValidator::class],

            ['userAgent', 'string', 'max' => 255],
            ['userAgent', IsNotArrayValidator::class],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}

<?php

namespace sales\model\coupon\useCase\request;

use sales\entities\cases\Cases;
use yii\base\Model;

/**
 * Class RequestForm
 *
 * @property int $caseId
 * @property int $count
 * @property string $code
 */
class RequestForm extends Model
{
    public const CODE_USD50 = 'USD50';

    public const CODE_LIST = [
        self::CODE_USD50 => self::CODE_USD50,
    ];

    public $count;
    public $code;
    public $caseId;

    public function __construct(int $caseId, $config = [])
    {
        $this->caseId = $caseId;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['count', 'required'],
            ['count', 'integer', 'min' => 1, 'max' => 9],

            ['code', 'required'],
            ['code', 'in', 'range' => array_keys(self::CODE_LIST)],

            ['caseId', 'required'],
            ['caseId', 'exist', 'targetAttribute' => 'cs_id', 'targetClass' => Cases::class],
        ];
    }
}

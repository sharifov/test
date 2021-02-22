<?php

namespace sales\model\coupon\useCase\request;

use common\models\Employee;
use sales\auth\Auth;
use sales\entities\cases\Cases;
use yii\base\Model;

/**
 * Class RequestForm
 *
 * @property int $caseId
 * @property int $count
 * @property string $code
 * @property int $userId
 */
class RequestForm extends Model
{
    public const CODE_USD25 = 'USD25';
    public const CODE_USD50 = 'USD50';
    public const CODE_USD100 = 'USD100';

    public const CODE_LIST = [
        self::CODE_USD25 => self::CODE_USD25,
        self::CODE_USD50 => self::CODE_USD50,
        self::CODE_USD100 => self::CODE_USD100,
    ];

    public $count;
    public $code;
    public $caseId;

    private $userId;

    public function __construct(int $caseId, $userId, $config = [])
    {
        $this->caseId = $caseId;
        $this->userId = $userId;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['count', 'required'],
            ['count', 'integer', 'min' => 1, 'max' => 9],

            ['code', 'required'],
            ['code', 'in', 'range' => array_keys($this->getCodeList())],

            ['caseId', 'required'],
            ['caseId', 'exist', 'targetAttribute' => 'cs_id', 'targetClass' => Cases::class],
        ];
    }

    public function getCodeList(): array
    {
        $list = self::CODE_LIST;
        $authManager = \Yii::$app->authManager;

        if ($authManager->checkAccess($this->userId, 'coupon/request-full-list')) {
            return $list;
        }

        unset($list[self::CODE_USD25]);
        unset($list[self::CODE_USD100]);

        return $list;
    }
}

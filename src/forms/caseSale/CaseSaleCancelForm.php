<?php

namespace src\forms\caseSale;

use common\models\CaseSale;
use modules\cases\src\entities\caseSale\CancelSaleReason;
use src\entities\cases\Cases;
use yii\base\Model;

/**
 * Class CaseSaleCancelForm
 *
 * @property int $caseId
 * @property int $caseSaleId
 * @property int $reasonId
 * @property string|null $message
 */
class CaseSaleCancelForm extends Model
{
    public $caseId;
    public $caseSaleId;
    public $reasonId;
    public $message = null;

    /**
     * @param int $caseId
     * @param int $caseSaleId
     * @param array $config
     */
    public function __construct(int $caseId, int $caseSaleId, array $config = [])
    {
        parent::__construct($config);
        $this->caseId = $caseId;
        $this->caseSaleId = $caseSaleId;
    }

    public function rules(): array
    {
        return [
            [['caseId', 'caseSaleId'], 'integer'],

            [['caseId'], 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['caseId' => 'cs_id']],

            [['caseSaleId'], 'exist', 'skipOnError' => true, 'targetClass' => CaseSale::class, 'targetAttribute' => ['caseSaleId' => 'css_sale_id']],

            ['reasonId', 'required'],
            ['reasonId', 'integer'],
            ['reasonId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['reasonId', 'in', 'range' => array_keys(CancelSaleReason::getList())],

            ['message', 'string', 'max' => 255],
            ['message', 'required', 'when' => function () {
                return $this->reasonId === CancelSaleReason::OTHER;
            }],
        ];
    }

    public function getReasonList(): array
    {
        return CancelSaleReason::getList();
    }

    public function attributeLabels(): array
    {
        return [
            'caseId' => 'Case Id',
            'caseSaleId' => 'Sale Id',
            'reasonId' => 'Reason',
            'message' => 'Message',
        ];
    }
}

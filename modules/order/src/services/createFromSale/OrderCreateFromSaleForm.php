<?php

namespace modules\order\src\services\createFromSale;

use common\models\Currency;
use common\models\Project;
use modules\order\src\entities\order\Order;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class OrderCreateFromSaleForm
 *
 * @property $currency
 * @property $saleId
 * @property $project
 *
 * @property $projectId
 */
class OrderCreateFromSaleForm extends Model
{
    public $currency;
    public $saleId;
    public $project;

    private ?int $projectId;

    public function rules(): array
    {
        return [
            [['saleId'], 'required'],
            [['saleId'], 'integer'],

            [['currency'], 'trim', 'skipOnEmpty' => true, 'skipOnError' => true],
            [['currency'], 'string', 'max' => 3],
            [['currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['currency' => 'cur_code']],

            [['project'], 'required'],
            [['project'], 'string'],
            [['project'], 'detectProjectId'],
        ];
    }

    public function detectProjectId($attribute)
    {
        if ($project = Project::findOne(['name' => $this->project])) {
            $this->projectId = $project->id;
        } else {
            $this->addError($attribute, 'Project not found by name (' . $this->project . ')');
        }
    }

    public function getProjectId(): ?int
    {
        return $this->projectId;
    }

    public static function fillForm(array $saleData): OrderCreateFromSaleForm /* TODO::  */
    {
        $form = new self();
        $form->project = ArrayHelper::getValue($saleData, 'project');
        $form->currency = ArrayHelper::getValue($saleData, 'price.currency');

        return $form;
    }
}

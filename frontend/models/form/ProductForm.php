<?php

namespace frontend\models\form;

use common\models\Lead;
use Yii;
use yii\base\Model;

/**
 * This is the model class for table "product".
 *
 * @property int $pr_type_id
 * @property string $pr_name
 * @property int $pr_lead_id
 * @property string $pr_description
 */
class ProductForm extends Model
{

    public $pr_type_id;
    public $pr_name;
    public $pr_lead_id;
    public $pr_description;


    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['pr_type_id', 'pr_lead_id'], 'required'],
            [['pr_type_id', 'pr_lead_id'], 'integer'],
            [['pr_description'], 'string'],
            [['pr_name'], 'string', 'max' => 40],
            [['pr_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['pr_lead_id' => 'id']],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'pr_type_id' => 'Product Type',
            'pr_name' => 'Name',
            'pr_lead_id' => 'Lead',
            'pr_description' => 'Description',
        ];
    }

}

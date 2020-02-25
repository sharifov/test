<?php

namespace modules\product\src\forms;

use yii\base\Model;

/**
 * Class ProductUpdateForm
 *
 * @property string $pr_name
 * @property string $pr_description
 */
class ProductUpdateForm extends Model
{
    public $pr_name;
    public $pr_description;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['pr_name', 'string', 'max' => 40],
            ['pr_description', 'string'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'pr_name' => 'Name',
            'pr_description' => 'Description',
        ];
    }
}

<?php

namespace modules\user\userFeedback\forms;

use yii\base\Model;

class UserFeedbackResolutionForm extends Model
{
    public $uf_resolution;
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['uf_resolution'], 'string', 'max' => 500],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'uf_resolution' => 'Resolution',
        ];
    }

    public function formName()
    {
        return '';
    }
}

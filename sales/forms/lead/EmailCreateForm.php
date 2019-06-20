<?php

namespace sales\forms\lead;

use yii\base\Model;

/**
 * Class EmailCreateForm
 * @property string $email
 * @property string $help - only for View for multiInput Widget
 */
class EmailCreateForm extends Model
{

    public $email;
    public $help;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['email', 'string', 'max' => 100],
            ['email', 'email'],
            ['email', 'filter', 'filter' => function($value) {
                return mb_strtolower(trim($value));
            }]
        ];
    }

}

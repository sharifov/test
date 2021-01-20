<?php

namespace modules\fileStorage\src\useCase\view;

use modules\fileStorage\src\services\url\QueryParams;
use yii\base\Model;

/**
 * Class ViewForm
 *
 * @property $uid
 * @property $context
 */
class ViewForm extends Model
{
    public $uid;
    public $context;

    public function rules(): array
    {
        return [
            ['uid', 'required'],
            ['uid', 'string'],

            ['context', 'required'],
            ['context', 'string'],
            ['context', 'in', 'range' => [QueryParams::CONTEXT_LEAD, QueryParams::CONTEXT_CASE]]
        ];
    }

    public function isLead(): bool
    {
        return $this->context === QueryParams::CONTEXT_LEAD;
    }

    public function isCase(): bool
    {
        return $this->context === QueryParams::CONTEXT_CASE;
    }

    public function formName(): string
    {
        return '';
    }
}

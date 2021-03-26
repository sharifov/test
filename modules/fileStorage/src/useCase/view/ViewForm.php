<?php

namespace modules\fileStorage\src\useCase\view;

use modules\fileStorage\src\services\url\QueryParams;
use yii\base\Model;

/**
 * Class ViewForm
 *
 * @property $uid
 * @property $context
 * @property bool|null $guard_enabled
 * @property bool|null $as_file
 */
class ViewForm extends Model
{
    public $uid;
    public $context;
    public $guard_enabled = true;
    public $as_file = false;

    public function rules(): array
    {
        return [
            ['uid', 'required'],
            ['uid', 'string'],

            ['guard_enabled', 'default', 'value' => true],
            ['guard_enabled', 'boolean'],

            ['context', 'required', 'when' => static function (self $model) {
                return (bool) $model->guard_enabled;
            }],
            ['context', 'string'],
            ['context', 'in', 'range' => [QueryParams::CONTEXT_LEAD, QueryParams::CONTEXT_CASE]],

            ['as_file', 'default', 'value' => false],
            ['as_file', 'boolean'],
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

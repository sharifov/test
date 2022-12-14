<?php

namespace frontend\widgets\multipleUpdate\redial;

use yii\base\Model;
use yii\helpers\VarDumper;

/**
 * Class MultipleUpdateForm
 *
 * @property $ids
 * @property $attempts
 * @property $weight
 * @property $created
 * @property $from
 * @property $to
 * @property $remove
 */
class MultipleUpdateForm extends Model
{
    public $ids;
    public $attempts;
    public $weight;
    public $created;
    public $from;
    public $to;
    public $remove;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['ids', 'required', 'message' => 'Not selected rows'],
            ['ids', 'string'],
            ['ids', 'filter', 'filter' => static function ($value) {
                return explode(',', $value);
            }],
            ['ids', 'each', 'rule' => ['integer']],
            ['ids', 'filter', 'filter' => static function ($value) {
                $new = [];
                foreach ($value as $item) {
                    $new[] = (int)$item;
                }
                return $new;
            }],

            ['attempts', 'integer', 'max' => 100],
            ['attempts', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['attempts', function () {
                if (!$this->isAttempts()) {
                    $this->attempts = null;
                }
            }, 'skipOnEmpty' => false],

            ['weight', 'integer', 'max' => 10000],
            ['weight', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['weight', function () {
                if (!$this->isWeight()) {
                    $this->weight = null;
                }
            } ,'skipOnEmpty' => false],

            ['created', 'string'],
            ['created', 'datetime', 'format' => 'php:Y-m-d H:i'],

            ['from', 'string'],
            ['from', 'datetime', 'format' => 'php:Y-m-d H:i'],
            ['from', 'validateFrom'],

            ['to', 'string'],
            ['to', 'datetime', 'format' => 'php:Y-m-d H:i'],
            ['to', 'validateTo'],

            ['remove', 'boolean'],

            ['ids', 'validateEmpty'],
        ];
    }

    public function validateFrom(): void
    {
        if (!$this->from) {
            return;
        }
        if ($this->created && strtotime($this->from) <= strtotime($this->created)) {
            $this->addError('from', 'Time from must be greater than Created time.');
        }
    }

    public function validateTo(): void
    {
        if (!$this->to) {
            return;
        }
        if ($this->from && strtotime($this->to) <= strtotime($this->from)) {
            $this->addError('to', 'Time to must be greater than Time from.');
        } elseif ($this->created && strtotime($this->to) <= strtotime($this->created)) {
            $this->addError('to', 'Time to must be greater than Created time.');
        }
    }

    public function validateEmpty(): void
    {
        if (
            !$this->from
            && !$this->to
            && !$this->created
            && !$this->remove
            && !$this->isAttempts()
            && !$this->isWeight()
        ) {
            $this->addError('ids', 'Not selected action');
        }
    }

    /**
     * @return bool
     */
    public function isAttempts(): bool
    {
        return $this->attempts !== '' && $this->attempts !== null;
    }

    /**
     * @return bool
     */
    public function isWeight(): bool
    {
        return $this->weight !== '' && $this->weight !== null;
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'attempts' => 'Call attempts',
            'created' => 'Created Time',
            'from' => 'Time from',
            'to' => 'Time to',
        ];
    }

    /**
     * @return bool
     */
    public function isRemove(): bool
    {
        return $this->remove ? true : false;
    }

    /**
     * @return bool
     */
    public function isAnyFieldForMultipleUpdate(): bool
    {
        return $this->isWeight() || $this->from || $this->to || $this->created;
    }
}

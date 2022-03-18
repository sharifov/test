<?php

namespace src\behaviors;

use src\helpers\app\AppHelper;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * Class KeySlugBehavior
 *
 * @property string $donorColumn
 * @property string $targetColumn
 * @property string $replacement
 * @property string $lowercase
 */
class KeySlugBehavior extends Behavior
{
    public $donorColumn;
    public $targetColumn;
    public string $replacement = '_';
    public bool $lowercase = true;

    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'slugGenerate',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'slugGenerate',
        ];
    }

    public function slugGenerate(): void
    {
        try {
            if (!empty($this->owner->{$this->donorColumn})) {
                $this->owner->{$this->targetColumn} = Inflector::slug(
                    $this->owner->{$this->donorColumn},
                    $this->replacement,
                    $this->lowercase
                );
            }
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), [
                'donorColumn' => $this->donorColumn,
                'donorColumnValue' => $this->owner->{$this->donorColumn} ?? null,
                'targetColumn' => $this->targetColumn,
                'targetColumnValue' => $this->owner->{$this->targetColumn} ?? null,
            ]);
            \Yii::error($message, 'KeySlugBehavior:SlugGenerate:Throwable');
        }
    }
}

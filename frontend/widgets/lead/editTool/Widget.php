<?php

namespace frontend\widgets\lead\editTool;

use common\models\Lead;

/**
 * Class Widget
 *
 * @property Lead $lead
 * @property string $modalId
 */
class Widget extends \yii\base\Widget
{
    public $lead;
    public $modalId;

    public function init(): void
    {
        parent::init();
        if (!$this->lead instanceof Lead) {
            throw new \InvalidArgumentException('lead property must be Lead');
        }
    }

    /**
     * @return string
     */
    public function run(): string
    {
        $editForm = new Form($this->lead);
        return $this->render('view', [
            'editForm' => $editForm,
            'modalId' => $this->modalId,
        ]);
    }
}

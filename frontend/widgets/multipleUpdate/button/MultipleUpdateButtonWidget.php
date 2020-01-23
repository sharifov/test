<?php

namespace frontend\widgets\multipleUpdate\button;

use yii\base\Widget;

/**
 * Class MultipleUpdateButtonWidget
 *
 * @property $modalId
 * @property $showUrl
 * @property string $gridId
 * @property string $formId
 * @property string $buttonText
 * @property string $buttonClass
 * @property string $headerText
 */
class MultipleUpdateButtonWidget extends Widget
{
    public $modalId;
    public $showUrl;
    public $gridId;

    public $formId = 'multiple-update-form';
    public $buttonText = 'Multiple update';
    public $buttonClass = 'multiple-update-btn';
    public $headerText = 'Multiple update';

    public function init(): void
    {
        parent::init();

        if ($this->modalId === null) {
            throw new \InvalidArgumentException('modalId must be set');
        }
        if ($this->showUrl === null) {
            throw new \InvalidArgumentException('showUrl must be set');
        }
        if ($this->gridId === null) {
            throw new \InvalidArgumentException('gridId must be set');
        }
    }

    public function run(): string
    {
        return $this->render('button', [
            'modalId' => $this->modalId,
            'showUrl' => $this->showUrl,
            'gridId' => $this->gridId,
            'buttonText' => $this->buttonText,
            'buttonClass' => $this->buttonClass,
            'headerText' => $this->headerText,
        ]);
    }
}

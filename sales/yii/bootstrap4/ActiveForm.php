<?php

namespace sales\yii\bootstrap4;

use sales\yii\bootstrap4\assets\ActiveFormAsset;

/**
 * Class ActiveForm
 *
 * @property bool $removeErrorsOnChangeElements
 */
class ActiveForm extends \yii\bootstrap4\ActiveForm
{
    public $removeErrorsOnChangeElements = true;

    public $fieldErrorCssClass = '.invalid-feedback';

    public function registerClientScript(): void
    {
        parent::registerClientScript();

        if (!$this->removeErrorsOnChangeElements) {
            return;
        }

        $view = $this->getView();
        ActiveFormAsset::register($view);
        $clientOptions = $this->getClientOptions();
        $errorSummary = $clientOptions['errorSummary'];
        $errorCssClass = $clientOptions['errorCssClass'];
        $view->registerJs("addRemoveErrorListenerToActiveFormField('{$this->id}', '{$errorSummary}', '{$errorCssClass}', '{$this->fieldErrorCssClass}');");
    }
}

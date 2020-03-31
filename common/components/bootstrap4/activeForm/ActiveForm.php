<?php

namespace common\components\bootstrap4\activeForm;

use common\components\bootstrap4\activeForm\assets\ActiveFormAsset;
use common\components\bootstrap4\activeForm\ClientBeforeSubmit;
use Webmozart\Assert\Assert;
use yii\base\Model;
use yii\bootstrap4\Html;

/**
 * Class ActiveForm
 *
 * @property bool $removeErrorsOnChangeElements
 * @property ClientBeforeSubmit|null $clientBeforeSubmit
 */
class ActiveForm extends \yii\bootstrap4\ActiveForm
{
    public $enableAjaxValidation = true;
    public $enableClientValidation = false;
    public $validateOnBlur = false;
    public $validateOnChange = false;

    public $removeErrorsOnChangeElements = true;

    public $fieldErrorCssClass = '.invalid-feedback';

    public $clientBeforeSubmit;

    public function registerClientScript(): void
    {
        parent::registerClientScript();
        $this->removeErrorsOnChangeElements();
        $this->clientBeforeSubmit();
    }

    private function clientBeforeSubmit(): void
    {
        if ($this->clientBeforeSubmit === null) {
            return;
        }

        Assert::isInstanceOf($this->clientBeforeSubmit, ClientBeforeSubmit::class);

        $this->getView()->registerJs($this->clientBeforeSubmit->getJs($this->id));
    }

    private function removeErrorsOnChangeElements(): void
    {
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

    public static function formatError(Model $model): array
    {
        $result = [];
        foreach ($model->getErrors() as $attribute => $errors) {
            $result[Html::getInputId($model, $attribute)] = $errors;
        }
        return $result;
    }
}

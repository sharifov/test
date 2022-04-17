<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\QuoteUrlActivity;
use frontend\models\CommunicationForm;
use kdn\yii2\JsonEditor;

/* @var $this yii\web\View */
/* @var $model QuoteUrlActivity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quote-url-activity-form">
    <div class="row">
        <div class="col-md-4">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'qua_uid')->textInput() ?>

            <?= $form->field($model, 'qua_quote_id')->textInput() ?>

            <?= $form->field($model, 'qua_communication_type')->dropDownList(CommunicationForm::TYPE_LIST) ?>

            <?= $form->field($model, 'qua_status')->dropDownList(QuoteUrlActivity::statusList()) ?>

            <?php try {
                echo $form
                    ->field($model, 'qua_ext_data')
                    ->widget(
                        JsonEditor::class,
                        [
                            'clientOptions' => [
                                'modes' => ['code', 'form', 'tree', 'view'],
                                'mode' => $model->isNewRecord ? 'code' : 'form'
                            ],
                            'expandAll' => ['tree', 'form'],
                            'value' => (is_null($model->qua_ext_data)) ? '{}' : $model->qua_ext_data
                        ]
                    );
            } catch (Exception $exception) {
                echo $form->field($model, 'qua_ext_data')->textarea(['rows' => 6, 'value' => $model->qua_ext_data]);
            } ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

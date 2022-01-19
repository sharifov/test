<?php

use modules\user\userFeedback\entity\UserFeedback;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\user\userFeedback\entity\UserFeedback */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-feedback-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
        <?= $form->field($model, 'uf_type_id')->dropDownList(UserFeedback::TYPE_LIST, ['prompt' => '---']) ?>

        <?= $form->field($model, 'uf_status_id')->dropDownList(UserFeedback::STATUS_LIST, ['prompt' => '---']) ?>

        <?= $form->field($model, 'uf_title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'uf_message')->textarea(['rows' => 6]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <div class="col-md-8">
        <div class="col-md-8">
            <?php

            try {
                echo $form->field($model, 'uf_data_json')->widget(
                    \kdn\yii2\JsonEditor::class,
                    [
                        'clientOptions' => [
                            'modes' => ['code', 'form', 'tree', 'view'], //'text',
                            'mode' => $model->isNewRecord ? 'code' : 'form'
                        ],
                        //'collapseAll' => ['view'],
                        'expandAll' => ['tree', 'form'],
                        'value' => json_encode($model->uf_data_json)
                    ]
                );
            } catch (Exception $exception) {
                echo $form->field($model, 'uf_data_json')->textarea(['rows' => 6, 'value' => json_encode($model->uf_data_json)]);
            }

            ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

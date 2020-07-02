<?php

use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\SaleSearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $caseModel \sales\entities\cases\Cases*/

?>

<div class="sale-search">

    <?php $form = ActiveForm::begin([
        'id' => 'sales_search_form',
        'action' => ['cases/view', 'gid' => $caseModel->cs_gid],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>


    <div class="row">
        <div class="col-md-6">

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'sale_id')->input('number', ['min' => 1]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'ticket_number')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'pnr')->textInput(['maxlength' => true]) ?>
                </div>

            </div>

            <div class="row">

                <div class="col-md-6">
                    <?= $form->field($model, 'acn')->textInput(['maxlength' => true]) ?>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'booking_id')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-3">

            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'email')->input('email') ?>
                </div>
            </div>
            <div class="row">
                    <div class="col-md-12">
                    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
                    </div>
<!--                        <div class="col-md-4">-->
<!--                            --><?php ////= $form->field($model, 'card')->textInput(['maxlength' => true]) ?>
<!--                        </div>-->
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <br>
            <div class="form-group text-center">
                <?= Html::submitButton('<i class="fa fa-search"></i> Search sales', ['class' => 'btn btn-primary search_sales_btn']) ?>
                <?php //= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset data', [''], ['class' => 'btn btn-warning']) ?>
                <?php //= Html::resetButton('<i class="fa fa-close"></i> Reset form', ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
    $(document).on('beforeSubmit', '#sales_search_form', function() {
        let btn = $(this).find('.search_sales_btn');        
        btn.html('<span class="spinner-border spinner-border-sm"></span> Loading')
            .prop("disabled", true);
    });
JS;
$this->registerJs($js, View::POS_READY);
?>


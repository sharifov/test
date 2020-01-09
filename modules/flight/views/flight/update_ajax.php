<?php

use modules\flight\models\forms\FlightForm;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model FlightForm */

$pjaxId = 'pjax-flight-update'
?>
<div class="flight-update-ajax">
    <div class="hotel-form">
        <script>
            pjaxOffFormSubmit('#<?=$pjaxId?>');
        </script>
        <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
        <?php
            $form = ActiveForm::begin([
                'options' => ['data-pjax' => true],
                'action' => ['/flight/flight/update-ajax', 'id' => $model->fl_id],
                'method' => 'post'
            ]);

            $arrayRange = array_combine(range(1, 9), range(1, 9));

        ?>

        <?php
            echo $form->errorSummary($model);
        ?>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'fl_product_id')->input('number', ['min' => 0]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'fl_trip_type_id')->input('number', ['min' => 0]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'fl_cabin_class')->textInput() ?>
            </div>
        </div>


        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'fl_adults')->dropDownList($arrayRange, ['prompt' => '-']) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'fl_children')->dropDownList($arrayRange, ['prompt' => '-']) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'fl_infants')->dropDownList($arrayRange, ['prompt' => '-']) ?>
            </div>
        </div>

        <div class="form-group text-center">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        <?php \yii\widgets\Pjax::end(); ?>
    </div>
</div>

<?php
$js = <<<JS

JS;
//$this->registerJs($js, View::POS_HEAD);
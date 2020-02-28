<?php

use common\models\Employee;
use sales\helpers\DateHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model sales\model\kpi\entity\KpiUserPerformance */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="kpi-user-performance-form">

    <?php Pjax::begin() ?>

    <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1]]); ?>

    <div class="row">

        <div class="col-md-3">
            <?= $form->field($model, 'up_user_id')->dropDownList(Employee::getList(), ['prompt' => '---']) ?>

            <?= $form->field($model, 'up_year')->input('number', ['step' => 1]) ?>

			<?= $form->field($model, 'up_month')->dropDownList(DateHelper::getMonthList(), ['prompt' => '--']) ?>

			<?= $form->field($model, 'up_performance')->input('number', ['step' => 1]) ?>

        </div>

    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php Pjax::end() ?>

</div>

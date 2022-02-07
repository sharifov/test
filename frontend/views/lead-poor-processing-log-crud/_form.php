<?php

use common\models\Employee;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataQuery;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\widgets\UserSelect2Widget;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog */
/* @var $form ActiveForm */
?>

<div class="lead-poor-processing-log-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'lppl_lead_id')->textInput() ?>

        <?= $form->field($model, 'lppl_lppd_id')->dropDownList(LeadPoorProcessingDataQuery::getList()) ?>

        <?= $form->field($model, 'lppl_status')->dropDownList(LeadPoorProcessingLogStatus::STATUS_LIST) ?>

        <?= $form->field($model, 'lppl_owner_id')->dropDownList(Employee::getList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'lppl_owner_id')->widget(UserSelect2Widget::class, [
            'data' => $model->lppl_owner_id ? [
                $model->lppl_owner_id => $model->owner->username
            ] : [],
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

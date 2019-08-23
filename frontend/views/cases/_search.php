<?php

use common\models\Department;
use common\models\Project;
use sales\entities\cases\CasesCategory;
use sales\entities\cases\CasesStatus;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\entities\cases\CasesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cases-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_id') ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'cs_gid') ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_project_id')->dropDownList(Project::getList(), ['prompt' => '-']) ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_dep_id')->dropDownList(Department::getList(), ['prompt' => '-']) ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_category')->dropDownList(CasesCategory::getList(), ['prompt' => '-']) ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_status')->dropDownList(CasesStatus::STATUS_LIST, ['prompt' => '-']) ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_subject') ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_user_id') ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_lead_id') ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_created_dt')->widget(
                        \dosamigos\datepicker\DatePicker::class, [
                        'inline' => false,
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-M-yyyy',
                        ]
                    ]);?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1">
            <?= $form->field($model, 'cssSaleId') ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'cssBookId') ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'salePNR') ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'clientPhone') ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'clientEmail') ?>
        </div>
    </div>

</div>

<div class="form-group text-center">
    <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
    <?= Html::resetButton('Reset form', ['class' => 'btn btn-outline-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>

</div>

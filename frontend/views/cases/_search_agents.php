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
                    <?= $form->field($model, 'cssSaleId') ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cssBookId') ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'salePNR') ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_project_id')->dropDownList(Project::getList(), ['prompt' => '-']) ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_category')->dropDownList(CasesCategory::getList(), ['prompt' => '-']) ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_status')->dropDownList(CasesStatus::STATUS_LIST, ['prompt' => '-']) ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'clientPhone') ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'clientEmail') ?>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="form-group text-center">
    <?/*= Html::submitButton('Search', ['class' => 'btn btn-primary']) */?><!--
    --><?/*= Html::resetButton('Reset form', ['class' => 'btn btn-outline-secondary']) */?>

    <?= Html::submitButton('<i class="fa fa-search"></i> Search cases', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset form', ['cases/index'], ['class' => 'btn btn-warning']) ?>
</div>

<?php ActiveForm::end(); ?>

</div>

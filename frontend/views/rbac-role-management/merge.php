<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model */
/* @var $roleList */
/* @var $form yii\widgets\ActiveForm */

\frontend\assets\QueryBuilderAsset::register($this);
$this->title                   = 'Merge to Role: "' . $model->name . '"';
$this->params['breadcrumbs'][] = ['label' => 'Rbac Role Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Merge';
?>
<div class="user-feedback-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="abac-policy-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->errorSummary($model) ?>

        <div class="row">
            <div class="col-md-5">
                <div class="row">
                    <div class="col-md-6">
                        <?= Html::label('Choose Role to merge from', 'role_donor', ['class' => 'control-label']) ?>
                        <?= $form->field($model, 'donor_name', [
                        ])->widget(Select2::class, [
                            'data'          => $roleList,
                            'size'          => Select2::SMALL,
                            'options'       => ['placeholder' => 'Select Role', 'multiple' => false],
                            'pluginOptions' => ['allowClear' => true],
                        ]) ?>
                    </div>
                    <div class="col-md-6">

                    </div>
                </div>

                <?php echo $form->field($model, 'name', [
                ])
                                ->hiddenInput()
                                ->label(false); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <?= Html::submitButton('<i class="fa fa-save"></i> Merge item', ['class' => 'btn btn-success', 'id' => 'btn-submit']) ?>
                </div>
            </div>
        </div>


        <div class="row">
        </div>


        <?php ActiveForm::end(); ?>

    </div>
</div>
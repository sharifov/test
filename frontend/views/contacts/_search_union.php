<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\ContactsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contacts-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'search_text')->label('Search') ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'is_company')->dropDownList([1 => 'Yes', 0 => 'No'], ['prompt' => '-']) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'is_public')->dropDownList([1 => 'Yes', 0 => 'No'], ['prompt' => '-']) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'disabled')->dropDownList([1 => 'Yes', 0 => 'No'], ['prompt' => '-']) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'favorite')->dropDownList([1 => 'Yes', 0 => 'No'], ['prompt' => '-']) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group text-center">
                <?= Html::submitButton('<i class="fa fa-search"></i> Search contacts', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset form', ['index'], ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

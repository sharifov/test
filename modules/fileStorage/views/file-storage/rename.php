<?php

use modules\fileStorage\src\useCase\fileStorage\update\EditForm;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form EditForm */
/* @var $this yii\web\View */
/* @var $activeForm ActiveForm */

$this->title = 'Rename File Storage: ' . $form->fs_id;
$this->params['breadcrumbs'][] = ['label' => 'File Storages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $form->fs_id, 'url' => ['view', 'id' => $form->fs_id]];
$this->params['breadcrumbs'][] = 'Rename';

?>
<div class="file-storage-rename">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="file-storage-form">

        <div class="col-md-4">

            <?php $activeForm = ActiveForm::begin(); ?>

            <?= $activeForm->field($form, 'fs_name')->textInput(['maxlength' => true]) ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>

    </div>

</div>

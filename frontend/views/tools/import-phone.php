<?php

use src\forms\file\CsvUploadForm;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var CsvUploadForm $model */
/** @var array $errors */
/** @var int $processed */

$this->title = 'Import Phone';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="import-phone">
    <h3><?= Html::encode($this->title) ?></h3>

    <?php Pjax::begin(['id' => 'pjax-import-phone', 'timeout' => 9000]); ?>
    <div class="row">

        <div class="col-md-6">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

            <?= $form->field($model, 'file')->fileInput(['multiple' => false, 'accept' => '.csv']) ?>

            <?= Html::submitButton('<i class="fa fa-upload"></i> Upload CSV file', ['class' => 'btn btn-success']) ?>

            <?php ActiveForm::end() ?>
        </div>

        <div class="col-md-6">
            <?php if ($processed) : ?>
                <div class="alert alert-success" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <p>Processed: <?php echo $processed ?></p>
                </div>
            <?php endif ?>
            <?php if ($errors) : ?>
                <div class="alert alert-danger" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <?php foreach ($errors as $value) : ?>
                        <p><?php echo $value ?></p>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
        </div>

    </div>
    <?php Pjax::end(); ?>
</div>

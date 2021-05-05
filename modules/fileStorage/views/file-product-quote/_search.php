<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileProductQuote\search\FileProductQuoteSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="file-product-quote-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'fpq_fs_id') ?>

    <?= $form->field($model, 'fpq_pq_id') ?>

    <?= $form->field($model, 'fpq_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

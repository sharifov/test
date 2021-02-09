<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\airportLang\entity\AirportLangSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="airport-lang-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ail_iata') ?>

    <?= $form->field($model, 'ail_lang') ?>

    <?= $form->field($model, 'ail_name') ?>

    <?= $form->field($model, 'ail_city') ?>

    <?= $form->field($model, 'ail_country') ?>

    <?php // echo $form->field($model, 'ail_created_user_id') ?>

    <?php // echo $form->field($model, 'ail_updated_user_id') ?>

    <?php // echo $form->field($model, 'ail_created_dt') ?>

    <?php // echo $form->field($model, 'ail_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\search\HotelListSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hotel-list-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'hl_id') ?>

    <?= $form->field($model, 'hl_code') ?>

    <?= $form->field($model, 'hl_hash_key') ?>

    <?= $form->field($model, 'hl_name') ?>

    <?= $form->field($model, 'hl_star') ?>

    <?php // echo $form->field($model, 'hl_category_name') ?>

    <?php // echo $form->field($model, 'hl_destination_code') ?>

    <?php // echo $form->field($model, 'hl_destination_name') ?>

    <?php // echo $form->field($model, 'hl_zone_name') ?>

    <?php // echo $form->field($model, 'hl_zone_code') ?>

    <?php // echo $form->field($model, 'hl_country_code') ?>

    <?php // echo $form->field($model, 'hl_state_code') ?>

    <?php // echo $form->field($model, 'hl_description') ?>

    <?php // echo $form->field($model, 'hl_address') ?>

    <?php // echo $form->field($model, 'hl_postal_code') ?>

    <?php // echo $form->field($model, 'hl_city') ?>

    <?php // echo $form->field($model, 'hl_email') ?>

    <?php // echo $form->field($model, 'hl_web') ?>

    <?php // echo $form->field($model, 'hl_phone_list') ?>

    <?php // echo $form->field($model, 'hl_image_list') ?>

    <?php // echo $form->field($model, 'hl_image_base_url') ?>

    <?php // echo $form->field($model, 'hl_board_codes') ?>

    <?php // echo $form->field($model, 'hl_segment_codes') ?>

    <?php // echo $form->field($model, 'hl_latitude') ?>

    <?php // echo $form->field($model, 'hl_longitude') ?>

    <?php // echo $form->field($model, 'hl_ranking') ?>

    <?php // echo $form->field($model, 'hl_service_type') ?>

    <?php // echo $form->field($model, 'hl_last_update') ?>

    <?php // echo $form->field($model, 'hl_created_dt') ?>

    <?php // echo $form->field($model, 'hl_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

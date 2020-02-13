<?php

use sales\access\ListsAccess;
use sales\auth\Auth;
use sales\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\offer\src\entities\offerViewLog\OfferViewLog */
/* @var $form yii\widgets\ActiveForm */

$list = new ListsAccess(Auth::id());

?>

<div class="offer-view-log-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ofvwl_offer_id')->textInput() ?>

        <?= $form->field($model, 'ofvwl_visitor_id')->textInput() ?>

        <?= $form->field($model, 'ofvwl_ip_address')->textInput() ?>

        <?= $form->field($model, 'ofvwl_user_agent')->textInput() ?>

        <?= $form->field($model, 'ofvwl_created_dt')->widget(DateTimePicker::class) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

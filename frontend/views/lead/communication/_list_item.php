<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model stdClass */
?>

<?php
    $fromType = 'client';
?>

<?php if($model->type === 'voice'): ?>
    <div class="chat__message chat__message--<?=$fromType?> chat__message--phone">
    <div class="chat__icn"><i class="fa fa-phone"></i></div>
    <i class="chat__status chat__status--sent fa fa-circle" data-toggle="tooltip" title="" data-placement="left" data-original-title="SENT"></i>
    <div class="chat__message-heading">
        <div class="chat__sender">Call from <strong>Agent Mary</strong> to <strong>+37366889955</strong></div>
        <div class="chat__date">11:01AM | June 9</div>
    </div>
    <div class="panel-body">
        <audio controls="controls" class="chat__audio">
            <source src="audio.mp3" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
    </div>
</div>
<?php endif;?>

<?php if($model->type === 'email'): ?>
    <div class="chat__message chat__message--<?=$fromType?> chat__message--email">
    <div class="chat__icn"><i class="fa fa-envelope-open"></i></div>
    <i class="chat__status chat__status--sent fa fa-circle" data-toggle="tooltip" title="" data-placement="right" data-original-title="SENT"></i>
    <div class="chat__message-heading">
        <div class="chat__sender">Email from <strong>John Trumpfort (john.trumpfort@techork.com)</strong></div>
        <div class="chat__date">11:01AM | June 9</div>
    </div>
    <div class="panel-body">
        <h5 class="chat__subtitle">Your Booking Flight to New York Received</h5>
        <div class="">Dear Mrs. / Ms. John Ronald Ruel Tolkien,
            You will receive an email with your passenger receipt in a few hours. We wish you a pleasant flight!
        </div>
        <div class="chat__message-footer">
            <a href="#" class="chat__details"><i class="fa fa-search-plus"></i>&nbsp;Details</a>
        </div>
    </div>
</div>
<?php endif;?>

<?php if($model->type === 'sms'): ?>
    <div class="chat__message chat__message--<?=$fromType?> chat__message--sms">
    <div class="chat__icn"><i class="fa fa-comments-o"></i></div>
    <i class="chat__status chat__status--success fa fa-circle" data-toggle="tooltip" title="" data-placement="left" data-original-title="DELIVERED"></i>
    <div class="chat__message-heading">
        <div class="chat__sender">SMS from <strong>Agent Mary</strong> to <strong>+37366889955</strong></div>
        <div class="chat__date">11:01AM | June 9</div>
    </div>
    <div class="panel-body">
        Dear Mrs. / Ms. John Ronald Ruel Tolkien,
        You will receive an email with your passenger receipt in a few hours. We wish you a pleasant flight!
    </div>
</div>
<?php endif;?>

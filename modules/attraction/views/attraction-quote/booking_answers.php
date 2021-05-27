<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;

/**
 * @var $model \modules\attraction\models\forms\BookingAnswersForm
 * @var $bookingDetails array
 */
$answers = !empty($bookingDetails['questionList']['nodes']) ? $bookingDetails['questionList']['nodes'] : $bookingDetails['availabilityList']['nodes'][0]['questionList']['nodes'];
?>
<div class="container">
    <div class="row">
        <div class="col-4">
            <?php
            $form = ActiveForm::begin([
                'id' => 'bk-answer-form',
                'action' => ['/attraction/attraction-quote/input-book-answers'],
                'method' => 'post'
            ]);
            ?>
            <div class="x_panel rounded">
                <div class="x_title">
                    <h2>Booking</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" style="display: block">

                    <?php $model->bookingId = $bookingDetails['id'] ?>
                    <?= $form->field($model, 'bookingId')->hiddenInput()->label(false) ?>
                    <?= $form->field($model, 'quoteId')->hiddenInput()->label(false) ?>

                    <?= $form->field($model, 'leadPassengerName') ?>
                </div>
            </div>

            <div class="x_panel rounded">
                <div class="x_title">
                    <h2>Lead Person Details</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" style="display: block">
                    <?php foreach ($answers as $answerKey => $answer) :
                         $mappedOptions = ArrayHelper::map($answer['availableOptions'], 'value', 'label');
                        ?>

                        <?php if ($answer['dataType'] === 'BOOLEAN') : ?>
                              <?= $form->field($model, 'booking_answers[' . $answerKey . '][' . $answer['id'] . ']')->checkbox()->label($answer['label']) ?>
                        <?php elseif ($answer['dataType'] === 'TEXT') : ?>
                               <?= $form->field($model, 'booking_answers[' . $answerKey . '][' . $answer['id'] . ']')->textInput()->label($answer['label']) ?>
                        <?php else : ?>
                               <?= $form->field($model, 'booking_answers[' . $answerKey . '][' . $answer['id'] . ']')->dropdownList($mappedOptions)->label($answer['label']) ?>
                        <?php endif; ?>

                    <?php endforeach; ?>
                </div>
            </div>
            <div class="form-group text-center">
                  <?= Html::submitButton('<i class="fa fa-plus"></i> Answer', ['class' => 'btn btn-success btn-book-answer']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

        <div class="col-8">
            <div class="x_panel rounded">
                <div class="x_title">
                    <h2>Summary</h2>
                        <div class="clearfix"></div>
                </div>
                <div class="x_content" style="display: block">
                    <h5><?= $bookingDetails['availabilityList']['nodes'][0]['product']['name'] ?></h5>
                    <div class="container">
                        <div class="row">
                            <div class="col-3">
                                 <img src="<?= $bookingDetails['availabilityList']['nodes'][0]['product']['previewImage']['url'] ?>" class="img-thumbnail" alt="Preview">
                            </div>
                            <div class="col-9">
                                <?= '<span class="badge badge-white">Date</span>: <b>' . $bookingDetails['availabilityList']['nodes'][0]['date'] . '</b><br>' ?>
                                <?php foreach ($bookingDetails['availabilityList']['nodes'][0]['optionList']['nodes'] as $option) : ?>
                                    <?= '<span class="badge badge-white">' . $option['label'] . '</span>: <b>' . $option['answerFormattedText'] . '</b><br>' ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
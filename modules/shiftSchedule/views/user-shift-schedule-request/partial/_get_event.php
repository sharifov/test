<?php

/**
 * @var View $this
 * @var UserShiftSchedule $event
 * @var ScheduleRequestForm $model
 * @var bool $success
 * @var string $userTimeZone
 */

use common\models\Lead;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\forms\ScheduleRequestForm;
use modules\shiftSchedule\src\helpers\UserShiftScheduleHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

$hours = round($event->uss_duration / 60) ?: 1;
$tsStartUtc = strtotime($event->uss_start_utc_dt);
$tsEndUtc = strtotime($event->uss_end_utc_dt);

?>
<?php Pjax::begin([
    'id' => 'pjax-decision-form',
    'enablePushState' => false,
    'enableReplaceState' => false,
]); ?>
    <div class="shift-schedule-event-view">

        <div class="row">

            <div class="col-md-9 text-left">
                <h5>
                    <span class="Time Line Type">
                        <?php echo $event->shiftScheduleType->getIconLabel() ?>
                        <?php echo Html::encode($event->getScheduleTypeTitle()) ?>,
                    </span>
                    <span title="User">
                        <i class="fa fa-user"></i> <?php echo Html::encode($event->user->username) ?>
                    </span>
                </h5>
            </div>
            <div class="col-md-3 text-right">
                <h6><span title="Status"><?php echo Html::encode($event->getStatusName()) ?></span></h6>
            </div>

            <table class="table table-bordered">
                <thead class="thead-dark">
                <tr class="text-center">
                    <th scope="col">Start</th>
                    <th scope="col">Duration</th>
                    <th scope="col">End</th>
                </tr>
                </thead>
                <tbody>
                <tr class="text-center">
                    <td>
                        <h6><?= Yii::$app->formatter->asDateTimeByUserTimezone(
                            $tsStartUtc,
                            $userTimeZone,
                            'php: d-M-Y'
                        )?></h6>
                        <h4><i class="fa fa-clock-o"></i> <?= Yii::$app->formatter->asDateTimeByUserTimezone(
                            $tsStartUtc,
                            $userTimeZone,
                            'php: H:i'
                        )?></h4>
                    </td>
                    <td style="width: 400px">
                        <div class="table-responsive" style="width: 400px">
                            <table class="table">
                                <thead>
                                <tr style="background: <?= Html::encode($event->shiftScheduleType->sst_color) ?>">
                                    <td style="color: #FFFFFF">
                                        &nbsp;
                                    </td>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <strong>
                            Duration Time:
                            <?= Lead::diffFormat((new DateTime($event->uss_start_utc_dt))->diff(new DateTime($event->uss_end_utc_dt))) ?>
                        </strong>
                    </td>
                    <td>
                        <h6><?= Yii::$app->formatter->asDateTimeByUserTimezone(
                            $tsEndUtc,
                            $userTimeZone,
                            'php: d-M-Y'
                        )?></h6>
                        <h4><i class="fa fa-clock-o"></i> <?= Yii::$app->formatter->asDateTimeByUserTimezone(
                            $tsEndUtc,
                            $userTimeZone,
                            'php: H:i'
                        )?></h4>
                    </td>
                </tr>
                </tbody>
            </table>

            <div class="col-md-6 text-left">
        <span title="TimeZone">
            <i class="fa fa-globe"></i> <?= Yii::$app->formatter->timeZone ?>
        </span>
            </div>

            <div class="col-md-6 text-right">
        <span title="Created / Updated">
            <i class="fa fa-calendar"></i>
            <?= Yii::$app->formatter->asDatetime(strtotime($event->uss_updated_dt ?: $event->uss_created_dt)) ?>
        </span>
            </div>

        </div>

        <hr>
        <input type="hidden" value="<?= $success ?>" id="request-status">
        <?php $form = ActiveForm::begin([
            'id' => 'decision-form',
            'options' => [
                'data-pjax' => true,
                'style' => 'position: relative',
            ],
        ]); ?>
        <div class="text-center js-loader"
             style="display: none; position: absolute;width: 100%;height: 100%;background: rgba(255, 255, 255, .8);z-index: 9999;">
            <div class="spinner-border m-5" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <h2>
            Your decision
        </h2>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'status')
                    ->dropDownList(UserShiftScheduleHelper::getAvailableRequestStatusList(), [
                        'prompt' => [
                            'text' => '---',
                            'options' => ['disabled' => true, 'selected' => true],
                        ],
                    ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'description')
                    ->textarea([
                        'rows' => 3,
                    ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                <?= Html::submitButton('Send your decision', [
                    'class' => [
                        'btn',
                        'btn-success',
                    ],
                ]) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
<?php
Pjax::end();

$js = <<<JS
    $(document).on('click', '.js-decision', function () {
        $(document).find('.js-decision').removeClass('active');
        $(document).find('.js-decision').find('input').attr('checked', false);
        
        $(this).addClass('active');
        $(this).find('input').attr('checked', true);
        $(this).find('input').prop('checked', true);
    });

    $(document).on('pjax:beforeSend', function () {
        $('.js-loader').show();
    }).on('pjax:end', function (a, b, c) {
        $('.js-loader').hide();
        if (c.container === '#pjax-decision-form') {
            $(document).trigger('RequestDecision:response', {
                requestStatus: $('#request-status').val()
            });
        }
    });
    
JS;
$this->registerJs($js);

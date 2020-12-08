<?php

use sales\services\cleaner\form\DbCleanerParamsForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\log\Logger;
use dosamigos\datepicker\DatePicker;
use sales\widgets\DateTimePicker;
use yii\helpers\Url;

/* @var yii\web\View $this */
/* @var DbCleanerParamsForm $modelCleaner */
/* @var ActiveForm $form */
/* @var string $pjaxIdForReload */

?>

<?php $form = ActiveForm::begin([
    'id' => 'clean_form',
    'action' => ['clean-table'],
    'method' => 'post',
]);
?>

<?php echo $form->errorSummary($modelCleaner) ?>

<?php echo $form->field($modelCleaner, 'table')->hiddenInput()->label(false) ?>
<?php echo $form->field($modelCleaner, 'column')->hiddenInput()->label(false) ?>

    <div class="x_panel">
        <div class="x_title">
            <h2 style="overflow: visible;">
            <i class="fa fa-trash"></i> Clean
                <sup>
                    <?php echo Html::a(
                        Html::tag('i', '', ['class' => 'fa fa-info-circle', 'style' => 'color: #53a265;']),
                        null,
                        ['id' => 'js-info_clean_btn']
                    ) ?>
                </sup>
            </h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: none;">
            <div class="row">
                <div class="col-md-1">
                    <?= $form->field($modelCleaner, 'level')->dropDownList([
                        Logger::LEVEL_ERROR => Logger::getLevelName(Logger::LEVEL_ERROR),
                        Logger::LEVEL_WARNING => Logger::getLevelName(Logger::LEVEL_WARNING),
                        Logger::LEVEL_INFO => Logger::getLevelName(Logger::LEVEL_INFO),
                        Logger::LEVEL_TRACE => Logger::getLevelName(Logger::LEVEL_TRACE),
                        Logger::LEVEL_PROFILE_BEGIN => Logger::getLevelName(Logger::LEVEL_PROFILE_BEGIN),
                        Logger::LEVEL_PROFILE_END => Logger::getLevelName(Logger::LEVEL_PROFILE_END),
                    ], ['prompt' => '-']) ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($modelCleaner, 'category')->dropDownList(\frontend\models\Log::getCategoryFilter(), ['prompt' => '-']) ?>
                </div>

                    <div class="col-md-2">
                        <?php echo $form->field($modelCleaner, 'strict_date')->widget(
                            DatePicker::class,
                            [
                                'clientOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd',
                                    'clearBtn' => true,
                                ],
                                'options' => [
                                    'autocomplete' => 'off',
                                    'readonly' => '1',
                                ],
                                'clientEvents' => [
                                    'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                                ],
                            ]
                        ) ?>
                    </div>
                    <div class="col-md-2">
                        <?php echo $form->field($modelCleaner, 'datetime')->widget(DateTimePicker::class) ?>
                    </div>
                    <div class="col-md-2">
                        <?php echo $form->field($modelCleaner, 'date')->widget(
                            DatePicker::class,
                            [
                                'clientOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd',
                                    'clearBtn' => true,
                                ],
                                'options' => [
                                    'autocomplete' => 'off',
                                    'readonly' => '1',
                                ],
                                'clientEvents' => [
                                    'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                                ],
                            ]
                        ) ?>
                    </div>

                    <div class="col-md-1">
                        <?php echo $form->field($modelCleaner, 'hour')->input('number', ['min' => 1, 'step' => 1]) ?>
                    </div>
                    <div class="col-md-1">
                        <?php echo $form->field($modelCleaner, 'day')->input('number', ['min' => 1, 'step' => 1]) ?>
                    </div>
                    <div class="col-md-1">
                        <?php echo $form->field($modelCleaner, 'month')->input('number', ['min' => 1, 'step' => 1]) ?>
                    </div>
                    <div class="col-md-1">
                        <?php echo $form->field($modelCleaner, 'year')->input('number', ['min' => 1, 'step' => 1]) ?>
                    </div>
                </div>

            <div class="row">
                <div class="col-md-12">
                    <br>
                    <div class="form-group">
                    <?php echo Html::submitButton('Clean logs', [
                            'id' => 'js_btn_clean_records',
                            'class' => 'btn btn-danger',
                        ]) ?>
                    </div>
                </div>
            </div>
         </div>
    </div>

<?php ActiveForm::end(); ?>

<?php echo $this->render('../clean/_clean_table_js', [
    'pjaxIdForReload' => $pjaxIdForReload,
    'cleanTableUrl' => Url::to(['log/clean-table']),
]); ?>


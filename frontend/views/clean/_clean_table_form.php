<?php

use yii\helpers\Url;
use sales\services\cleaner\form\DbCleanerParamsForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use sales\widgets\DateTimePicker;

/* @var yii\web\View $this */
/* @var DbCleanerParamsForm $modelCleaner */
/* @var yii\widgets\ActiveForm $form */
/* @var string $pjaxIdForReload */

$cleanTableUrl = Url::to(['clean/clean-table-ajax']);
?>

<div class="clean-table-box">
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

                <?php $form = ActiveForm::begin([
                    'id' => 'clean_form',
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    'options' => ['data-pjax' => 0],
                ]); ?>

                <?php echo $form->errorSummary($modelCleaner) ?>

                <?php echo $form->field($modelCleaner, 'table')->hiddenInput()->label(false) ?>
                <?php echo $form->field($modelCleaner, 'column')->hiddenInput()->label(false) ?>

                <div class="row">
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

                <div class="form-group">
                    <?php echo Html::submitButton('Clean records', [
                        'id' => 'js_btn_clean_records',
                        'class' => 'btn btn-danger',
                    ]) ?>
                </div>

                <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php echo $this->render('_clean_table_js', [
    'pjaxIdForReload' => $pjaxIdForReload,
]); ?>


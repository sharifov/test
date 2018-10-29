<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider ArrayDataProvider
 * @var $model SoldReportForm
 * @var $form ActiveForm
 * @var $isSupervision boolean
 * @var $employees array
 */

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use yii\data\ArrayDataProvider;
use frontend\models\SoldReportForm;
use dosamigos\datepicker\DatePicker;

$formId = sprintf('%s-Id', $model->formName());
$totalCountId = sprintf('#%s', Html::getInputId($model, 'totalCount'));
$dateToId = Html::getInputId($model, 'dateTo');
$dateFromId = Html::getInputId($model, 'dateFrom');

$js = <<<JS
    function validateDateRange() {
        if ($('#$dateToId').val() != '' && $('#$dateFromId').val() != '') {
            var dateTo = new Date($('#$dateToId').val()),
                dateFrom = new Date($('#$dateFromId').val());
            if (dateTo >= dateFrom) {
                var timeDiff = Math.abs(dateTo.getTime() - dateFrom.getTime());
                var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
                if (diffDays < 31) {
                    return true;
                }
            }
        }
        return false;
    }

    $('.btn-primary').click(function() {
        var form = $('#$formId');
        if (!validateDateRange()) {
            return false;
        }
        $('#preloader').removeClass('hidden');
        $.ajax({
            url: form.action,
            type: 'post',
            data: form.serialize(),
            success: function (data) {
                var tabResult = $('#table-expert-grid-id');
                tabResult.html(data.grid);
                $('$totalCountId').val(data.totalCount);
                if (data.totalCount != 0) {
                    $('#submit-btn').removeClass('hidden');
                } else {
                    $('#submit-btn').addClass('hidden');
                }
                $('#preloader').addClass('hidden');
            },
            error: function (error) {
                console.log('Error: ' + error);
            }
        });
    });
JS;

$this->registerJs($js);

?>

<div class="panel panel-main">
    <div class="panel-body">
        <div class="panel panel-default">
            <?php
            $form = ActiveForm::begin([
                'id' => $formId,
                'successCssClass' => '',
            ])
            ?>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <th><?= Html::activeLabel($model, 'dateFrom') ?></th>
                                    <td class="td-input">
                                        <?= $form->field($model, 'dateFrom', [
                                            'options' => [
                                                'tag' => false
                                            ],
                                            'template' => '{input}'
                                        ])->widget(
                                            DatePicker::class, [
                                            'options' => [
                                                'data-opened' => 'false'
                                            ],
                                            'inline' => true,
                                            'template' => '{input}',
                                            'clientOptions' => [
                                                'autoclose' => true,
                                                'format' => 'dd-M-yyyy',
                                                'todayHighlight' => true
                                            ]
                                        ]) ?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <th><?= Html::activeLabel($model, 'dateTo') ?></th>
                                    <td class="td-input">
                                        <?= $form->field($model, 'dateTo', [
                                            'options' => [
                                                'tag' => false
                                            ],
                                            'template' => '{input}'
                                        ])->widget(
                                            DatePicker::class, [
                                            'options' => [
                                                'data-opened' => 'false'
                                            ],
                                            'inline' => true,
                                            'template' => '{input}',
                                            'clientOptions' => [
                                                'autoclose' => true,
                                                'format' => 'dd-M-yyyy',
                                                'todayHighlight' => true
                                            ]
                                        ]) ?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if ($isSupervision) : ?>
                        <div class="col-sm-4">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        <th><?= Html::activeLabel($model, 'employee') ?></th>
                                        <td class="td-input">
                                            <?= $form->field($model, 'employee', [
                                                'options' => [
                                                    'tag' => false,
                                                ],
                                                'template' => '{input}'
                                            ])->dropDownList($employees, [
                                                'prompt' => 'Select agent'
                                            ]) ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="panel-footer">
                <div class="actions-btn-group">
                    <?php
                    $searchButton = '<span class="btn-icon"><i class="fa fa-search"></i></span><span>Report</span>';
                    ?>
                    <?= Html::button($searchButton, [
                        'class' => 'btn btn-with-icon btn-primary'

                    ]) ?>
                </div>
            </div>
            <?php ActiveForm::end() ?>
        </div>
        <div id="table-expert-grid-id" class="table-responsive">
            <?= $this->render('_grid', [
                'dataProvider' => $dataProvider,
                'model' => $model
            ]) ?>
        </div>
    </div>
</div>


<div class="modal modal-events fade" id="modal-report-info" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Sold Leads
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>
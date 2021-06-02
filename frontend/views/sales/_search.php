<?php

use sales\model\user\entity\sales\SalesSearch;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var SalesSearch $model */
/* @var yii\widgets\ActiveForm $form */
?>

<div class="user-stats-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
        'id' => 'salesForm',
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <?php
                $model->dateRange = $model->dateRange ?? $model->defaultDateRange;

                echo $form->field($model, 'dateRange', [
                    'options' => ['class' => 'form-group']
                ])->widget(\kartik\daterange\DateRangePicker::class, [
                    'options' => ['id' => 'sold-picker'],
                    'autoUpdateOnInit' => true,
                    'presetDropdown' => true,
                    'hideInput' => true,
                    'useWithAddon' => true,
                    'convertFormat' => true,
                    'startAttribute' => 'dateFrom',
                    'endAttribute' => 'dateTo',
                    'pluginOptions' => [
                        'minDate' => $model->minDate,
                        'maxDate' => $model->maxDate,
                        'timePicker' => false,
                        'locale' => [
                            'format' => 'Y-m-d',
                            'separator' => ' - ',
                        ],
                        'ranges' => [
                            'Current Month' => ["moment().startOf('month')", "moment().endOf('month')"],
                            'Previous Month' => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
                        ],
                    ],
                ])->label('Sold Date');
                ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?php echo Html::submitButton('Search', ['class' => 'btn btn-primary js-user-stats-btn']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$js = <<<JS
    $(document).on('beforeSubmit', '#salesForm', function(event) {
        let btn = $(this).find('.js-user-stats-btn');
        
        btn.html('<span class="spinner-border spinner-border-sm"></span> Loading');
        btn.prop("disabled", true);
        
        setTimeout(function () {
            btn.html('<span class="spinner-border spinner-border-sm"></span> Prepare data');
        }, 5000);
        
        setTimeout(function () {
            btn.html('<span class="spinner-border spinner-border-sm"></span> Data processed');
        }, 10000);
        
        setTimeout(function () {
            btn.html('<span class="spinner-border spinner-border-sm"></span> Please, wait');
        }, 15000);
        
        setTimeout(function () {
            btn.html('<span class="spinner-border spinner-border-sm"></span> Loading');
        }, 20000);
    });
JS;
$this->registerJs($js, View::POS_READY);

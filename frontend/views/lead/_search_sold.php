<?php

use yii\helpers\Html;
use \kartik\form\ActiveForm;


/* @var $this yii\web\View */
/* @var $model common\models\search\CallSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<p>

</p>
<div class="lead-search">
    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-search"></i> Search</h2>

            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>

            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: <?=(Yii::$app->request->isPjax || Yii::$app->request->get('CallSearch') || Yii::$app->request->get('createTimeRange')) ? 'block' : 'none'?>">
            <?php $form = ActiveForm::begin([
                'action' => ['sold'],
                'method' => 'get',
                'options' => [
                    'data-pjax' => 1,
                ],
            ]); ?>

            <div class="row">
                <div class="col-md-3">

                    <?php

                    echo $form->field($model, 'createTimeRange', [
                        //'addon'=>['prepend'=>['content'=>'<i class="fa fa-calendar"></i>']],
                        'options' => ['class' => 'form-group']
                    ])->widget(\kartik\daterange\DateRangePicker::class, [
                        'useWithAddon' => true,
                        'presetDropdown' => true,
                        'hideInput' => true,
                        'convertFormat' => true,
                        'startAttribute' => 'sold_date_from',
                        'endAttribute' => 'sold_date_to',
                        'pluginOptions' => [
                            'timePicker' => true,
                            'timePickerIncrement' => 1,
                            'timePicker24Hour' => true,
                            'locale' => [
                                'format' => 'Y-m-d H:i',
                                'separator' => ' - '
                            ]
                        ]
                    ])->label('Created DateTime Range');
                    ?>
                </div>

            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group text-center">
                        <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-primary']) ?>
                        <?= Html::resetButton('<i class="fa fa-close"></i> Reset form', ['class' => 'btn btn-default']) ?>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>

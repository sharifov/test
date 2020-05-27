<?php

use yii\helpers\Html;
use \kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\CallSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-search">
    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-search"></i> Search</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>

                <?php /*<li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-comment"></i></a>


                <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Settings 1</a>
                    </li>
                    <li><a href="#">Settings 2</a>
                    </li>
                </ul>
            </li>*/?>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: ">
            <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => [
                    'data-pjax' => 1
                ],
            ]); ?>


            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'createTimeRange', [
                        //'addon'=>['prepend'=>['content'=>'<i class="fa fa-calendar"></i>']],
                        'options' => ['class' => 'form-group']
                    ])->widget(\kartik\daterange\DateRangePicker::class, [
                        'useWithAddon' => true,
                        'presetDropdown' => true,
                        'hideInput' => true,
                        'convertFormat' => true,
                        'startAttribute' => 'createTimeStart',
                        'endAttribute' => 'createTimeEnd',
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
                <!--<div class="col-md-3">
                    <div class="col-md-6">
                        <?php /*echo $form->field($model, 'call_duration_from')->input('number', ['min' => 0]) */?>
                    </div>
                    <div class="col-md-6">
                        <?php /*echo $form->field($model, 'call_duration_to')->input('number', ['min' => 0]) */?>
                    </div>
                </div>

                <div class="col-md-2">
                    <?php /*echo $form->field($model, 'projectId')->dropDownList(\common\models\Project::getList(), ['prompt' => '-'])->label('Project ID') */?>
                </div>
                <div class="col-md-2">
                    <?php /*echo $form->field($model, 'statusId')->dropDownList(\common\models\Call::STATUS_LIST, ['prompt' => '-'])->label('Status ID') */?>
                </div>
                <div class="col-md-2">
                    <?php /*echo $form->field($model, 'callTypeId')->dropDownList(\common\models\Call::CALL_TYPE_LIST, ['prompt' => '-'])->label('Call Type ID') */?>
                </div> -->
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

<?php

use frontend\extensions\DateRangePicker;
use frontend\models\Log;
use frontend\models\search\LogSearch;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var LogSearch $searchModel */

?>

<div class="log-search">
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
        <div class="x_content" style="display: <?= Yii::$app->request->isPjax ? 'block' : 'none' ?>">
            <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => [
                    'data-pjax' => 1,
                ],
            ]); ?>

            <div class="row">
                <div class="col-md-3">
                    <?php try {
                        echo $form->field($searchModel, 'excludedCategories')
                            ->widget(Select2::class, [
                                'data' => $searchModel->getCategoriesFilter(),
                                'size' => Select2::SMALL,
                                'options' => ['placeholder' => '-'],
                                'pluginOptions' => ['allowClear' => true, 'multiple' => true],
                            ])->label('Excluded categories');
                    } catch (Exception $e) {
                        echo 'Widget cannot be displayed: ' . $e->getMessage();
                    } ?>
                </div>
                <?php /** @fflag FFlag::FF_KEY_SYSTEM_LOG_SEARCH_BLOCK_IMPROVEMENTS_ENABLE, Enable improvements in system log search block */ ?>
                <?php if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_SYSTEM_LOG_SEARCH_BLOCK_IMPROVEMENTS_ENABLE) === true) : ?>
                    <div class="col-md-3">
                        <?php echo $form->field($searchModel, 'createdDateTimeRange', [
                            'options' => ['class' => 'form-group']
                        ])->widget(\kartik\daterange\DateRangePicker::class, [
                            'useWithAddon' => true,
                            'presetDropdown' => true,
                            'hideInput' => true,
                            'convertFormat' => true,
                            'startAttribute' => 'createdDateTimeStart',
                            'endAttribute' => 'createdDateTimeEnd',
                            'pluginOptions' => [
                                'timePicker' => true,
                                'timePickerIncrement' => 1,
                                'timePicker24Hour' => true,
                                'locale' => [
                                    'format' => 'Y-m-d H:i',
                                    'separator' => ' - '
                                ],
                                'ranges' => \Yii::$app->params['dateRangePicker']['configs']['default']
                            ],
                        ])->label('Created DateTime Range');
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group text-center">
                        <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset form', ['/log/index'], ['class' => 'btn btn-default']) ?>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
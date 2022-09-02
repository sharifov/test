<?php

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
        <div class="x_content" style="display: <?= Yii::$app->request->get('LogSearch') ? 'block' : 'none' ?>">
            <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
            ]); ?>

            <div class="row">
                <div class="col-md-12 col-sm-12 profile_details">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-md-3">
                                <?php try {
                                    echo $form->field($searchModel, 'excludedCategories')
                                        ->widget(Select2::class, [
                                            'data' => Log::getCategoryFilter(),
                                            'size' => Select2::SMALL,
                                            'options' => ['placeholder' => '-'],
                                            'pluginOptions' => ['allowClear' => true, 'multiple' => true],
                                        ])->label('Excluded fields');
                                } catch (Exception $e) {
                                    echo 'Widget cannot be displayed: ' . $e->getMessage();
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
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
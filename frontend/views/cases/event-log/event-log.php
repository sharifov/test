<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use sales\entities\cases\CaseEventLog;
use modules\cases\src\abac\CasesAbacObject;
use modules\cases\src\abac\dto\CasesAbacDto;

/**
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $searchModel \sales\entities\cases\CaseEventLogSearch
 */
?>

<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-list"></i> Event Log List (<?= $dataProvider->totalCount ?>)</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>
                <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: none; margin-top: -10px;">
        <?php Pjax::begin([
            'id' => 'case-event-grid-pjax',
            'enablePushState' => false,
            'clientOptions' => ['method' => 'GET']
        ]) ?>
        <?php echo GridView::widget([
            'id' => 'case-event-grid',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            //'filterUrl' => ['/cases/ajax-case-event-log'],
            'columns' => [
                'cel_id',
                /*[
                    'attribute' => 'cel_case_id',
                    'filter' => false,
                ],*/
                'cel_description',
                [
                    'attribute' => 'cel_created_dt',
                    'class' => \common\components\grid\DateTimeColumn::class,
                ],
                ['class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'buttons' => [
                        'view' => static function ($url, CaseEventLog $model) {
                            return '<span data-toggle="tooltip" data-placement="top" title="' . Html::encode(VarDumper::dumpAsString($model->cel_data_json)) . '"><i class="fas fa-info-circle"></i></span>';
                        },
                    ],
                    'visibleButtons' => [
                        'view' => function (CaseEventLog $model, $key, $index) {
                            /** @abac new CasesAbacDto($model), CasesAbacObject::UI_BTN_EVENT_LOG_VIEW, CasesAbacObject::ACTION_READ, show Data view button tooltip */
                            return Yii::$app->abac->can(
                                new CasesAbacDto($model->celCase),
                                CasesAbacObject::UI_BTN_EVENT_LOG_VIEW,
                                CasesAbacObject::ACTION_READ
                            );
                        },

                    ],
                ]
            ],
        ]) ?>
        <?php Pjax::end() ?>
    </div>
</div>





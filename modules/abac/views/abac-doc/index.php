<?php

use common\components\grid\DateTimeColumn;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use kartik\select2\Select2;

/* @var yii\web\View $this */
/* @var modules\abac\src\entities\abacDoc\AbacDocSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */
/* @var array $files */
/* @var array $objects */
/* @var array $actions */

$this->title = 'Abac Docs';
$this->params['breadcrumbs'][] = $this->title;
$get = Yii::$app->request->get();
$formName = $searchModel->formName();
?>

<div class="abac-doc-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-refresh"></i> ReScan', ['scan'], ['class' => 'btn btn-warning']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-abac-doc', 'scrollTo' => 0]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            ['class' => SerialColumn::class],
            [
                'attribute' => 'ad_file',
                'format' => 'raw',
                'filter' => Select2::widget([
                    'name' => $searchModel->formName() . '[ad_file]',
                    'data' =>  $files,
                    'size' => Select2::SIZE_SMALL,
                    'value' => ArrayHelper::getValue($get, $formName . '.ad_file'),
                    'options' => [
                        'placeholder' => '',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ]
                ]),
            ],
            'ad_line',
            'ad_subject',
            [
                'attribute' => 'ad_object',
                'format' => 'raw',
                'filter' => Select2::widget([
                    'name' => $searchModel->formName() . '[ad_object]',
                    'data' =>  $objects,
                    'size' => Select2::SIZE_SMALL,
                    'value' => ArrayHelper::getValue($get, $formName . '.ad_object'),
                    'options' => [
                        'placeholder' => '',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ]
                ]),
            ],
            [
                'attribute' => 'ad_action',
                'format' => 'raw',
                'filter' => Select2::widget([
                    'name' => $searchModel->formName() . '[ad_action]',
                    'data' =>  $actions,
                    'size' => Select2::SIZE_SMALL,
                    'value' => ArrayHelper::getValue($get, $formName . '.ad_action'),
                    'options' => [
                        'placeholder' => '',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ]
                ]),
            ],
            'ad_description',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ad_created_dt'
            ],

            ['class' => ActionColumn::class, 'template' => '{view}'], /* {update} {delete} */
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>

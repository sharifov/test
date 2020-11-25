<?php

use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = 'Client Chat Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="chat-report">
    <h1><i class=""></i> <?= Html::encode($this->title) ?></h1>

    <div class="">
        <div class="x_panel">
            <div class="x_title">
                <h2><i class="fa fa-search"></i> Search</h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li>
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" style="display: block">
                <?= $this->render('partial/_report_search', ['model' => $searchModel]);  ?>
            </div>
        </div>
    </div>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive' => true,
        'hover' => true,
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Chats Data</h3>',
        ],
        //'export' => false,
        //'toggleData' => false,
        'columns' => [
            'username:userName',
            [
                'attribute' => 'generated',
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],
            [
                'attribute' => 'closed',
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],
            [
                'label' => 'Messages',
                'attribute' => 'msg',
                'value' => function ($model) {
                    return $model['msg'] ? $model['msg'] : '-';
                },
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ]
        ]
    ]);
?>
</div>
<?php

use kdn\yii2\JsonEditor;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ApiLog */

$this->title = 'Request-Response ' . $model->al_id;
$this->params['breadcrumbs'][] = ['label' => 'Api Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->al_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->al_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="row">
    <div class="col-md-4">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'al_id',
            'al_action',
            //'al_request_data:ntext',
            'al_request_dt',
            //'al_response_data:ntext',
            'al_response_dt',
            'al_ip_address',

        ],
    ]) ?>
    </div>

    <div class="col-md-4">

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [

                [
                    'attribute' => 'al_execution_time',
                    //'format' => 'html',
                    'value' => function (\common\models\ApiLog $model) {
                        return $model->al_execution_time;
                    },
                ],
                [
                    'attribute' => 'al_memory_usage',
                    'format' => 'raw',
                    'value' => function (\common\models\ApiLog $model) {
                        return Yii::$app->formatter->asShortSize($model->al_memory_usage, 2);
                    },
                ],

                [
                    'attribute' => 'al_db_execution_time',
                    'value' => function (\common\models\ApiLog $model) {
                        return $model->al_db_execution_time;
                    },
                ],

                [
                    'attribute' => 'al_db_query_count',
                    'value' => function (\common\models\ApiLog $model) {
                        return $model->al_db_query_count;
                    },
                ],
            ],
        ]) ?>
    </div>
    </div>

    <div class="row">
    <div class="col-md-6">
    <h2>Request (<?=$model->al_request_dt?>):</h2>

           <?php
            try {
                echo JsonEditor::widget(
                    [
                       // JSON editor options
                       'clientOptions' => [
                           'modes' => ['code', 'preview', 'view'], // all available modes
                           'mode' => 'view', // default mode
                       ],
                       'collapseAll' => ['view'], // collapse all fields in "view" mode
                       //'containerOptions' => ['class' => 'container'], // HTML options for JSON editor container tag
                       'expandAll' => ['view', 'form'], // expand all fields in "tree" and "form" modes
                       'name' => 'editor', // hidden input name
                       //'options' => ['id' => 'data'], // HTML options for hidden input
                       'value' => $model->al_request_data, // JSON which should be shown in editor
                    ]
                );
            } catch (Throwable $throwable) {
                echo 'Error: ' . $throwable->getMessage();
                echo '<pre><small>' . \yii\helpers\VarDumper::dumpAsString(@json_decode($model->al_request_data, true), 10, true) . '</small></pre>';
            }
            ?>

        <h2>Request JSON</h2>
        <pre><small><?= Html::encode($model->al_request_data) ?></small></pre>

    </div>


    <div class="col-md-6">
    <h2>Response (<?=$model->al_response_dt?>):</h2>


        <?php
        try {
            echo JsonEditor::widget(
                [
                    // JSON editor options
                    'clientOptions' => [
                        'modes' => ['code', 'preview', 'view'], // all available modes
                        'mode' => 'view', // default mode
                    ],
                    'collapseAll' => ['view'], // collapse all fields in "view" mode
                    //'containerOptions' => ['class' => 'container'], // HTML options for JSON editor container tag
                    'expandAll' => ['view', 'form'], // expand all fields in "tree" and "form" modes
                    'name' => 'editor', // hidden input name
                    //'options' => ['id' => 'data'], // HTML options for hidden input
                    'value' => $model->al_response_data, // JSON which should be shown in editor
                ]
            );
        } catch (Throwable $throwable) {
            echo 'Error: ' . $throwable->getMessage();
            echo '<pre><small>' . \yii\helpers\VarDumper::dumpAsString(@json_decode($model->al_response_data, true), 10, true) . '</small></pre>';
        }
        ?>

        <h2>Response JSON</h2>
        <pre><small><?= Html::encode($model->al_response_data) ?></small></pre>
    </div>
    </div>

</div>

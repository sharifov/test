<?php

use common\components\grid\project\ProjectColumn;
use common\models\ClientProject;
use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ClientProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Projects';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-project-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Project', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'cp_client_id:client',
            [
                'class' => ProjectColumn::class,
                'attribute' => 'cp_project_id',
                'relation' => 'cpProject',
            ],
            [
                'attribute' => 'cp_created_dt',
                'value' => function(ClientProject $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->cp_created_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'cp_created_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

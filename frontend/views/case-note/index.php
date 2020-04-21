<?php

use common\models\CaseNote;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CaseNoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Case Notes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="case-note-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Case Note', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'cn_id',
            'cn_cs_id',
            //'cn_user_id',

            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'cn_user_id',
                'relation' => 'cnUser',
                'placeholder' => 'Select User',
            ],

            'cn_text:ntext',
            [
                'attribute' => 'cn_created_dt',
                'value' => function(\common\models\CaseNote $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->cn_created_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'cn_created_dt',
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
            //'cn_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

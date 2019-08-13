<?php

use yii\helpers\Html;
use yii\grid\GridView;
use sales\entities\cases\Cases;
use \sales\entities\cases\CasesStatusHelper;

/* @var $this yii\web\View */
/* @var $searchModel sales\entities\cases\CasesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cases';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cases-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php //= Html::a('Create Cases', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => yii\grid\SerialColumn::class],

            'cs_id',
            'cs_subject',
            'cs_description:ntext',
            'cs_category',
            [
                'attribute' => 'cs_status',
                'value' => function (Cases $model) {
                    $value = CasesStatusHelper::getName($model->cs_status);
                    $str = '<span class="label ' . CasesStatusHelper::getClass($model->cs_status) . '">' . $value . '</span>';
                    if ($model->lastLogRecord) {
                        $str .= '<br><br><span class="label label-default">' . Yii::$app->formatter->asDatetime(strtotime($model->lastLogRecord->csl_start_dt)) . '</span>';
                        $str .= '<br>';
                        $str .= $model->lastLogRecord ? Yii::$app->formatter->asRelativeTime(strtotime($model->lastLogRecord->csl_start_dt)) : '';
                    }
                    return $str;
                },
                'format' => 'raw',
                'filter' => CasesStatusHelper::STATUS_LIST,
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],
            //'cs_user_id',
            //'cs_lead_id',
            //'cs_call_id',
            //'cs_depart_id',
            //'cs_created_dt',

            ['class' => yii\grid\ActionColumn::class],
        ],
    ]); ?>


</div>

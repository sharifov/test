<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CallUserAccessSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Call User Accesses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-user-access-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Call User Access', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'cua_call_id',
            //'cua_user_id',
            [
                'attribute' => 'cua_user_id',
                'value' => function (\common\models\CallUserAccess $model) {
                    return $model->cuaUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->cuaUser->username) : '-';
                },
                'format' => 'raw',
                'filter' => \common\models\Employee::getList()
            ],
            //'cua_status_id',
            [
                'attribute' => 'cua_status_id',
                'value' => function (\common\models\CallUserAccess $model) {
                    return Html::encode($model->getStatusTypeName());
                },
                'format' => 'raw',
                'filter' => \common\models\CallUserAccess::getStatusTypeList()
            ],
            [
                'attribute' => 'cua_created_dt',
                'value' => function (\common\models\CallUserAccess $model) {
                    return $model->cua_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->cua_created_dt)) : '-';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'cua_updated_dt',
                'value' => function (\common\models\CallUserAccess $model) {
                    return $model->cua_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->cua_updated_dt)) : '-';
                },
                'format' => 'raw'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

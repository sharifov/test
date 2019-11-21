<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ProfitBonusSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $user_id int */

$this->title = 'Profit Bonuses';
$this->params['breadcrumbs'][] = $this->title;

if (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
}
?>
<div class="profit-bonus-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Profit Bonus', ['create','user_id' => $user_id], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'header' => 'Agent',
                'attribute' => 'pb_user_id',
                'value' => static function (\common\models\ProfitBonus $model) {
                    return $model->pbUser ? '<i class="fa fa-user"></i> ' . $model->pbUser->username : '-';
                },
                'format' => 'raw',
                'filter' => (empty($user_id))?$userList:false,
            ],
            'pb_min_profit',
            'pb_bonus',
            'pb_updated_dt',


            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'contentOptions'=>['style'=>'width: 60px;'],
                'buttons' => [
                    'update' => function ($url, $model, $key) use ($user_id) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to([
                            'profit-bonus/update',
                            'id' => $model['pb_id'],
                            'user_id' => $user_id,
                        ]), [
                            'data-pjax' => 0,
                            'title' => 'Update'
                        ]);
                    },
                    'delete' => function ($url, $model, $key) use ($user_id) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $model->pb_id, 'user_id' => $user_id], [
                        'class' => '',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]);
                    }
                ]
            ]
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

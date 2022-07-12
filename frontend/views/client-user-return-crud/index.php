<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use src\model\clientUserReturn\entity\ClientUserReturn;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\clientUserReturn\entity\ClientUserReturnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client User Returns';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-user-return-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client User Return', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'cur_client_id',
                'value' => static function (ClientUserReturn $model) {
                    return Html::a('<i class="fa fa-link"></i> ' . $model->cur_client_id, ['/client/view', 'id' => $model->cur_client_id]);
                },
                'format' => 'raw'
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'cur_user_id',
                'relation' => 'user',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cur_created_dt',
            ],
            [
                'class' => ActionColumn::class,
                'urlCreator' => static function ($action, ClientUserReturn $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'cur_client_id' => $model->cur_client_id, 'cur_user_id' => $model->cur_user_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php

use common\components\grid\DateTimeColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChatUnread\entity\search\ClientChatUnreadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Unreads';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-unread-index">

    <h1><?= Html::encode($this->title); ?></h1>

    <p>
        <?= Html::a('Create Client Chat Unread', ['create'], ['class' => 'btn btn-success']); ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'ccu_cc_id',
            'ccu_count',
            ['class' => DateTimeColumn::class, 'attribute' => 'ccu_created_dt'],
            ['class' => DateTimeColumn::class, 'attribute' => 'ccu_updated_dt'],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

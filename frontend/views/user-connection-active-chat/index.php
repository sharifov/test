<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\user\entity\userConnectionActiveChat\search\UserConnectionActiveChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Connection Active Chats';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-connection-active-chat-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Connection Active Chat', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'ucac_conn_id',
            'ucac_chat_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use sales\model\clientChat\entity\channelTranslate\ClientChatChannelTranslate;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChat\entity\channelTranslate\search\ClientChatChannelTranslateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Channel Translates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-channel-translate-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Channel Translate', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'ct_channel_id',
                'value' => static function (ClientChatChannelTranslate $model) {
                    return $model->ctChannel ? $model->ctChannel->ccc_frontend_name : '-';
                },
                'filter' => ClientChatChannel::getList()
            ],

            [
                'attribute' => 'ct_language_id',
                'value' => static function (ClientChatChannelTranslate $model) {
                    return $model->ctLanguage ? $model->ctLanguage->language_id : '-';
                },
                'filter' => \common\models\Language::getLanguages()
            ],
            'ct_name',

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ct_created_user_id',
                'relation' => 'ctCreatedUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ct_updated_user_id',
                'relation' => 'ctCreatedUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ct_created_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ct_updated_dt',
                'format' => 'byUserDateTime'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

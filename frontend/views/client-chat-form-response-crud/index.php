<?php

use common\components\grid\DateTimeColumn;
use src\model\clientChatForm\entity\ClientChatForm;
use src\model\clientChatFormResponse\entity\ClientChatFormResponse;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var src\model\clientChatFormResponse\entity\ClientChatFormResponseSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Client Chat Form Response';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-form-response-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat Form Response', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'ccfr_id',
            'ccfr_uid',
            'ccfr_client_chat_id',

            [
                'attribute' => 'ccfr_form_id',
                'value' => static function (ClientChatFormResponse $model) {
                    return $model->clientChatForm ? $model->clientChatForm->ccf_name : '-';
                },
                'format' => 'raw',
                'filter' => ClientChatForm::getList(),
                'options' => ['style' => 'width: 100px']
            ],
            'ccfr_value',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ccfr_created_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ccfr_rc_created_dt',
                'format' => 'byUserDateTime'
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

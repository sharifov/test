<?php

use common\components\grid\UserSelect2Column;
use dosamigos\datepicker\DatePicker;
use sales\model\clientChatFeedback\entity\ClientChatFeedback;
use sales\widgets\UserSelect2Widget;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var sales\model\clientChatFeedback\entity\clientChatFeedbackSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Client Chat Feedback';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-feedback-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat Feedback', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'ccf_id',
            'ccf_client_chat_id',
            [
                'attribute' => 'ccf_user_id',
                'filter' => UserSelect2Widget::widget([
                    'model' => $searchModel,
                    'attribute' => 'ccf_user_id'
                ]),
                'format' => 'username',
                'options' => [
                    'width' => '200px'
                ],
            ],
            'ccf_client_id',
            [
                'attribute' => 'ccf_rating',
                'filter' => ClientChatFeedback::RATING_LIST,
                'format' => 'raw',
            ],
            [
                'attribute' => 'ccf_created_dt',
                'value' => static function (ClientChatFeedback $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->ccf_created_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'ccf_created_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'clearBtn' => true,
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                    'clientEvents' => [
                        'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                    ],
                ]),
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php

use sales\model\ClientChatCouchNote\entity\ClientChatCouchNote;
use sales\widgets\UserSelect2Widget;
use yii\bootstrap4\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\ClientChatCouchNote\entity\ClientChatCouchNoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Couch Notes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-couch-note-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat Couch Note', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'cccn_id',
            'cccn_cch_id',
            'cccn_rid',
            'cccn_message:ntext',
            'cccn_alias',
            [
                'attribute' => 'cccn_created_user_id',
                'filter' => UserSelect2Widget::widget([
                    'model' => $searchModel,
                    'attribute' => 'cccn_created_user_id'
                ]),
                'format' => 'username',
                'options' => [
                    'width' => '200px'
                ],
            ],
            [
                'attribute' => 'cccn_created_dt',
                'value' => static function (ClientChatCouchNote $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->cccn_created_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'cccn_created_dt',
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

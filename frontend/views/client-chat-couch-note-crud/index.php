<?php

use sales\model\ClientChatCouchNote\entity\ClientChatCouchNote;
use sales\widgets\UserSelect2Widget;
use yii\bootstrap4\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;

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
        'layout' => "{errors}\n{pager}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'cccn_id',
            'cccn_cch_id',
            'cccn_rid',
            [
                'attribute' => 'cccn_message',
                'value' => static function (ClientChatCouchNote $model) {
                    return Yii::$app->formatter->asNtext($model->cccn_message);
                },
                'contentOptions' => [
                    'style' => ['max-width' => '600px;', 'word-wrap' => 'break-word;']
                ],
                'format' => 'raw',
            ],
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
                'class' => DateTimeColumn::class,
                'attribute' => 'cccn_created_dt',
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

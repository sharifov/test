<?php

use common\components\grid\DateTimeColumn;
use common\models\Employee;
use sales\model\clientChat\cannedResponseCategory\entity\ClientChatCannedResponseCategory;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChat\cannedResponseCategory\entity\search\ClientChatCannedResponseCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Canned Response Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-canned-response-category-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat Canned Response Category', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'crc_id',
            'crc_name',
            'crc_enabled:boolean',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'crc_created_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'crc_updated_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'attribute' => 'crc_created_user_id',
                'value' => static function (ClientChatCannedResponseCategory $model) {
                    return Employee::findOne(['id' => $model->crc_created_user_id])->nickname ?? null;
                },
                'filter' => Employee::getList()
            ],
            [
                'attribute' => 'crc_updated_user_id',
                'value' => static function (ClientChatCannedResponseCategory $model) {
                    return Employee::findOne(['id' => $model->crc_updated_user_id])->nickname ?? null;
                },
                'filter' => Employee::getList()
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

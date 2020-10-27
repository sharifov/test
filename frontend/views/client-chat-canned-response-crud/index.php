<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use common\models\Employee;
use common\models\Language;
use common\models\Project;
use sales\model\clientChat\cannedResponse\entity\ClientChatCannedResponse;
use sales\model\clientChat\cannedResponseCategory\entity\ClientChatCannedResponseCategory;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChat\cannedResponse\entity\search\ClientChatCannedResponseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Canned Responses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-canned-response-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat Canned Response', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'cr_id',
            [
                'attribute' => 'cr_project_id',
                'value' => static function (ClientChatCannedResponse $model) {
                    return Project::findOne(['id' => $model->cr_project_id])->name ?? null;
                },
                'filter' => Project::getList()
            ],
            [
                'attribute' => 'cr_category_id',
                'value' => static function (ClientChatCannedResponse $model) {
                    return ClientChatCannedResponseCategory::findOne(['crc_id' => $model->cr_category_id])->crc_name ?? null;
                },
                'filter' => ClientChatCannedResponseCategory::getList()
            ],
            [
                'attribute' => 'cr_language_id',
                'value' => static function (ClientChatCannedResponse $model) {
                    return Language::findOne(['language_id' => $model->cr_language_id])->name ?? null;
                },
                'filter' => Language::getList()
            ],
            [
                'attribute' => 'cr_user_id',
                'value' => static function (ClientChatCannedResponse $model) {
                    return Employee::findOne(['id' => $model->cr_user_id])->nickname ?? null;
                },
                'filter' => Employee::getList()
            ],
            'cr_sort_order',
            //'cr_message',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cr_created_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cr_updated_dt',
                'format' => 'byUserDateTime'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php

use yii\grid\ActionColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use common\models\Employee;
use common\models\Language;
use common\models\Project;
use sales\model\clientChat\cannedResponse\entity\ClientChatCannedResponse;
use sales\model\clientChat\cannedResponseCategory\entity\ClientChatCannedResponseCategory;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;
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
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'cr_id',
            [
                'attribute' => 'cr_message',
                'value' => static function (ClientChatCannedResponse $model) {
                    if (strlen($model->cr_message) < 60) {
                        return Html::encode($model->cr_message);
                    }
                    $truncatedStr = StringHelper::truncate($model->cr_message, 60, '...');
                    $detailBox = '<div id="detail_' . $model->cr_id . '" style="display: none;">' . Html::encode($model->cr_message) . '</div>';
                    $detailBtn = ' <i class="fas fa-eye green show_detail" style="cursor: pointer;" data-idt="' . $model->cr_id . '"></i>';
                    return $truncatedStr . $detailBox . $detailBtn;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'cr_project_id',
                'value' => static function (ClientChatCannedResponse $model) {
                    return Yii::$app->formatter->asProjectName($model->crProject);
                },
                'filter' => Project::getList(),
                'format' => 'raw',
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

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php
yii\bootstrap4\Modal::begin([
        'title' => 'Detail',
        'id' => 'modal',
        'size' => \yii\bootstrap4\Modal::SIZE_LARGE,
    ]);
yii\bootstrap4\Modal::end();

$js = <<<JS
    $(document).on('click', '.show_detail', function(){
        
        let logId = $(this).data('idt');
        let detailEl = $('#detail_' + logId);
        let modalBodyEl = $('#modal .modal-body');
        
        modalBodyEl.html(detailEl.html()); 
        $('#modal-label').html('Detail (' + logId + ')');       
        $('#modal').modal('show');
        return false;
    });
JS;

$this->registerJs($js, \yii\web\View::POS_READY);

<?php

use common\components\grid\DateTimeColumn;
use kartik\grid\GridView;
use modules\user\userFeedback\entity\UserFeedback;
use modules\user\userFeedback\entity\UserFeedbackFile;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\user\userFeedback\entity\search\UserFeedbackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'My feedback';
$this->params['breadcrumbs'][] = $this->title;


$columns = [
    'uf_id',
    [
        'label' => 'Type',
        'attribute' => 'uf_type_id',
        'value' => static function (UserFeedback $model) {
            return $model->getTypeLabel();
        },
        'format' => 'raw',
        'filter' => UserFeedback::getTypeList()
    ],
    [
        'label' => 'Status',
        'attribute' => 'uf_status_id',
        'value' => static function (UserFeedback $model) {
            return $model->getStatusLabel();
        },
        'format' => 'raw',
        'filter' => UserFeedback::getStatusList()
    ],
    'uf_title',
    [
        'value' => static function (UserFeedback $model) {
            return UserFeedbackFile::find()->andWhere(['uff_uf_id' => $model->uf_id])->count();
        },
        'label' => 'Attached files',
    ],
    [
        'attribute' => 'uf_message',
        'value' => static function (UserFeedback $model) {
            if (!$model->uf_message) {
                return null;
            }
            $truncatedStr = StringHelper::truncate($model->uf_message, 400, '...', null, true);
            $detailBox = '<div id="detail_' . $model->uf_id . '" style="display: none;">' . $model->uf_message . '</div>';
            $detailBtn = ' <i class="fas fa-eye green showDetail" style="cursor: pointer;" data-idt="' . $model->uf_id . '"></i>';
            $resultStr = $truncatedStr . $detailBox . $detailBtn;
            return '<small>' . $resultStr . '</small>';
        },
        'format' => 'raw'
    ],
    ['class' => DateTimeColumn::class, 'attribute' => 'uf_created_dt'],
    [
        'attribute' => 'uf_resolution',
        'value' => static function (UserFeedback $model) {
            if (!$model->uf_resolution) {
                return null;
            }
            return '<pre><small>' . (StringHelper::truncate($model->uf_resolution, 400, '...', null, true)) . '</small></pre>';
        },
        'format' => 'raw'
    ],
    [
        'class' => \common\components\grid\UserSelect2Column::class,
        'attribute' => 'uf_resolution_user_id',
        'relation' => 'ufResolutionUser',
        'placeholder' => 'Select user'
    ],
    ['class' => DateTimeColumn::class, 'attribute' => 'uf_resolution_dt'],
    [
        'class'      => ActionColumn::class,
        'template' => '{view}',
        'urlCreator' => static function ($action, UserFeedback $model, $key, $index, $column) {
            if ($action === 'view') {
                return Url::toRoute([$action, 'uf_id' => $model->uf_id, 'uf_created_dt' => $model->uf_created_dt]);
            }
        }
    ],
];
?>
<div class="user-feedback-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(['id' => 'feedback-pjax-list']); ?>

    <?php $gridId = 'feedback-grid-id'; ?>


    <?= GridView::widget([
        'id' => $gridId,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
    ]); ?>

    <?php Pjax::end(); ?>

</div>
<?php
yii\bootstrap4\Modal::begin([
    'title' => 'Message Detail',
    'id' => 'modal',
    'size' => \yii\bootstrap4\Modal::SIZE_LARGE,
]);
yii\bootstrap4\Modal::end();


$jsCode = <<<JS
    $(document).on('click', '.showDetail', function(){
        
        let feedbackId = $(this).data('idt');
        let detailEl = $('#detail_' + feedbackId);
        let modalBodyEl = $('#modal .modal-body');
        
        modalBodyEl.html(detailEl.html()); 
        $('#modal-label').html('Detail User Feedback Message (' + feedbackId + ')');       
        $('#modal').modal('show');
        return false;
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);

<?php

use common\components\grid\UserSelect2Column;
use sales\model\userModelSetting\entity\UserModelSetting;
use yii\grid\ActionColumn;
use common\components\grid\DateTimeColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var sales\model\userModelSetting\entity\UserModelSettingSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'User Model Settings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-model-setting-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Model Setting', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-user-model-setting', 'scrollTo' => 0]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'ums_id',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ums_user_id',
                'relation' => 'umsUser',
                'placeholder' => '',
                'contentOptions' => ['style' => 'width:135px;'],
            ],
            'ums_name',
            [
                'attribute' => 'ums_type',
                'value' => static function (UserModelSetting $model) {
                    return $model->getTypeName();
                },
                'filter' => UserModelSetting::TYPE_LIST,
            ],
            'ums_class',
            [
                'attribute' => 'ums_settings_json',
                'format' => 'raw',
                'value' => static function (UserModelSetting $model) {
                    if (empty($model->ums_settings_json)) {
                        return Yii::$app->formatter->nullDisplay;
                    }
                    $truncatedStr = StringHelper::truncate(
                        Html::encode(VarDumper::dumpAsString($model->ums_settings_json)),
                        600,
                        '...',
                        null,
                        false
                    );
                    $detailData = VarDumper::dumpAsString($model->ums_settings_json, 10, true);
                    $detailBox = '<div id="detail_' . $model->ums_id . '" style="display: none;">' . $detailData . '</div>';
                    $detailBtn = ' <i class="fas fa-eye green showDetail" style="cursor: pointer;" data-idt="' . $model->ums_id . '"></i>';
                    $resultStr = $truncatedStr . $detailBox . $detailBtn;
                    return '<small>' . $resultStr . '</small>';
                },
                'enableSorting' => false,
            ],
            [
                'attribute' => 'ums_enabled',
                'value' => static function (UserModelSetting $model) {
                    if (empty($model->ums_enabled)) {
                        return Yii::$app->formatter->nullDisplay;
                    }
                    return Yii::$app->formatter->asBooleanByLabel($model->ums_enabled);
                },
                'filter' => [1 => 'Yes', 0 => 'No'],
                'format' => 'raw',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ums_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ums_updated_dt',
            ],
            ['class' => ActionColumn::class],
        ],
    ]) ?>

    <?php Pjax::end(); ?>
</div>

<?php
yii\bootstrap4\Modal::begin([
        'title' => 'Detail',
        'id' => 'modal',
        'size' => \yii\bootstrap4\Modal::SIZE_LARGE,
    ]);
yii\bootstrap4\Modal::end();

$jsCode = <<<JS
    $(document).on('click', '.showDetail', function(){
        
        let logId = $(this).data('idt');
        let detailEl = $('#detail_' + logId);
        let modalBodyEl = $('#modal .modal-body');
        
        modalBodyEl.html(detailEl.html()); 
        $('#modal-label').html('Detail (' + logId + ')');
        $('#modal').modal('show');
        return false;
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);


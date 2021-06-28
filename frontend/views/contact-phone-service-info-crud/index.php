<?php

use common\components\grid\DateTimeColumn;
use sales\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Contact Phone Service Infos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-phone-service-info-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Contact Phone Service Info', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-contact-phone-service-info']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [

            'cpsi_cpl_id',
            [
                'attribute' => 'cpsi_service_id',
                'value' => static function (ContactPhoneServiceInfo $model) {
                    return  ContactPhoneServiceInfo::getServiceName($model->cpsi_service_id);
                },
                'format' => 'raw',
                'filter' => ContactPhoneServiceInfo::SERVICE_LIST
            ],
            [
                'attribute' => 'lr_json_data',
                'format' => 'raw',
                'value' => static function (ContactPhoneServiceInfo $model) {
                    $resultStr = '-';
                    if ($decodedData = \frontend\helpers\JsonHelper::decode($model->cpsi_data_json)) {
                        $truncatedStr = StringHelper::truncate(
                            Html::encode(VarDumper::dumpAsString($decodedData)),
                            300,
                            '...',
                            null,
                            false
                        );

                        $detailData = VarDumper::dumpAsString($decodedData, 10, true);
                        $detailBox = '<div id="detail_' . $model->cpsi_cpl_id . '" style="display: none;">' . $detailData . '</div>';
                        $detailBtn = ' <i class="fas fa-eye green showDetail" style="cursor: pointer;" data-idt="' . $model->cpsi_cpl_id . '"></i>';

                        $resultStr = $truncatedStr . $detailBox . $detailBtn;
                    }
                    return '<small>' . $resultStr . '</small>';
                },
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cpsi_created_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cpsi_updated_dt',
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

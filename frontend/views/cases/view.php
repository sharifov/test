<?php

use common\models\Employee;
use modules\fileStorage\FileStorageSettings;
use modules\fileStorage\src\widgets\FileStorageListWidget;
use sales\auth\Auth;
use sales\helpers\cases\CasesViewRenderHelper;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\bootstrap4\Modal;

/**
 * @var $this yii\web\View
 * @var $model sales\entities\cases\Cases
 * @var $comForm frontend\models\CaseCommunicationForm
 * @var $previewEmailForm frontend\models\CasePreviewEmailForm
 * @var $previewSmsForm frontend\models\CasePreviewSmsForm
 * @var $dataProviderCommunication yii\data\ActiveDataProvider
 * @var $dataProviderCommunicationLog yii\data\ActiveDataProvider
 * @var $enableCommunication boolean
 * @var $isAdmin boolean
 *
 * @var $saleSearchModel common\models\search\SaleSearch
 * @var $saleDataProvider yii\data\ArrayDataProvider
 *
 * @var $csSearchModel common\models\search\CaseSaleSearch
 * @var $csDataProvider yii\data\ArrayDataProvider
 *
 * @var $leadSearchModel common\models\search\LeadSearch
 * @var $leadDataProvider yii\data\ArrayDataProvider
 *
 * @var $modelNote common\models\CaseNote
 * @var $dataProviderNotes yii\data\ArrayDataProvider
 *
 * @var $coupons \sales\model\coupon\entity\couponCase\CouponCase[]
 * @var $sendCouponsForm \sales\model\coupon\useCase\send\SendCouponsForm
 *
 * @var $fromPhoneNumbers array
 * @var bool $smsEnabled
 * @var array $unsubscribedEmails
 *
 * @var ActiveDataProvider $dataProviderOrders
 */

$this->title = 'Case ' . $model->cs_id;
$this->params['breadcrumbs'][] = ['label' => 'Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
\frontend\assets\CreditCardAsset::register($this);

$bundle = \frontend\themes\gentelella_v2\assets\AssetLeadCommunication::register($this);

/** @var Employee $user */
$user = Yii::$app->user->identity;

$clientProjectInfo = $model->client->clientProjects;
$unsubscribe = false;
if (isset($clientProjectInfo) && $clientProjectInfo) {
    foreach ($clientProjectInfo as $item) {
        if ($model->cs_project_id == $item['cp_project_id']) {
            $unsubscribe = $item['cp_unsubscribe'];
        }
    }
} else {
    $unsubscribe = false;
}

$unsubscribedEmails =  array_column($model->project->emailUnsubscribes, 'eu_email');
?>

<div class="cases-view">


    <h1>
        <?=$model->department ? '<i class="fa fa-sitemap"></i>  <span class="badge badge-warning">' . Html::encode($model->department->dep_name) . '</span>' : ''?>
        <?php /*=$model->project ? ' <span class="label label-warning">' . Html::encode($model->project->name) . '</span>': ''*/?>

    <?=$model->category ? Html::encode($model->category->cc_name) : '' ?>: <?= Html::encode($this->title) ?></h1>


    <div class="x_panel">
        <div class="x_content" style="display: block;">
            <p>
                <?php if ($model->isTrash()) :?>
                    <?php if (Auth::can('cases/take_Trash', ['case' => $model])) :?>
                        <?= CasesViewRenderHelper::renderChangeStatusButton($model->cs_status, $user)?>
                    <?php endif ?>
                <?php else :?>
                    <?= CasesViewRenderHelper::renderChangeStatusButton($model->cs_status, $user)?>
                <?php endif ?>

                <?= Html::button('<i class="fa fa-list"></i> Status History ' . ($model->caseStatusLogs ? '(' . count($model->caseStatusLogs) . ')' : ''), ['class' => 'btn btn-info', 'id' => 'btn-status-history', 'title' => 'Status history']) ?>
                <?= CasesViewRenderHelper::renderTakeButton($model, $user) ?>
                <?php if (Auth::can('cases/view_Checked', ['case' => $model])) : ?>
                    <?= CasesViewRenderHelper::renderCheckedButton($model) ?>
                <?php endif; ?>

                <?php /*= Html::a('Update', ['update', 'id' => $model->cs_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->cs_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $this->render('_general_info', [
                'model'      => $model,
                'isAdmin'       => $isAdmin
            ])
?>
        </div>
        <div class="col-md-4">
            <?php yii\widgets\Pjax::begin(['id' => 'pjax-client-info', 'enablePushState' => false, 'enableReplaceState' => false]) ?>
            <?= $this->render('_client_info', [
                'caseModel'      => $model,
                'isAdmin'       => $isAdmin,
                'unsubscribe' => $unsubscribe,
                'unsubscribedEmails' => $unsubscribedEmails,
            ])
?>
            <?php \yii\widgets\Pjax::end(); ?>
        </div>
        <div class="col-md-4">
            <?= $this->render('lead/_lead_info', [
                'caseModel'      => $model,
                'leadModel'      => $model->lead,

                'leadSearchModel' => $leadSearchModel,
                'leadDataProvider' => $leadDataProvider,

                'isAdmin'       => $isAdmin
            ])
?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $this->render('coupons/view', ['model' => $model, 'coupons' => $coupons, 'sendCouponsForm' => $sendCouponsForm]) ?>

            <?= $this->render('orders/case_orders', [
                'dataProviderOrders' => $dataProviderOrders,
            ]) ?>
        </div>

        <div class="col-md-6">
            <?php if ($enableCommunication) : ?>
                    <?= $this->render('communication/case_communication', [
                        'model'      => $model,
                        'previewEmailForm' => $previewEmailForm,
                        'previewSmsForm' => $previewSmsForm,
                        'comForm'       => $comForm,
                        'dataProvider'  => (bool)Yii::$app->params['settings']['new_communication_block_case'] ? $dataProviderCommunicationLog : $dataProviderCommunication,
                        'isAdmin'       => $isAdmin,
                        'isCommunicationLogEnabled' => Yii::$app->params['settings']['new_communication_block_case'],
                        'fromPhoneNumbers' => $fromPhoneNumbers,
                        'smsEnabled' => $smsEnabled,
                        'unsubscribedEmails' => $unsubscribedEmails,
                    ]);
                    ?>
            <?php else : ?>
                <div class="alert alert-warning" role="alert">You do not have access to view Communication block messages.</div>
            <?php endif;?>
        </div>


    </div>

    <?php if (FileStorageSettings::isEnabled() && Auth::can('case-view/files/view', ['case' => $model])) : ?>
        <div class="row">
            <div class="col-md-6">
            </div>
            <div class="col-md-6">
                <?= FileStorageListWidget::byCase(
                    $model->cs_id,
                    (
                        FileStorageSettings::canUpload()
                        && Auth::can('case-view/files/upload')
                        && Auth::can('cases/update', ['case' => $model])
                    )
                ) ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">

            <?= $this->render('notes/agent_notes', [
                'caseModel' => $model,
                'dataProviderNotes'  => $dataProviderNotes,
                'modelNote'  => $modelNote,
            ]); ?>


        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            <?= $this->render('sales/_sale_search', [
                'searchModel' => $saleSearchModel,
                'dataProvider' => $saleDataProvider,
                'caseModel' => $model,
                'isAdmin'       => $isAdmin
            ])
?>

            <?= $this->render('sales/_sale_list', [
                'searchModel' => $csSearchModel,
                'dataProvider' => $csDataProvider,
                'caseModel' => $model,
                'isAdmin'       => $isAdmin,
                'saleSearchModel' => $saleSearchModel,
                'saleDataProvider' => $saleDataProvider,
            ])
?>
        </div>
    </div>



</div>


<?php
Modal::begin([
    'id' => 'modalCaseSm',
    'title' => '',
    'size' => Modal::SIZE_SMALL,
    'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
]);
?>

<?php
Modal::end();
?>

<?php
Modal::begin([
    'id' => 'modalCase',
    'title' => '',
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
]);
?>

<?php
Modal::end();
?>


<?php
    $changeStatusAjaxUrl = \yii\helpers\Url::to(['cases/change-status', 'gid' => $model->cs_gid]);
    $statusHistoryAjaxUrl = \yii\helpers\Url::to(['cases/status-history', 'gid' => $model->cs_gid]);

    $js = <<<JS
     $(document).on('click', '#btn-change-status', function(){
            let modal = $('#modalCaseSm');
            //$('#search-sale-panel').toggle();
            modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
            $('#modalCaseSm-label').html($(this).attr('title'));
            
            $.get('$changeStatusAjaxUrl', function(data) {
                modal.find('.modal-body').html(data);
            });
            
           return false;
     });

    $(document).on('click', '#btn-status-history', function(){
            let modal = $('#modalCase');
            //$('#search-sale-panel').toggle();
            modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
            $('#modalCase-label').html($(this).attr('title'));
            
            $.get('$statusHistoryAjaxUrl', function(data) {
                modal.find('.modal-body').html(data);
            });
            
           return false;
     });
    
    
    $(document).on('click','#client-unsubscribe-button', function (e) {
        e.preventDefault();
        let url = $(this).data('unsubscribe-url');        
        $.ajax({
            url: url,               
            success: function(response){
                $.pjax.reload({container: '#pjax-client-info', timeout: 10000, async: false});
                if (Boolean(Number(response.data.action))){
                    new PNotify({title: "Communication", type: "info", text: 'Client communication restricted', hide: true});
                } else {
                    new PNotify({title: "Communication", type: "info", text: 'Client communication allowed', hide: true});
                }
                updateCommunication();                
            }
        });
    });
JS;

$this->registerJs($js);

Modal::begin([
    'title' => 'Client Chat Room',
    'id' => 'chat-room-popup',
    'size' => Modal::SIZE_LARGE
]);

Modal::end();

$jsCommBlockChatView = <<<JS

$('body').on('click', '.comm-chat-room-view', function(e) {  
    e.preventDefault();
    $.get(        
        '/client-chat-qa/room',       
        {
            id: $(this).data('id')
        },
        function (data) {
            $('#chat-room-popup .modal-body').html(data);
            $('#chat-room-popup').modal('show');
        }  
    );
});

JS;
$this->registerJs($jsCommBlockChatView);

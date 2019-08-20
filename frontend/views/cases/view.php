<?php

use sales\entities\cases\CasesStatus;
use sales\helpers\cases\CasesActionsHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/**
 * @var $this yii\web\View
 * @var $model sales\entities\cases\Cases
 * @var $comForm \frontend\models\CaseCommunicationForm
 * @var $previewEmailForm \frontend\models\CasePreviewEmailForm
 * @var $previewSmsForm \frontend\models\CasePreviewSmsForm
 * @var $dataProviderCommunication \yii\data\ActiveDataProvider
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
 *
 */

$this->title = 'Case ' . $model->cs_id;
$this->params['breadcrumbs'][] = ['label' => 'Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$bundle = \frontend\themes\gentelella\assets\AssetLeadCommunication::register($this);

$allowActionsList = CasesStatus::getAllowList($model->cs_status);

?>
<div class="cases-view">


    <h1>
        <?=$model->department ? '<i class="fa fa-sitemap"></i>  <span class="badge badge-warning">' . Html::encode($model->department->dep_name) . '</span>': ''?>
        <?/*=$model->project ? ' <span class="label label-warning">' . Html::encode($model->project->name) . '</span>': ''*/?>

    <?=$model->category ? Html::encode($model->category->cc_name) : '' ?>: <?= Html::encode($this->title) ?></h1>


    <div class="x_panel">
        <div class="x_content" style="display: block;">
            <p>
                <?= $allowActionsList ? Html::button('<i class="fa fa-exchange"></i> Change Status', ['class' => 'btn btn-warning', 'id' => 'btn-change-status', 'title' => 'Change Case status']) : ''?>
                <?= Html::button('<i class="fa fa-list"></i> Status History ' . ($model->casesStatusLogs ? '(' . count($model->casesStatusLogs) . ')' : ''), ['class' => 'btn btn-info', 'id' => 'btn-status-history', 'title' => 'Status history']) ?>
                <?= CasesActionsHelper::renderTakeButton($model, Yii::$app->user->id) ?>
                <?/*= Html::a('Update', ['update', 'id' => $model->cs_id], ['class' => 'btn btn-primary']) ?>
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
            <?= $this->render('_client_info', [
                'caseModel'      => $model,
                'isAdmin'       => $isAdmin
            ])
            ?>
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

            <?= $this->render('notes/agent_notes', [
                'caseModel' => $model,
                'dataProviderNotes'  => $dataProviderNotes,
                'modelNote'  => $modelNote,
            ]); ?>


        </div>

        <div class="col-md-6">
                <?php if ($enableCommunication) : ?>
                <?= $this->render('communication/case_communication', [
                    'model'      => $model,
                    'previewEmailForm' => $previewEmailForm,
                    'previewSmsForm' => $previewSmsForm,
                    'comForm'       => $comForm,
                    'dataProvider'  => $dataProviderCommunication,
                    'isAdmin'       => $isAdmin
                ]);
                ?>
            <?php else: ?>
                <div class="alert alert-warning" role="alert">You do not have access to view Communication block messages.</div>
            <?php endif;?>
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

<style type="text/css">
    @media screen and (min-width: 768px) {
        .modal-dialog {
            width: 800px; /* New width for default modal */
        }
        .modal-sm {
            width: 350px; /* New width for small modal */
        }
    }
    @media screen and (min-width: 992px) {
        .modal-lg {
            width: 80%; /* New width for large modal */
        }
    }
</style>

<?php
yii\bootstrap\Modal::begin([
    'id' => 'modalCaseSm',
    //'headerOptions' => ['id' => 'modalCaseSmHeader'],
    'size' => \yii\bootstrap\Modal::SIZE_SMALL,
    'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
]);
?>

<?php
yii\bootstrap\Modal::end();
?>

<?php
yii\bootstrap\Modal::begin([
    'id' => 'modalCase',
    //'headerOptions' => ['id' => 'modalCaseHeader'],
    'size' => \yii\bootstrap\Modal::SIZE_DEFAULT,
    'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
]);
?>

<?php
yii\bootstrap\Modal::end();
?>


<?php
    $ajaxUrl = \yii\helpers\Url::to(['cases/change-status', 'gid' => $model->cs_gid]);
    $statusHistoryajaxUrl = \yii\helpers\Url::to(['cases/status-history', 'gid' => $model->cs_gid]);

    $js = <<<JS
     $(document).on('click', '#btn-change-status', function(){
            var modal = $('#modalCaseSm');
            //$('#search-sale-panel').toggle();
            modal.modal('show').find('.modal-body').html('<div style="text-align:center"><img width="200px" src="https://loading.io/spinners/gear-set/index.triple-gears-loading-icon.svg"></div>');
            modal.modal('show').find('.modal-header').html('<h3>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
            
            $.get('$ajaxUrl', function(data) {
                modal.find('.modal-body').html(data);
            });
            
           return false;
     });

    $(document).on('click', '#btn-status-history', function(){
            var modal = $('#modalCase');
            //$('#search-sale-panel').toggle();
            modal.modal('show').find('.modal-body').html('<div style="text-align:center"><img width="200px" src="https://loading.io/spinners/gear-set/index.triple-gears-loading-icon.svg"></div>');
            modal.modal('show').find('.modal-header').html('<h3>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
            
            $.get('$statusHistoryajaxUrl', function(data) {
                modal.find('.modal-body').html(data);
            });
            
           return false;
     });
JS;

$this->registerJs($js);

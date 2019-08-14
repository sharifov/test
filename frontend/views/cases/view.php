<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var $this yii\web\View
 * @var $model sales\entities\cases\Cases
 * @var $comForm \frontend\models\CaseCommunicationForm
 * @var $previewEmailForm \frontend\models\CasePreviewEmailForm
 * @var $previewSmsForm \frontend\models\CasePreviewSmsForm
 * @var $dataProviderCommunication \yii\data\ActiveDataProvider
 * @var $enableCommunication boolean
 * @var $isAdmin boolean
 */

$this->title = $model->cs_id;
$this->params['breadcrumbs'][] = ['label' => 'Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$bundle = \frontend\themes\gentelella\assets\AssetLeadCommunication::register($this);

?>
<div class="cases-view">


    <h1>
        <?=$model->department ? '<i class="fa fa-sitemap"></i>  <span class="label label-warning">' . Html::encode($model->department->dep_name) . '</span>': ''?>
        <?=$model->project ? ' <span class="label label-warning">' . Html::encode($model->project->name) . '</span>': ''?>

    - Case <?= Html::encode($this->title) ?>: <?=Html::encode($model->cs_subject)?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->cs_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->cs_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'cs_id',
                    'cs_status',
                    'cs_project_id',
                    'cs_user_id',
                    'cs_subject',
                    'cs_description:ntext',
                    'cs_category',
                    'cs_lead_id',
                    'cs_call_id',
                    'cs_dep_id',
                    'cs_client_id',
                    'cs_created_dt',
                    'cs_updated_dt',
                ],
            ]) ?>
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



</div>

<?php


    $flowTransitionUrl = \yii\helpers\Url::to([
        'case/flow-transition',
        'caseId' => $model->cs_id
    ]);

    $js = <<<JS
    /*$('#view-flow-transition').click(function() {
        $('#preloader').removeClass('hidden');
        var editBlock = $('#get-request-flow-transition');
        editBlock.find('.modal-body').html('');
        editBlock.find('.modal-body').load('$flowTransitionUrl', function( response, status, xhr ) {
            $('#preloader').addClass('hidden');
            editBlock.modal('show');
        });
    });*/
    
    $(function () {
        $.scrollUp({
            scrollName: 'scrollUp', // Element ID
            topDistance: '300', // Distance from top before showing element (px)
            topSpeed: 300, // Speed back to top (ms)
            animation: 'fade', // Fade, slide, none
            animationInSpeed: 200, // Animation in speed (ms)
            animationOutSpeed: 200, // Animation out speed (ms)
            scrollText: 'Scroll to top', // Text for element
            activeOverlay: true, // Set CSS color to display scrollUp active point, e.g '#00FFFF'
        });
    
        //$("[data-toggle='tooltip']").tooltip();
        //$("[data-toggle='popover']").popover({sanitize: false});
    
    });
    
JS;

    $this->registerJs($js);

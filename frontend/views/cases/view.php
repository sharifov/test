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
 */

$this->title = 'Case: ' . $model->cs_id;
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
        <div class="col-md-4">
            <?= $this->render('_general_info', [
                'model'      => $model,
                'isAdmin'       => $isAdmin
            ])
            ?>
        </div>
        <div class="col-md-4">
            <?= $this->render('_client_info', [
                'model'      => $model->client,
                'isAdmin'       => $isAdmin
            ])
            ?>
        </div>
        <div class="col-md-4">
            <?= $this->render('_lead_info', [
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
            <?= $this->render('_sale_search', [
                'searchModel' => $saleSearchModel,
                'dataProvider' => $saleDataProvider,
                'caseModel' => $model,
                'isAdmin'       => $isAdmin
            ])
            ?>
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
            <?/*= $this->render('_sale_list', [
                'searchModel' => $csSearchModel,
                'dataProvider' => $csDataProvider,
                'caseModel' => $model,
                'isAdmin'       => $isAdmin
            ])*/
            ?>
        </div>
    </div>



</div>

<?php
    $js = <<<JS
  
    });
    
JS;

    //$this->registerJs($js);

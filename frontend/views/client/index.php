<?php

use common\models\Client;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use src\auth\Auth;
use common\components\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Clients';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <p>
        <?= Html::a('Create Client', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
    $filterProjects = \common\models\Project::getList();
    $filterProjects['-1'] = 'Without project';

    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'uuid',
            'parent_id',
            [
                'attribute' => 'first_name',
                'value' => static function ($model) {
                    $data = \common\helpers\LogHelper::hidePersonalData($model->toArray(), ['first_name']);
                    return $data ['first_name'];
                },
            ],
            //'first_name',
            'middle_name',
            'last_name',
            'company_name',
            'is_company:boolean',
            'is_public:boolean',
            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'cl_project_id',
                'relation' => 'project',
                'filter' => $filterProjects,
            ],
            'disabled:boolean',
            'cl_excluded:boolean',
            [
                'label' => 'Client Return Type',
                'value' => static function (Client $model) {
                    return \src\helpers\client\ClientReturnHelper::displayClientReturnLabels($model->id, Auth::id());
                },
                'format' => 'raw'
            ],
            [
                'header' => 'Phones',
                'attribute' => 'client_phone',
                'value' => function (Client $model) {
                    return \frontend\widgets\SliceAndShowMoreWidget::widget([
                        'data' => $model->getOnlyPhonesMask($model->getOnlyPhones()),
                        'separator' => ' <i class="fa fa-phone"><code></code></i>'
                    ]);
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-left'],
            ],

            [
                'header' => 'Emails',
                'attribute' => 'client_email',
                'value' => function (Client $model) {
                    return \frontend\widgets\SliceAndShowMoreWidget::widget([
                        'data' => $model->getOnlyEmailsMask($model->getOnlyEmails()),
                        'separator' => ' <i class="fa fa-phone"><code></code></i>'
                    ]);
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-left'],
            ],

            //'created',
            //'updated',

            [
                'header' => 'Leads',
                'value' => function (Client $model) use ($searchModel) {
                    $limit = $searchModel->getLeadsLimit();
                    $leads = $model->getLeadIdsAndRequestIp($limit);
                    $data = [];
                    if ($leads) {
                        foreach ($leads as $lead) {
                            $data[] = '<i class="fa fa-link"></i> ' . Html::a('lead: ' . $lead['id'], ['/leads/view', 'id' => $lead['id'], 'showInPopUp' => 'modal'], ['title' => 'Lead: ' . $lead['id'], 'class' => "show-modal", "data-id" => $lead['id'], 'target' => '_blank', 'data-pjax' => 0]) . ' (IP: ' . $lead['request_ip'] . ')';
                        }
                        if ($model->leadsCountByClient() > $limit) {
                            $data[] = '<i class="fas fa-eye green"></i> ' . Html::a('Show more', ['/client/view', 'id' => $model->id, '#' => 'pjax-client-leads'], ['title' => 'Show more', 'target' => '_blank', 'data-pjax' => 0]);
                        }
                    }

                    $str = '';
                    if ($data) {
                        $str = '' . implode('<br>', $data) . '';
                    }

                    return $str;
                },
                'format' => 'raw',
                //'options' => ['style' => 'width:100px']
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'created'
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'updated'
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}',
                'visibleButtons' => [
                    'view' => static function (Client $model, $key, $index) {
                        return Auth::can('/client/view');
                    },
                    'update' => static function (Client $model, $key, $index) {
                        return Auth::can('/client/update');
                    }
                ]
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>


    <style type="text/css">
        @media screen and (min-width: 768px) {
            .modal-dialog {
                width: 700px; /* New width for default modal */
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
yii\bootstrap4\Modal::begin([
    'id' => 'modalClient',
    'size' => \yii\bootstrap4\Modal::SIZE_LARGE,
    'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
]);
echo "<div id='modalClientContent'></div>";
yii\bootstrap4\Modal::end();



$jsCode = <<<JS

    $(document).on('click', '.show-modal', function(){
        //e.preventDefault();
        $('#modalClient').modal('show').find('#modalClientContent').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');

        $('#modalClient-label').html($(this).attr('title'));
        $.get($(this).attr('href'), function(data) {
          $('#modalClient').find('#modalClientContent').html(data);
        });
       return false;
    });


JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);

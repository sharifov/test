<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\BooleanColumn;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChatChannel\entity\search\ClientChatChannelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Channels';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-channel-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Client Chat Channel', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Validate all', ['validate-all'], [
            'class' => 'btn btn-primary',
            'data' => [
                'confirm' => 'Are you sure you want to Validate all?',
                'method' => 'post',
            ],
        ]) ?>
       <?= Html::a('Register all', ['register-all'], [
            'class' => 'btn btn-primary',
            'data' => [
                'confirm' => 'Are you sure you want to Register all?',
                'method' => 'post',
            ],
        ]) ?>
       <?= Html::a('UnRegister all', ['un-register-all'], [
            'class' => 'btn btn-primary',
            'data' => [
                'confirm' => 'Are you sure you want to UnRegister all?',
                'method' => 'post',
            ],
        ]) ?>
    </p>


    <?php Pjax::begin(['id' => 'client-chat-channel-pjax']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-hover'],
        'rowOptions' => static function (ClientChatChannel $model) {

            if ($model->ccc_disabled) {
                return [
                    'class' => 'danger'
                ];
            }

        },
        'columns' => [
            ['attribute' => 'ccc_id',
                'headerOptions' => ['style' => 'width:70px'],
            ],
            [
                'attribute' => 'ccc_project_id',
                'format' => 'projectName',
                'filter' => \common\models\Project::getList()
            ],
            //'ccc_name',
            [
                'label' => 'Name',
                'attribute' => 'ccc_name',
                'value' => static function (ClientChatChannel $model) {
                    return $model->ccc_name;
                },
                'filter' => ClientChatChannel::getListWithNames()

            ],
            ['class' => BooleanColumn::class, 'attribute' => 'ccc_registered'],
            //'ccc_frontend_name',
            [
                'label' => 'Frontend Name',
                'attribute' => 'ccc_frontend_name',
                'value' => static function (ClientChatChannel $model) {
                    return $model->ccc_frontend_name;
                },
                'filter' => ClientChatChannel::getListWithFrontedNames()

            ],
            [
                'label' => 'Translations',
                'value' => static function (ClientChatChannel $model) {
                    $translates = $model->clientChatChannelTranslates;
                    $val = null;
                    if ($translates) {
                        $data = \yii\helpers\ArrayHelper::map($translates, 'ct_language_id', 'ct_language_id');
                        if ($data) {
                            $val = '<label class="label label-default">' . implode('</label> <span class="label label-default">', $data) . '</label>';
                        }
                    }

                    return $val ?: '-';
                },
                'format' => 'raw'
            ],
            //'ccc_dep_id:departmentName',
            [
                'attribute' => 'ccc_dep_id',
                'format' => 'departmentName',
                'filter' => \common\models\Department::getList()
            ],
            [
                'attribute' => 'ccc_ug_id',
                'value' => static function (ClientChatChannel $model) {
                    return $model->cccUg ? $model->cccUg->ug_name : null;
                }
            ],
            'ccc_disabled:boolean',
//            ['class' => BooleanColumn::class, 'attribute' => 'ccc_disabled'],
            //'ccc_frontend_enabled:booleanByLabel',
            ['class' => BooleanColumn::class, 'attribute' => 'ccc_frontend_enabled'],
            //'ccc_default:boolean',
            ['class' => BooleanColumn::class, 'attribute' => 'ccc_default'],
            ['class' => BooleanColumn::class, 'attribute' => 'ccc_default_device'],
            'ccc_priority',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ccc_created_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ccc_updated_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ccc_created_user_id',
                'relation' => 'cccCreatedUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ccc_updated_user_id',
                'relation' => 'cccUpdatedUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{default} {translate} {view} {update} {delete} ',
                'contentOptions' => ['style' => 'width: 90px;'],
                'visibleButtons' => [
                    /*'view' => function ($model, $key, $index) {
                        return User::hasPermission('viewOrder');
                    },*/
//                    'update' => static function ($model, $key, $index) use ($user) {
//                        return $user->isAdmin();
//                    },
//
//                    'delete' => static function ($model, $key, $index) use ($user) {
//                        return $user->isAdmin();
//                    },

//                    'translate' => static function (ClientChatChannel $model, $key, $index) use ($user) {
//                        return $user->isAdmin() && $model->isIn() && ($model->isStatusIvr() || $model->isStatusQueue() || $model->isStatusRinging() || $model->isStatusInProgress());
//                    },


                ],
                'buttons' => [
                    'translate' => static function ($url, ClientChatChannel $model) {
                        return Html::a('<i class="fa fa-language text-info"></i>', ['client-chat-channel-translate/index', 'ClientChatChannelTranslateSearch[ct_channel_id]' => $model->ccc_id], [
                            'title' => 'Translate',
                            'data-pjax' => 0,
                            'target' => '_blank',
                        ]);
                    },
                    'default' => static function ($url, ClientChatChannel $model) {
                        if (!$model->ccc_default) {
                            return Html::a('<i class="fas fa-cog"></i>', ['client-chat-channel-crud/set-default', 'channel_id' => $model->ccc_id], ['class' => 'set_default', 'data-pjax' => 0,]);
                        }
                    }
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

    <?php

    $js = <<<JS
$(document).on('click', '.set_default', function (e) {
    e.preventDefault();
    let btn = $(this);
    
    $.ajax({
        url: btn.attr('href'),
        type: 'get',
        dataType: 'json',
        beforeSend: function () {
            btn.find('i').addClass('fa-spin');
        },
        complete: function () {
            btn.find('i').removeClass('fa-spin');
        },
        success: function (data) {
            let type = 'success';  
            let title = 'Success';  
            if (data.error) {
              type = 'error';  
              title = 'Error';  
            } else {
                pjaxReload({container: '#client-chat-channel-pjax'});
            }
            createNotify(title, data.message, type);
        },
        error: function (xhr, txt) {
            
        }
        
    })
})
JS;

    $this->registerJs($js);
    ?>
</div>

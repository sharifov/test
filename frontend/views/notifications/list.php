<?php

use dosamigos\datepicker\DatePicker;
use frontend\widgets\multipleUpdate\myNotifications\MultipleUpdateButtonWidget;
use kartik\grid\GridView;
use kartik\grid\GridViewInterface;
use modules\notification\src\abac\dto\NotificationAbacDto;
use modules\notification\src\abac\NotificationAbacObject;
use src\auth\Auth;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\NotificationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('notifications', 'My Notifications');
$this->params['breadcrumbs'][] = $this->title;
$gridId = 'notifications-list-gv';
$pjaxId = 'my-notifications-pjax';

$notificationAbacDto = new NotificationAbacDto(null);
/** @abac $abacDto, NotificationAbacDto::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_MULTIPLE_UPDATE_MAKE_READ, Access to btn multiple update notification */
$canMultipleUpdateRead = Yii::$app->abac->can($notificationAbacDto, NotificationAbacObject::OBJ_NOTIFICATION_MULTIPLE_UPDATE, NotificationAbacObject::ACTION_MULTIPLE_UPDATE_MAKE_READ, Auth::user());
?>
<div class="notifications-list">

    <h1><i class="fa fa-bell-o"></i> <?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('<i class="fa fa-check"></i> Make Read All', ['all-read'], [
            'class' => 'btn btn-info',
            'data' => [
                'confirm' => Yii::t('notifications', 'Are you sure you want to mark read all notifications?'),
                'method' => 'post',
            ],
        ]) ?>

        <?= Html::a('<i class="fa fa-times"></i> Delete All', ['all-delete'], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('notifications', 'Are you sure you want to delete all notifications?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php Pjax::begin(['id' => $pjaxId]); ?>
    <?php if ($canMultipleUpdateRead) : ?>
        <?= MultipleUpdateButtonWidget::widget([
          'gridId' => $gridId,
          'pjaxId' => $pjaxId,
      ]) ?>
    <?php endif; ?>



    <?php
    $columns = [
        ['class' => 'yii\grid\SerialColumn', 'contentOptions' => ['class' => 'serial-td']],

        //'n_id',

        //'n_user_id',

        //'n_title',
        [
            'attribute' => 'n_type_id',
            //'format' => 'html',
            'value' => function (\common\models\Notifications $model) {
                return $model->typeIcon . ' ' . Html::tag('small', $model->getType());
            },
            'format' => 'raw',
            'filter' => \common\models\Notifications::getTypeList()
        ],

        [
            'attribute' => 'n_title',
            'value' => static function (\common\models\Notifications $model) {
                return Html::a($model->n_title, ['/notifications/view2', 'id' => $model->n_id], ['data-pjax' => 0]);
            },
            'format' => 'raw',
            'contentOptions' => ['class' => 'group-td']
        ],

        //'n_message:ntextWithPurify',
        'n_message:textWithLinks',
        //'n_message:raw',

        'n_new:boolean',

        //'n_deleted:boolean',
        //'n_popup:boolean',
        //'n_popup_show:boolean',

//        [
//            'attribute' => 'n_read_dt',
//            'value' => static function (\common\models\Notifications $model) {
//                return $model->n_read_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->n_read_dt)) : '-';
//            },
//            'format' => 'raw',
//            'filter' => DatePicker::widget([
//                'model' => $searchModel,
//                'attribute' => 'n_read_dt',
//                'clientOptions' => [
//                    'autoclose' => true,
//                    'format' => 'yyyy-mm-dd',
//                ],
//                'options' => [
//                    'autocomplete' => 'off',
//                    'placeholder' => 'Choose Date'
//                ],
//            ]),
//        ],

        [
            'attribute' => 'n_created_dt',
            'value' => static function (\common\models\Notifications $model) {
                return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->n_created_dt));
            },
            'format' => 'raw',
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'n_created_dt',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                ],
                'options' => [
                    'autocomplete' => 'off',
                    'placeholder' => 'Choose Date'
                ],
            ]),
        ],

        //['class' => 'yii\grid\ActionColumn'],
        [
            'class' => 'yii\grid\ActionColumn',
            //'controller' => 'order-shipping',
            'template' => '{view2} {soft-delete}',

            'buttons' => [
                'view2' => function ($url, $model) {
                    return Html::a('<i class="glyphicon glyphicon-search"></i>', $url, [
                        'title' => Yii::t('notifications', 'View'), 'data-pjax' => 0
                    ]);
                },
                'soft-delete' => function ($url, $model) {
                    return Html::a('<i class="glyphicon glyphicon-remove-circle"></i>', $url, [
                        'title' => Yii::t('notifications', 'Delete'),
                        'data' => [
                            'confirm' => Yii::t('notifications', 'Are you sure you want to delete this message?'),
                            //'method' => 'post',
                        ],
                    ]);
                }
            ],
        ],
    ];
    if ($canMultipleUpdateRead) {
        array_unshift($columns, [
            'class' => '\kartik\grid\CheckboxColumn',
            'name' => 'NotificationMultipleForm[lead_list]',
            'pageSummary' => true,
            'rowSelectedClass' => GridViewInterface::TYPE_INFO,
        ]);
    }
    ?>
    <?= GridView::widget([
        'id' => $gridId,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-condensed table-responsive tbl-notif-list'],
        'rowOptions' => function (\common\models\Notifications $model, $index, $widget, $grid) {
            /*if($model->n_type_id == 4) {
                return ['style' => 'background-color:#f2dede'];
            }
            if($model->n_type_id == 3) {
                return ['style' => 'background-color:#fcf8e3'];
            }
            if($model->n_type_id == 1) {
                return ['style' =>  'background-color:#dff0d8'];
            }
            if($model->n_type_id == 2) {
                return ['style' =>  'background-color:#d9edf7'];
            }*/

            if ($model->n_new) {
                return ['class' =>  'bold'];
            }
        },
        'columns' => $columns,
    ]); ?>
    <?php
    $js = <<<JS
$(document).ready(function () {
    let title;
    let sameNotif = false;
    let sameNotifCnt = 0;
    var serialClass;
    $('.tbl-notif-list tbody tr').each(function (i, e) {
        let elemTitle = $(e).find('td.group-td').text();
        if (elemTitle === title) {
            sameNotif = true;
            sameNotifCnt++;
            if (sameNotifCnt === 1) {
                let arrowDown = $('<i class="fa fa-angle-double-down" style="cursor: pointer; font-weight: 900;"></i>');
                let prevId = $(e).prev().find('td.serial-td');
                let serialNumber = prevId.text();
                serialClass = 'group-' + serialNumber;
                prevId.append(' ').append(arrowDown);
                let tmpClass = '.' + serialClass;
                arrowDown.on('click', function () {
                    $(this).toggleClass('fa-angle-double-down fa-angle-double-up');
                    $('.tbl-notif-list').find(tmpClass).toggle();
                });
            }
            $(e).addClass(serialClass).hide();
        } else {
            sameNotif = false;
            sameNotifCnt = 0;
            serialClass = '';
        }
        title = elemTitle;
    });
});
JS;
    $this->registerJs($js);
    ?>
    <?php Pjax::end(); ?>
</div>

<?php
$js = <<<JS
    $(document).on('pjax:success', function() {
        $("html, body").animate({ scrollTop: $('#notifications-list-gv').position().top }, 400);
    });
JS;

$this->registerJs($js, $this::POS_END);
?>
<?php
$css = <<<CSS
.tbl-notif-list.table-striped > tbody > tr:nth-child(2n+1) > td, 
.tbl-notif-list.table-striped > tbody > tr:nth-child(2n+1) > th,
.tbl-notif-list.table-striped tbody tr:nth-of-type(odd) {
    background-color: initial !important;
}
.tbl-notif-list .bold {
font-weight: bold;
}
CSS;
$this->registerCss($css);

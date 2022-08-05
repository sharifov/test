<?php

use yii\widgets\Pjax;
use yii\grid\GridView;
use common\components\grid\UserSelect2Column;
use dosamigos\datepicker\DatePicker;

?>

<?php Pjax::begin(); ?>
<?php /*echo $this->render('_info_emails_search', ['model' => $emailSearchModel]); */ ?>
<h5>Emails Stats</h5>
<div class="well">
    <?= GridView::widget([
        'dataProvider' => $emailDataProvider,
        'filterModel' => $emailSearchModel,
        'emptyTextOptions' => [
            'class' => 'text-center'
        ],
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'e_id',
//        ['class' => 'yii\grid\ActionColumn',
//            'template' => '{view} {update} {delete}',
//            'visibleButtons' => [
//                /*'view' => function ($model, $key, $index) {
//                    return User::hasPermission('viewOrder');
//                },*/
//                'update' => static function ($model, $key, $index) use ($user) {
//                    return $user->isAdmin();
//                },
//
//                'delete' => static function ($model, $key, $index) use ($user) {
//                    return $user->isAdmin();
//                },
//            ],
//        ],
            //'e_reply_id',

            //'e_project_id',
            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'e_project_id',
                'relation' => 'eProject',
            ],
//            [
//                'attribute' => 'e_project_id',
//                'value' => static function (\common\models\Email $model) {
//                    return $model->project ? '<span class="badge badge-info">' . Html::encode($model->project->name) . '</span>' : '-';
//                },
//                'format' => 'raw',
//                'filter' => $projectList
//            ],
            'e_email_from',
            'e_email_to',

            'e_lead_id',
            'e_case_id',
            //'e_email_cc:email',
            //'e_email_bc:email',
            //'e_email_subject:email',
            //'e_email_body_text:ntext',
            //'e_attach',
            //'e_email_data:ntext',
            //'e_type_id',
            [
                'attribute' => 'e_type_id',
                'value' => static function (\common\models\Email $model) {
                    return $model->getTypeName();
                },
                'filter' => \common\models\Email::TYPE_LIST
            ],
            //'e_template_type_id',
//        [
//            'attribute' => 'e_template_type_name',
//            'value' => static function (\common\models\Email $model) {
//                return $model->templateType ? $model->templateType->etp_name : '-';
//            },
//            'label' => 'Template Name'
//            //'filter' =>
//        ],
//        //'e_language_id',
//        [
//            'attribute' => 'e_language_id',
//            'value' => static function (\common\models\Email $model) {
//                return $model->e_language_id;
//            },
//            'filter' => \common\models\Language::getLanguages(true)
//        ],
            'e_communication_id',
            //'e_is_deleted',
            //'e_is_new:boolean',
            //'e_delay',
            //'e_priority',
            //'e_status_id',
            [
                'attribute' => 'e_status_id',
                'value' => static function (\common\models\Email $model) {
                    return $model->getStatusName();
                },
                'filter' => \common\models\Email::STATUS_LIST
            ],
            'attribute' => 'e_client_id:client',
            //'e_status_done_dt',
            //'e_read_dt',
            //'e_error_message',
            /*[
                'attribute' => 'e_updated_user_id',
                'value' => static function (\common\models\Email $model) {
                    return ($model->updatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->updatedUser->username) : $model->e_updated_user_id);
                },
                'filter' => $userList,
                'format' => 'raw'
            ],*/

//        [
//            'class' => UserSelect2Column::class,
//            'attribute' => 'e_created_user_id',
//            'relation' => 'createdUser',
//            'placeholder' => ''
//        ],

//            [
//                'attribute' => 'e_created_user_id',
//                'value' => static function (\common\models\Email $model) {
//                    return ($model->createdUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->createdUser->username) : $model->e_created_user_id);
//                },
//                'filter' => $userList,
//                'format' => 'raw'
//            ],
            /*[
                'attribute' => 'e_updated_dt',
                'value' => static function (\common\models\Email $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->e_updated_dt));
                },
                'format' => 'raw'
            ],*/

            /*[
                'attribute' => 'e_created_user_id',
                'value' => static function (\common\models\Email $model) {
                    return  ($model->createdUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->createdUser->username) : $model->e_created_user_id);
                'format' => 'raw'
                },
            ],*/
            [
                'attribute' => 'e_created_dt',
                'value' => static function (\common\models\Email $model) {
                    return $model->e_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->e_created_dt), 'php: Y-m-d [H:i:s]') : '-';
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $emailSearchModel,
                    'attribute' => 'e_created_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off'
                    ],
                ]),
            ],


        ],
    ]); ?>
</div>
<?php Pjax::end(); ?>

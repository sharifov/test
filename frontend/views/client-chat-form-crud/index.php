<?php

use common\components\grid\project\ProjectColumn;
use src\model\clientChatForm\entity\abac\ClientChatFormAbacObject;
use src\model\clientChatForm\entity\ClientChatForm;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\UserSelect2Column;
use dosamigos\datepicker\DatePicker;

/* @var yii\web\View $this */
/* @var src\model\clientChatForm\entity\ClientChatFormSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Client Chat Forms';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-form-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat Form', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'ccf_id',
            'ccf_key',
            'ccf_name',
            [
                'class' => ProjectColumn::class,
                'attribute' => 'ccf_project_id',
                'relation' => 'project',
            ],
            [
                'attribute' => 'ccf_enabled',
                'value' => static function (ClientChatForm $model) {
                    return $model->ccf_enabled ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
                },
                'filter' => [1 => 'Yes', 0 => 'No'],
                'format' => 'raw',
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ccf_created_user_id',
                'relation' => 'createdUser',
                'options' => ['style' => 'width:140px']
            ],
            [
                'attribute' => 'ccf_created_dt',
                'value' => static function (ClientChatForm $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->ccf_created_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'ccf_created_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'clearBtn' => true,
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Choose Date',
                        'readonly' => '1',
                    ],
                    'clientEvents' => [
                        'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                    ],
                ]),
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update} {form_builder} {delete}',
                'visibleButtons' => [
                    'delete' => static function (ClientChatForm $model) {
                          /** @abac ClientChatFormAbacObject::UI_CRUD, ClientChatFormAbacObject::ACTION_DELETE, Delete to button take */
                         return Yii::$app->abac->can(null, ClientChatFormAbacObject::UI_CRUD, ClientChatFormAbacObject::ACTION_DELETE);
                    },
                    'update' => static function (ClientChatForm $model) {
                        /** @abac ClientChatFormAbacObject::UI_CRUD, ClientChatFormAbacObject::ACTION_UPDATE, Update to button take */
                        return Yii::$app->abac->can(null, ClientChatFormAbacObject::UI_CRUD, ClientChatFormAbacObject::ACTION_UPDATE);
                    },
                    'form_builder' => static function (ClientChatForm $model) {
                        /** @abac ClientChatFormAbacObject::UI_CRUD, ClientChatFormAbacObject::ACTION_UPDATE, Update to button take */
                        return Yii::$app->abac->can(null, ClientChatFormAbacObject::UI_CRUD, ClientChatFormAbacObject::ACTION_UPDATE);
                    },
                ],
                'buttons' => [
                    'form_builder' => static function ($url, ClientChatForm $model) {
                        return Html::a(
                            Html::tag('span', '', ['class' => 'glyphicon glyphicon-cog']),
                            ['/client-chat-form-crud/builder', 'id' => $model->ccf_id],
                            [
                                'title' => 'Form Builder',
                                'data-id' => $model->ccf_id,
                                'data-pjax' => '0',
                            ]
                        );
                    },
                ]
            ]
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

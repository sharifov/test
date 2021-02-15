<?php

use sales\helpers\text\SecureStringHelper;
use sales\model\userClientChatData\entity\UserClientChatData;
use yii\grid\ActionColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;

/* @var yii\web\View $this */
/* @var sales\model\userClientChatData\entity\UserClientChatDataSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'User Client Chat Data';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-client-chat-data-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Client Chat Data', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-user-client-chat-data']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'uccd_employee_id',
                'relation' => 'uccdEmployee',
                'placeholder' => '',
            ],
            'uccd_username',
            'uccd_name',
            'uccd_rc_user_id',
            [
                'attribute' => 'uccd_auth_token',
                'value' => static function (UserClientChatData $model) {
                    if (!$model->uccd_auth_token) {
                        return Yii::$app->formatter->nullDisplay;
                    }
                    return SecureStringHelper::generate((string) $model->uccd_auth_token, 10);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'uccd_token_expired',
                'value' => static function (UserClientChatData $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->uccd_token_expired));
                },
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'uccd_token_expired',
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
                'format' => 'raw',
            ],
            [
                'attribute' => 'uccd_active',
                'value' => static function (UserClientChatData $model) {
                    return Yii::$app->formatter->asBooleanByLabel($model->uccd_active);
                },
                'filter' => [1 => 'Yes', 0 => 'No'],
                'format' => 'raw',
            ],
            ['class' => DateTimeColumn::class, 'attribute' => 'uccd_created_dt'],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'uccd_created_user_id',
                'relation' => 'createdUser',
                'placeholder' => '',
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'uccd_updated_user_id',
                'relation' => 'updatedUser',
                'placeholder' => '',
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view}&nbsp; {update}&nbsp;',
                'buttons' => [
                    'update' => static function ($url, UserClientChatData $model) {
                        return Html::a(
                            '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>',
                            ['user-client-chat-data/update', 'id' => $model->uccd_id],
                            [
                                'title' => 'Manage UserClientChat',
                            ]
                        );
                    }
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>

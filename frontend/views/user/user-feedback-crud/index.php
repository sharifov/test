<?php

use common\components\grid\DateTimeColumn;
use common\models\Employee;
use modules\user\userFeedback\entity\UserFeedback;
use modules\user\userFeedback\entity\UserFeedbackFile;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\user\userFeedback\entity\search\UserFeedbackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Feedbacks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-feedback-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Feedback', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'uf_id',
            [
                'attribute' => 'uf_type_id',
                'value' => static function (UserFeedback $model) {
                    return Html::encode($model->getTypeName());
                },
            ],
            [
                'attribute' => 'uf_status_id',
                'value' => static function (UserFeedback $model) {
                    return Html::encode($model->getStatusName());
                },
            ],
            'uf_title',
            [
                'value' => static function (UserFeedback $model) {
                    return UserFeedbackFile::find()->andWhere(['uff_uf_id' => $model->uf_id])->count();
                },
                'label' => 'Attached files',
            ],
            [
                'attribute' => 'uf_message',
                'value' => static function (UserFeedback $model) {
                    if (!$model->uf_message) {
                        return null;
                    }
                    return '<pre><small>' . (StringHelper::truncate($model->uf_message, 400, '...', null, true)) . '</small></pre>';
                },
                'format' => 'raw'
            ],
            ['class' => DateTimeColumn::class, 'attribute' => 'uf_created_dt'],
            [
                'attribute' => 'uf_created_user_id',
                'value' => static function (UserFeedback $userFeedback) {
                    $user = Employee::findOne($userFeedback->uf_created_user_id);
                    return $user->username ?? null;
                }
            ],
//            'uf_data_json',
            [
                'class' => ActionColumn::class,
                'urlCreator' => static function ($action, UserFeedback $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'uf_id' => $model->uf_id, 'uf_created_dt' => $model->uf_created_dt]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

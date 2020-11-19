<?php

use sales\auth\Auth;
use sales\services\cleaner\form\DbCleanerParamsForm;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\search\UserSiteActivitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var DbCleanerParamsForm $modelCleaner */

$this->title = 'User Site Activities';
$this->params['breadcrumbs'][] = $this->title;
$pjaxListId = 'pjax-site-activity';
?>
<div class="user-site-activity-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php if (Auth::can('global/clean/table')): ?>
        <div class="col-md-6" style="margin-left: -10px;">
            <?php echo $this->render('../clean/_clean_table_form', [
                'modelCleaner' => $modelCleaner,
                'pjaxIdForReload' => $pjaxListId,
            ]); ?>
        </div>
    <?php endif ?>

    <?php Pjax::begin(['id' => $pjaxListId]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'usa_id',

           /*[
                'attribute' => 'usa_user_id',
                'value' => static function (\frontend\models\UserSiteActivity $model) {
                    return  ($model->usaUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->usaUser->username) : $model->usa_user_id);
                },
                'format' => 'raw',
                'filter' => \common\models\Employee::getList()
            ],*/

            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'usa_user_id',
                'relation' => 'usaUser',
                'placeholder' => 'Select User',
            ],

            'usa_request_url',
            'usa_page_url',
            'usa_ip',
            //'usa_request_type',
            [
                'attribute' => 'usa_request_type',
                'value' => static function (\frontend\models\UserSiteActivity $model) {
                    return  $model->getRequestTypeName();
                },
                //'format' => 'raw',
                'filter' => \frontend\models\UserSiteActivity::REQUEST_TYPE_LIST
            ],
            //'usa_request_get:ntext',
            //'usa_request_post:ntext',

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'usa_created_dt'
            ],

            /*[
                'attribute' => 'usa_created_dt',
                'value' => static function(\frontend\models\UserSiteActivity $model) {
                    return $model->usa_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->usa_created_dt), 'php: Y-m-d [H:i:s]') : $model->usa_created_dt;
                },
                'format' => 'raw',
            ],*/
            [
                'label' => 'Duration',
                'value' => static function (\frontend\models\UserSiteActivity $model) {
                    return Yii::$app->formatter->asRelativeTime(strtotime($model->usa_created_dt));
                },
                'format' => 'raw'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

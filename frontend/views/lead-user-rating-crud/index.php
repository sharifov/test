<?php

use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use src\model\leadUserRating\entity\LeadUserRating;

/* @var $this yii\web\View */
/* @var $searchModel src\model\leadUserRating\entity\LeadUserRatingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead User Ratings';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="lead-user-rating-index">

        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('Create Lead User Rating', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <?php Pjax::begin(['id' => 'pjax-LeadUserRating']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'lur_lead_id',
                    'value' => static function (LeadUserRating $model) {
                        return Yii::$app->formatter->asLead($model->lead, 'fa-cubes');
                    },
                    'format' => 'raw',
                ],
                [
                    'class' => UserSelect2Column::class,
                    'attribute' => 'lur_user_id',
                    'relation' => 'user',
                    'placeholder' => 'User'
                ],
                [
                    'attribute' => 'lur_rating',
                    'filter' => LeadUserRating::getRatingList(),
                ],
                [
                    'class' => \common\components\grid\DateTimeColumn::class,
                    'attribute' => 'lur_created_dt',
                    'limitEndDay' => false,
                ],
                [
                    'class' => \common\components\grid\DateTimeColumn::class,
                    'attribute' => 'lur_updated_dt',
                    'limitEndDay' => false,
                ],
                [
                    'class' => ActionColumn::class,
                    'urlCreator' => static function ($action, \src\model\leadUserRating\entity\LeadUserRating $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'lur_lead_id' => $model->lur_lead_id, 'lur_user_id' => $model->lur_user_id]);
                    }
                ],
            ],
        ]); ?>

        <?php Pjax::end(); ?>

    </div>

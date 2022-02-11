<?php

use common\components\grid\UserSelect2Column;
use src\model\leadUserData\entity\LeadUserData;
use src\model\leadUserData\entity\LeadUserDataDictionary;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel src\model\leadUserData\entity\LeadUserDataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead User Datas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-user-data-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lead User Data', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-LeadUserData']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'lud_id',
            [
                'attribute' => 'lud_type_id',
                'value' => static function (LeadUserData $model) {
                    return $model->getTypeName();
                },
                'filter' => LeadUserDataDictionary::TYPE_LIST,
                'format' => 'raw',
            ],
            [
                'attribute' => 'lud_lead_id',
                'value' => static function (LeadUserData $model) {
                    return Yii::$app->formatter->asLead($model->ludLead, 'fa-cubes');
                },
                'format' => 'raw',
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'lud_user_id',
                'relation' => 'ludUser',
                'placeholder' => 'User'
            ],
            [
                'class' =>  DateTimeColumn::class,
                'attribute' => 'lud_created_dt',
                'limitEndDay' => false,
            ],
            [
                'class' => ActionColumn::class,
                'urlCreator' => static function ($action, LeadUserData $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'lud_id' => $model->lud_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

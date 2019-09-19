<?php

use sales\entities\cases\Cases;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $dataProvider yii\data\ActiveDataProvider */

$isAgent = true;
?>

<div class="row">
    <div class="col-md-12">
        <h4>Cases</h4>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'cs_gid',
                    'value' => function (Cases $model) {
                        return Html::a($model->cs_id, ['cases/view', 'gid' => $model->cs_gid], [
                            'data-pjax' => 0,
                            'target' => '_blank'
                        ]);
                    },
                    'format' => 'raw'
                ],

                [
                    'attribute' => 'cs_project_id',
                    'value' => function (Cases $model) {
                        return $model->project ? $model->project->name : '';
                    },
                ],
                'cs_subject',
                [
                    'attribute' => 'cs_category',
                    'value' => function (Cases $model) {
                        return $model->category ? $model->category->cc_name : '';
                    },
                ],
                [
                    'attribute' => 'cs_user_id',
                    'value' => function (Cases $model) {
                        return $model->owner ? $model->owner->username : '';
                    },
                    'visible' => Yii::$app->user->identity->isSupSuper() || Yii::$app->user->identity->isExSuper() || Yii::$app->user->identity->isAdmin()
                ],
                [
                    'attribute' => 'cs_lead_id',
                    'value' => function (Cases $model) {
                        return $model->lead ? $model->lead->uid : '';
                    },
                ],
                [
                    'attribute' => 'cs_dep_id',
                    'value' => function (Cases $model) {
                        return $model->department ? $model->department->dep_name : '';
                    },
                ],
                // 'cs_created_dt',
                [
                    'attribute' => 'cs_created_dt',
                    'value' => function (Cases $model) {
                        return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->cs_created_dt));
                    },
                    'format' => 'raw'
                ],
            ],
        ]); ?>

    </div>
</div>

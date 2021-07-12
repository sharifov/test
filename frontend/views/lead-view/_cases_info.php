<?php

use common\models\Employee;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $dataProvider yii\data\ActiveDataProvider */

/** @var Employee $user */
$user = Yii::$app->user->identity;

?>

<div class="row">
    <div class="col-md-12">
        <h4>Cases</h4>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'label' => 'ID',
                    'value' => static function (Cases $model) {
                        if (Yii::$app->user->can('caseSection')) {
                            return Html::a($model->cs_id, ['cases/view', 'gid' => $model->cs_gid], [
                                'data-pjax' => 0,
                                'target' => '_blank'
                            ]);
                        }
                        return $model->cs_id;
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'cs_status',
                    'value' => static function (Cases $model) {
                        return CasesStatus::getLabel($model->cs_status);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'cs_project_id',
                    'value' => static function (Cases $model) {
                        return $model->project ? $model->project->name : '';
                    },
                ],
                'cs_subject',
                [
                    'attribute' => 'cs_category_id',
                    'value' => static function (Cases $model) {
                        return $model->category ? $model->category->cc_name : '';
                    },
                ],
                [
                    'label' => 'Owner',
                    'attribute' => 'cs_user_id',
                    'value' => static function (Cases $model) {
                        return $model->owner ? $model->owner->username : '';
                    },
                    'visible' => $user->isSupSuper() || $user->isExSuper() || $user->isAdmin()
                ],
                [
                    'attribute' => 'cs_lead_id',
                    'value' => static function (Cases $model) {
                        return $model->lead ? $model->lead->uid : '';
                    },
                ],
                [
                    'attribute' => 'cs_dep_id',
                    'value' => static function (Cases $model) {
                        return $model->department ? $model->department->dep_name : '';
                    },
                ],
                // 'cs_created_dt',
                [
                    'attribute' => 'cs_created_dt',
                    'value' => static function (Cases $model) {
                        return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->cs_created_dt));
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'cs_updated_dt',
                    'value' => static function (Cases $model) {
                        return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->cs_updated_dt));
                    },
                    'format' => 'raw'
                ],
            ],
        ]) ?>

    </div>
</div>

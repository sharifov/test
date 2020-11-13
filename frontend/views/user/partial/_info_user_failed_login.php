<?php

use yii\grid\GridView;
use common\models\Employee;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model Employee */

?>

<?php Pjax::begin() ?>
<div class="row">
    <div class="col-md-12">
        <h5>User Failed Login</h5>
        <div class="well">
            <?= GridView::widget([
                'dataProvider' => new \yii\data\ArrayDataProvider([
                    'allModels' => $model->userFailedLogin,
                    'pagination' => [
                        'pageSize' => 10
                    ]
                ]),
                'emptyTextOptions' => ['class' => 'text-center'],
                'columns' => [
                    [
                        'label' => 'IP',
                        'value' => function (\frontend\models\UserFailedLogin $model) {
                            return $model->ufl_ip ? $model->ufl_ip : '-';
                        },
                    ],

                    [
                        'label' => 'User Agent',
                        'value' => function (\frontend\models\UserFailedLogin $model) {
                            return $model->ufl_ua ? $model->ufl_ua : '-';
                        },
                    ],

                    [
                        'label' => 'Session ID',
                        'value' => function (\frontend\models\UserFailedLogin $model) {
                            return $model->ufl_session_id ? $model->ufl_session_id : '-';
                        },
                    ],

                    [
                        'label' => 'Created Date',
                        'value' => function (\frontend\models\UserFailedLogin $model) {
                            return $model->ufl_created_dt ? $model->ufl_created_dt : '-';
                        },
                    ],
                ]
            ]); ?>
        </div>
    </div>
</div>
<?php Pjax::end() ?>

<?php

use common\models\Employee;
use common\models\UserParams;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model Employee */

?>

<?php if ($model->userParams): ?>
<div class="row">
    <div class="col-md-6">
        <h5>General Info</h5>
        <div class="well">

            <?= \yii\widgets\DetailView::widget([
                'model' => $model->userParams,
                'attributes' => [
                    [
                        'label' => 'Start of work in the company',
                        'value' => static function (UserParams $model) {
                            return $model->upUser->userProfile->up_join_date;
                        }
                    ],
                    [
                        'label' => 'Experience',
                        'value' => static function (UserParams $model) {
                            return $model->upUser->userProfile->getExperienceMonth() . ' Months';
                        }
                    ],
                    [
                        'attribute' => 'up_base_amount',
                        'value' => static function (UserParams $model) {
                            return $model->up_base_amount ? '$' . number_format($model->up_base_amount, 2) : '-';
                        },
                    ],
                    [
                        'attribute' => 'up_commission_percent',
                        'value' => static function (UserParams $model) {
                            return $model->up_commission_percent ? $model->up_commission_percent . '%' : '-';
                        },

                    ],
                    //        [
                    //            'label' => 'New Commission Percent',
                    //            'value' => $userCommissionRuleValue . ' %'
                    //        ],
                    //        [
                    //            'label' => 'New Bonus Value',
                    //            'value' => '$'.$userBonusRuleValue
                    //        ],
                    'up_bonus_active:boolean',
                    'up_timezone',
                    'up_work_start_tm',
                    'up_work_minutes',
                    //'up_inbox_show_limit_leads',
                    'up_default_take_limit_leads',
                    'up_min_percent_for_take_leads'
                    /*[
                        'attribute' => 'up_updated_dt',
                        'value' => function(\common\models\UserParams $model) {
                            return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->up_updated_dt));
                        },
                        'format' => 'raw',
                    ],*/
                    //'upUpdatedUser.username'
                ],
            ]) ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($model->userProfile): ?>
        <div class="col-md-6">
            <h5>Chats Credentials Sets</h5>
            <div class="well">
                <?= \yii\widgets\DetailView::widget([
                    'model' => $model->userProfile,
                    'attributes' => [
                        /*[
                            'attribute' => 'up_base_amount',
                            'value' => function(\common\models\UserParams $model) {
                                return $model->up_base_amount ? '$'.number_format($model->up_base_amount , 2) : '-';
                            },
                        ],*/
                        'up_telegram',
                        'up_telegram_enable:boolean',
                        'up_rc_auth_token',
                        'up_rc_user_id',
                        'up_rc_user_password',
                        'up_rc_token_expired'

                    ],
                ]) ?>
            </div>
        </div>
    <?php endif; ?>

</div>
<div class="row">
    <?php if ($model->userProjectParams): ?>
        <div class="col-md-6">
            <h5>Project Params</h5>
            <div class="well">
                <?= GridView::widget([
                    'dataProvider' => new \yii\data\ArrayDataProvider([
                        'allModels' => $model->userProjectParams,
                    ]),
                    'layout' => '{items}',
                    'columns' => [
                        [
                            'label' => 'Project',
                            'value' => function (\common\models\UserProjectParams $model) {
                                return $model->uppProject->name ? $model->uppProject->name : '-';
                            },
                        ],

                        [
                            'label' => 'Department',
                            'value' => function (\common\models\UserProjectParams $model) {
                                return $model->uppDep->dep_name ? $model->uppDep->dep_name : '-';
                            },
                        ],

                        [
                            'label' => 'Email List',
                            'value' => function (\common\models\UserProjectParams $model) {
                                return $model->upp_email ? $model->upp_email : '-';
                            },
                        ],

                        [
                            'label' => 'Phone List',
                            'value' => function (\common\models\UserProjectParams $model) {
                                return $model->upp_tw_phone_number ? $model->upp_tw_phone_number : '-';
                            },
                        ],

                        [
                            'label' => 'Allow General Line',
                            'value' => function (\common\models\UserProjectParams $model) {
                                return $model->upp_allow_general_line == 1 ? '<i class="fa fa-check-square-o"></i>': '<i class="fa fa-close"></i>';
                            },
                            'format' => 'raw'
                        ],
                    ]
                ]); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($model->userProductType): ?>
        <div class="col-md-6">
            <h5>Product Type</h5>
            <div class="well">
                <?= GridView::widget([
                    'dataProvider' => new \yii\data\ArrayDataProvider([
                        'allModels' => $model->userProductType,
                    ]),
                    'layout' => '{items}',
                    'columns' => [
                        [
                            'label' => 'Product Type',
                            'value' => function (\common\models\UserProductType $model) {
                                return $model->productType->pt_name ? $model->productType->pt_name : '-';
                            },
                        ],

                        [
                            'label' => 'Commission Percent',
                            'value' => function (\common\models\UserProductType $model) {
                                return $model->upt_commission_percent ? $model->upt_commission_percent : '-';
                            },
                        ],

                        [
                            'label' => 'Product Enabled',
                            'value' => function (\common\models\UserProductType $model) {
                                return $model->upt_product_enabled == 1 ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
                            },
                            'format' => 'raw'
                        ],
                    ]
                ]); ?>
            </div>
        </div>
    <?php endif; ?>
</div>



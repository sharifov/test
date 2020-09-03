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
<div class="col-md-6">
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
                    return $model->up_base_amount ? '$'.number_format($model->up_base_amount , 2) : '-';
                },
            ],
            [
                'attribute' => 'up_commission_percent',
                'value' => static function (UserParams $model) {
                    return $model->up_commission_percent ? $model->up_commission_percent. '%' : '-';
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
<?php endif; ?>
<div class="col-md-6">
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

<?php

use yii\bootstrap4\Alert;
use yii\widgets\DetailView;

/** @var $visitorLog \common\models\VisitorLog|null */

?>

<?php if ($visitorLog): ?>
    <?= DetailView::widget([
        'model' => $visitorLog,
        'attributes' => [
            'vl_project_id:projectName',
            'vl_ga_client_id',
            'vl_ga_user_id',
            'vl_customer_id',
            'lead:lead',
            'vl_gclid',
            'vl_dclid',
            'vl_utm_source',
            'vl_utm_medium',
            'vl_utm_campaign',
            'vl_utm_term',
            'vl_utm_content',
            'vl_referral_url',
            'vl_user_agent',
            'vl_ip_address'
        ]
    ]) ?>
<?php else: ?>
    <?= Alert::widget([
        'body' => 'Visitor log data not found.',
        'options' => [
            'class' => 'alert alert-warning'
        ]
    ]) ?>
<?php endif; ?>

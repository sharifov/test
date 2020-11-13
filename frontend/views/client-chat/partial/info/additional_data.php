<?php

use yii\bootstrap4\Alert;
use yii\widgets\DetailView;

/** @var $clientChatVisitorData */

?>

<?php if ($clientChatVisitorData): ?>
    <?= DetailView::widget([
        'model' => $clientChatVisitorData,
        'attributes' => [
            'cvd_country',
            'cvd_region',
            'cvd_city',
            'cvd_latitude',
            'cvd_longitude',
            'cvd_url',
            'cvd_referrer',
            'cvd_timezone',
            'cvd_local_time'
        ]
    ]) ?>
<?php else: ?>
    <?= Alert::widget([
        'body' => 'Client Chat Data not found.',
        'options' => [
            'class' => 'alert alert-warning'
        ]
    ]) ?>
<?php endif; ?>

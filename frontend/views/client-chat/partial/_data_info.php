<?php
/**
 * @var $this \yii\web\View
 * @var $clientChat \sales\model\clientChat\entity\ClientChat|null
 * @var $visitorLog \common\models\VisitorLog|null
 */

use yii\bootstrap4\Alert;
use yii\widgets\DetailView;

?>

<div class="row">
	<div class="col-md-6">
        <?php if ($clientChat && $clientChat->cchData): ?>
            <h4>Client chat additional data</h4>
            <?= DetailView::widget([
                'model' => $clientChat->cchData,
                'attributes' => [
                    'ccd_title',
                    'ccd_country',
                    'ccd_region',
                    'ccd_city',
                    'ccd_latitude',
                    'ccd_longitude',
                    'ccd_url',
                    'ccd_referrer',
                    'ccd_timezone',
                    'ccd_local_time'
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
	</div>
    <div class="col-md-6">
        <?php if ($visitorLog): ?>
            <h4>Visitor log</h4>
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
    </div>
</div>

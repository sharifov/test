<?php
/**
 * @var $this \yii\web\View
 * @var $clientChat \sales\model\clientChat\entity\ClientChat|null
 * @var $visitorLog \common\models\VisitorLog|null
 */

use yii\bootstrap4\Alert;

?>

<div class="row">
	<div class="col-md-6">
        <?php if ($clientChat && $clientChat->cchData): ?>
            <h4>Client chat additional data</h4>
            <?= \yii\widgets\DetailView::widget([
                'model' => $clientChat->cchData,
                'attributes' => [
                    'cch_title',
                    'cch_description',
                    'cch_case_id',
                    'cch_lead_id',
                    'cch_status_id',
                    'cch_note',
                    'cch_ua'
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
			<?= \yii\widgets\DetailView::widget([
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

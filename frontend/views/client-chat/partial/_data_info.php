<?php
/**
 * @var $this \yii\web\View
 * @var $clientChat \sales\model\clientChat\entity\ClientChat|null
 * @var $visitorLog \common\models\VisitorLog|null
 * @var $clientChatVisitorData ClientChatVisitorData|null
 * @var yii\data\ActiveDataProvider $dataProviderRequest
 * @var Client $client
 * @var yii\data\ActiveDataProvider|null $leadDataProvider
 * @var yii\data\ActiveDataProvider|null $casesDataProvider
 */

use common\models\Client;
use common\models\Lead;
use sales\entities\cases\Cases;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatVisitorData\entity\ClientChatVisitorData;
use yii\bootstrap4\Alert;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

?>

<div class="row">
	<div class="col-md-6">
        <?php if ($clientChatVisitorData): ?>
            <h4>Client chat additional data</h4>
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

        <?php if ($dataProviderRequest->getTotalCount()) :?>
            <h4>Browsing history</h4>
            <?php Pjax::begin(['id' => 'pjax-browsing-history', 'timeout' => 5000, 'enablePushState' => false]); ?>
            <?php echo GridView::widget([
                'dataProvider' => $dataProviderRequest,
                'columns' => [
                    [
                        'attribute' => 'ccr_created_dt',
                        'value' => static function(ClientChatRequest $model) {
                            return $model->ccr_created_dt ?
                                Yii::$app->formatter->asDatetime(strtotime($model->ccr_created_dt)) : '-';
                        },
                        'format' => 'raw',
                        'header' => 'Created',
                    ],
                    [
                        'label' => 'Url',
                        'value' => static function(ClientChatRequest $model) {
                            if ($pageUrl = $model->getPageUrl()) {
                                return Yii::$app->formatter->asUrl($pageUrl);
                            }
                            return Yii::$app->formatter->nullDisplay;
                        },
                        'format' => 'raw',
                        'header' => 'Url',
                    ],
                ],
            ]) ?>
            <?php Pjax::end() ?>
        <?php endif ?>

        <?php if ($client) :?>
            <?php if ($leadDataProvider->getTotalCount()) :?>
                <h4>Leads from client</h4>
                <?php Pjax::begin(['id' => 'pjax-client-leads', 'timeout' => 5000, 'enablePushState' => false]); ?>
                <?php echo GridView::widget([
                    'dataProvider' => $leadDataProvider,
                    'columns' => [
                        [
                            'attribute' => 'id',
                            'value' => static function(Lead $model) {
                                return Yii::$app->formatter->asLead($model, 'fa-cubes');
                            },
                            'format' => 'raw',
                            'header' => 'Lead',
                        ],
                        [
                            'attribute' => 'created',
                            'value' => static function(Lead $model) {
                                return Yii::$app->formatter->asByUserDateTime($model->created);
                            },
                            'format' => 'raw',
                            'header' => 'Created',
                        ],
                    ],
                ]) ?>
                <?php Pjax::end() ?>
            <?php endif ?>

            <?php if ($casesDataProvider->getTotalCount()) :?>
                <h4>Cases from client</h4>
                <?php Pjax::begin(['id' => 'pjax-client-cases', 'timeout' => 5000, 'enablePushState' => false]); ?>
                <?php echo GridView::widget([
                    'dataProvider' => $casesDataProvider,
                    'columns' => [
                        [
                            'attribute' => 'cs_id',
                            'value' => static function(Cases $model) {
                                return Yii::$app->formatter->asCase($model, 'fa-cube');
                            },
                            'format' => 'raw',
                            'header' => 'Case',
                        ],
                        [
                            'attribute' => 'cs_created_dt',
                            'value' => static function(Cases $model) {
                                return Yii::$app->formatter->asByUserDateTime($model->cs_created_dt);
                            },
                            'format' => 'raw',
                            'header' => 'Created',
                        ],
                    ],
                ]) ?>
                <?php Pjax::end() ?>
            <?php endif ?>

        <?php endif  ?>

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

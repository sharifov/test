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
use sales\model\clientChatVisitorData\entity\ClientChatVisitorData;

$tabs[] = [
    'id' => 'additional-data',
    'name' => 'Additional data',
    'content' => $this->render('info/additional_data', ['clientChatVisitorData' => $clientChatVisitorData])
];

if ($dataProviderRequest->getTotalCount()) {
    $tabs[] = [
        'id' => 'browsing-history',
        'name' => 'Browsing history',
        'content' => $this->render('info/browsing_history', ['dataProviderRequest' => $dataProviderRequest])
    ];
}

if ($client) {
    if ($leadDataProvider && $leadDataProvider->getTotalCount()) {
        $tabs[] = [
            'id' => 'client-leads',
            'name' => 'Leads from client',
            'content' => $this->render('info/client_leads', ['leadDataProvider' => $leadDataProvider])
        ];
    }
    if ($casesDataProvider && $casesDataProvider->getTotalCount()) {
        $tabs[] = [
            'id' => 'client-cases',
            'name' => 'Cases from client',
            'content' => $this->render('info/client_cases', ['casesDataProvider' => $casesDataProvider])
        ];
    }
}

$tabs[] = [
    'id' => 'visitor-log',
    'name' => 'Visitor log',
    'content' => $this->render('info/visitor_log', ['visitorLog' => $visitorLog])
];
?>

<div class="row">
	<div class="col-md-12">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <?php foreach ($tabs as $key => $tab): ?>
                    <?php if ($key === 0): ?>
                        <a class="nav-item nav-link active" id="nav-<?= $tab['id']?>-tab" data-toggle="tab" href="#nav-<?= $tab['id']?>" role="tab" aria-controls="nav-<?= $tab['id']?>" aria-selected="true"><?= $tab['name']?></a>
                    <?php else: ?>
                        <a class="nav-item nav-link" id="nav-<?= $tab['id']?>-tab" data-toggle="tab" href="#nav-<?= $tab['id']?>" role="tab" aria-controls="nav-<?= $tab['id']?>" aria-selected="false"><?= $tab['name']?></a>
                    <?php endif;?>
                <?php endforeach; ?>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <br>
            <?php foreach ($tabs as $key => $tab): ?>
                <?php if ($key === 0): ?>
                    <div class="tab-pane fade show active" id="nav-<?= $tab['id']?>" role="tabpanel" aria-labelledby="nav-<?= $tab['id']?>-tab"><?= $tab['content']?></div>
                <?php else: ?>
                    <div class="tab-pane fade" id="nav-<?= $tab['id']?>" role="tabpanel" aria-labelledby="nav-<?= $tab['id']?>-tab"><?= $tab['content']?></div>
                <?php endif;?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

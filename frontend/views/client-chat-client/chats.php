<?php

/** @var Client $client */
/** @var ActiveDataProvider $chatsDataProvider */

use common\models\Client;
use yii\data\ActiveDataProvider;
use yii\widgets\Pjax;

?>

<?= $this->render('client_block', ['client' => $client]) ?>

<?php Pjax::begin(['id' => 'client_chat_list_chats', 'timeout' => 2000, 'enablePushState' => false]); ?>
    <?= $this->render('chats_block', ['chatsDataProvider' => $chatsDataProvider]) ?>
<?php
Pjax::end();

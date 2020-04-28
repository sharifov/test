<?php

use common\models\Client;
use common\models\ClientPhone;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var ClientPhone[] $clientPhones
 * @var Client $client
 */
?>

    <?=Html::a('<i class="fas fa-plus-circle success"></i> Add Phone', '#', [
        'id' => 'client-new-phone-button',
        'data-modal_id' => 'sm',
        'title' => 'Add Phone',
        'data-content-url' => Url::to(['contacts/ajax-add-contact-phone-modal-content', 'client_id' => $client->id]),
        'class' => 'showModalButton'
    ])?>
    <br />

<table class="table table-condensed table-bordered" style="margin: 15px 0 0 0 ;" id="contact-phones">
    <?php foreach ($clientPhones as $key => $phone): ?>
        <?php echo $this->render('_phone_row', [
                'phone' => $phone,
            ]);
        ?>
    <?php endforeach; ?>
</table>

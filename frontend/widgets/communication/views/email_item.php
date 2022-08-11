<?php

/**
 * @var $data array
 */
?>
<div class="chat__message chat__message--<?= $data['class']?> chat__message--email">
    <div class="chat__icn"><i class="fa fa-envelope-o"></i></div><?= $data['icon']?>
     <div class="chat__message-heading">
       <div class="chat__sender">
          Email from <?= $data['createdUser']?> (<?= $data['fromName']?> <<strong><?= $data['from']?> )</strong>
          to (<?= $data['toName']?> <<strong class="<?= $data['unsubscribed'] ? 'text-line-through' : ''?>"><?= $data['to']?></strong>>)
        </div>
        <div class="chat__date"><?= $data['createdDate']?> <?= $data['language']?></div>
    </div>
    <div class="card-body">
        <h5 class="chat__subtitle" style="word-break: break-all;"><?= $data['shortSubject']?></h5>
        <div><?= $data['body']?></div><?= $data['footer'] ?? ''?>
    </div>
</div>
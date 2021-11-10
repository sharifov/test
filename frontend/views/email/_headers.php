<?php

use yii\helpers\Html;
use sales\helpers\email\MaskEmailHelper;

?>
<style>
    @media print {
        body{  background-color:#FFFFFF; background-image:none; color:#000000 }
        #ad { display:none;}
        #leftbar { display:none;}
        #contentarea{  width:100%;}
    }
</style>

<b>Subject: </b><?=Html::encode($mail->e_email_subject);?><br>
<?php echo $mail->isInbox()
                ? '<b>Email from:</b> (' . Html::encode($mail->e_email_from_name) . ' &lt;' . Html::encode(MaskEmailHelper::masking($mail->e_email_from)) . '&gt;)<br>'
                : '<b>Email from:</b> ' . ($mail->eCreatedUser ? Html::encode($mail->eCreatedUser->username) : '-') . ', (' . Html::encode($mail->e_email_from_name) . ' &lt;' . Html::encode($mail->e_email_from) . '&gt;)<br>';
echo $mail->isInbox()
? '<b>Email To:</b> ' . Html::encode($mail->e_email_to_name) . ' &lt;' . Html::encode($mail->e_email_to) . '&gt;<br>'
: '<b>Email To:</b> ' . Html::encode($mail->e_email_to_name) . ' &lt;' . Html::encode(MaskEmailHelper::masking($mail->e_email_to)) . '&gt;' . '<br>';
echo '<b>Date:</b> ' . Yii::$app->formatter->asDatetime(strtotime($mail->e_created_dt));
?>
<br>
<br>

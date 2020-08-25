<?php


namespace sales\model\clientChat;

use Yii;

/**
 * Class ClientChatTranslate
 * @package sales\model\clientChat
 */
class ClientChatTranslate
{
    /**
     * @param null $language
     * @return array
     */
    public static function getTranslates($language = null): array
    {
        $data['i18n'] = [
            'dialogs_history'           => Yii::t('clientChat', 'Conversation history', [], $language),
            'new_message'               => Yii::t('clientChat', 'New message', [], $language),
            'enter_message'             => Yii::t('clientChat', 'Type your message and press Enter', [], $language),
            'dialogs_zerodata'          => Yii::t('clientChat', 'There are no conversations yet. We\'ve never talked before', [], $language),
            'enter_email'               => Yii::t('clientChat', 'Enter your email', [], $language),
            'enter_phone'               => Yii::t('clientChat', 'Enter phone number', [], $language),
            'leave_email'               => Yii::t('clientChat', 'You can leave your email and we\'ll continue this conversation through email:', [], $language),
            'page_title_new_message'    => Yii::t('clientChat', 'New message', [], $language),
            'privacy_policy'            => Yii::t('clientChat', 'Privacy Policy', [], $language),
            'close'                     => Yii::t('clientChat', 'Close', [], $language),
            'bumperText'                => Yii::t('clientChat','We use cookies to offer you a better browsing experience, analyze   site traffic and personalize content. By using this site or clicking I agree, you consent to our use of cookies. You can read our policy 
    here.', [], $language),
            'bumperButton'              => Yii::t('clientChat', 'I agree', [], $language),
            'confirmSubscription'       => Yii::t('clientChat', 'I agree with terms of service', [], $language),
            'appeal'                    => Yii::t('clientChat', 'You', [], $language),
            'vote_comment'              => Yii::t('clientChat', 'What happened?', [], $language),
            'vote_text'                 => Yii::t('clientChat', 'Please rate the operator', [], $language),
            'vote_thanks'               => Yii::t('clientChat', 'Thank you for rating!', [], $language),
        ];

        $data['settings'] = [
            'messenger_collapsed_text'      => Yii::t('clientChat-settings', 'Ask me...', [], $language),
            'messenger_offline_message'     => Yii::t('clientChat-settings', 'Тhere are no operators ready to answer now, but you  can leave your question, and we will help you during business hours.', [], $language),
            'messenger_welcome_message'     => Yii::t('clientChat-settings', 'Ask your question and we will respond. Write to us!', [], $language),
        ];
        return $data;
    }

}
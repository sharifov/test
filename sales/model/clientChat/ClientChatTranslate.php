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

        $data['connection_lost'] = [
            'title'     => Yii::t('clientChat', 'Connection Lost', [], $language),
            'subtitle'  => Yii::t('clientChat', 'Trying to reconnect', [], $language),
        ];

        $data['waiting_for_response']   = Yii::t('clientChat', 'Waiting for response', [], $language);
        $data['waiting_for_agent']      = Yii::t('clientChat', 'Waiting for an agent', [], $language);
        $data['video_reply']            = Yii::t('clientChat', 'Video message', [], $language);
        $data['audio_reply']            = Yii::t('clientChat', 'Audio message', [], $language);
        $data['image_reply']            = Yii::t('clientChat', 'Image message', [], $language);
        $data['new_message']            = Yii::t('clientChat', 'New message', [], $language);
        $data['agent']                  = Yii::t('clientChat', 'Agent', [], $language);
        $data['textarea_placeholder']   = Yii::t('clientChat', 'Type a message...', [], $language);

        $data['registration'] = [
            'title'                     => Yii::t('clientChat_registration', 'Welcome', [], $language),
            'subtitle'                  => Yii::t('clientChat_registration', 'Be sure to leave a message', [], $language),
            'name'                      => Yii::t('clientChat_registration', 'Name', [], $language),
            'name_placeholder'          => Yii::t('clientChat_registration', 'Your name', [], $language),
            'email'                     => Yii::t('clientChat_registration', 'Email', [], $language),
            'email_placeholder'         => Yii::t('clientChat_registration', 'Your email', [], $language),
            'department'                => Yii::t('clientChat_registration', 'Department', [], $language),
            'department_placeholder'    => Yii::t('clientChat_registration', 'Choose a department', [], $language),
            'start_chat'                => Yii::t('clientChat_registration', 'Start chat', [], $language),
        ];

        $data['conversations'] = [
            'no_conversations'              => Yii::t('clientChat_conversations', 'No conversations yet', [], $language),
            'no_archived_conversations'     => Yii::t('clientChat_conversations', 'No archived conversations yet', [], $language),
            'history'                       => Yii::t('clientChat_conversations', 'Conversation history', [], $language),
            'active'                        => Yii::t('clientChat_conversations', 'Active', [], $language),
            'archived'                      => Yii::t('clientChat_conversations', 'Archived Chats', [], $language),
            'start_new'                     => Yii::t('clientChat_conversations', 'New Chat', [], $language),
        ];

        $data['file_upload'] = [
            'file_too_big'          => Yii::t('clientChat_file', 'This file is too big. Max file size is {{size}}', [], $language),
            'file_too_big_alt'      => Yii::t('clientChat_file', 'No archived conversations yetThis file is too large', [], $language),
            'generic_error'         => Yii::t('clientChat_file', 'Failed to upload, please try again', [], $language),
            'not_allowed'           => Yii::t('clientChat_file', 'This file type is not supported', [], $language),
            'drop_file'             => Yii::t('clientChat_file', 'Drop file here to upload it', [], $language),
            'upload_progress'       => Yii::t('clientChat_file', 'Uploading file...', [], $language),
        ];

        $data['department'] = [
            'sales'         => Yii::t('clientChat', 'Sales', [], $language),
            'support'       => Yii::t('clientChat', 'Support', [], $language),
            'exchange'      => Yii::t('clientChat', 'Exchange', [], $language),
        ];

        return $data;
    }

}
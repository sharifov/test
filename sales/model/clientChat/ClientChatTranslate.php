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
        $data['transfer_department']   = Yii::t('clientChat', 'The chat was transferred to the department {{name}}', [], $language);
        $data['videocall_started']   = Yii::t('clientChat', 'Video call started', [], $language);
        $data['videocall_ended']   = Yii::t('clientChat', 'Video call ended', [], $language);


        $data['registration'] = [
            'title'                     => Yii::t('clientChat_registration', 'We are ready to help you', [], $language),
            'subtitle'                  => Yii::t('clientChat_registration', 'Please leave your contact details below to start a conversation.', [], $language),
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
            'conversation_ended'            => Yii::t('clientChat_conversations', 'Conversation has ended', [], $language),
        ];

        $data['file_upload'] = [
            'file_too_big'          => Yii::t('clientChat_file', 'This file is too big. Max file size is {{size}}', [], $language),
            'file_too_big_alt'      => Yii::t('clientChat_file', 'This file is too big.', [], $language),
            'generic_error'         => Yii::t('clientChat_file', 'Failed to upload, please try again', [], $language),
            'not_allowed'           => Yii::t('clientChat_file', 'This file type is not supported', [], $language),
            'drop_file'             => Yii::t('clientChat_file', 'Drop file here to upload it', [], $language),
            'upload_progress'       => Yii::t('clientChat_file', 'Uploading file...', [], $language),
        ];

        /*$data['department'] = [
            'sales'         => Yii::t('clientChat', 'Sales', [], $language),
            'support'       => Yii::t('clientChat', 'Support', [], $language),
            'exchange'      => Yii::t('clientChat', 'Exchange', [], $language),
        ];*/

        $data['feedback'] = [
            'thanks_for_feedback'       => Yii::t('clientChat_feedback', 'Thanks for your feedback!', [], $language),
            'leave_feedback'            => Yii::t('clientChat_feedback', 'Leave feedback', [], $language),
            'rate_conversation'         => Yii::t('clientChat_feedback', 'Rate Your Conversation', [], $language),
            'submit'                    => Yii::t('clientChat_feedback', 'Submit feedback', [], $language),
            'submit_error'              => Yii::t('clientChat_feedback', 'Failed to submit', [], $language),
            'comment'                   => Yii::t('clientChat_feedback', 'Your opinion is important for us', [], $language),
            'rate'                      => Yii::t('clientChat_feedback', 'You rated this dialogue as {{rate}}', [], $language),
            'no_rating'                 => Yii::t('clientChat_feedback', 'No score', [], $language),
            'rating_1'                  => Yii::t('clientChat_feedback', 'Terrible', [], $language),
            'rating_2'                  => Yii::t('clientChat_feedback', 'Bad', [], $language),
            'rating_3'                  => Yii::t('clientChat_feedback', 'Acceptable', [], $language),
            'rating_4'                  => Yii::t('clientChat_feedback', 'Good', [], $language),
            'rating_5'                  => Yii::t('clientChat_feedback', 'Awesome', [], $language),
        ];



        return $data;
    }

}
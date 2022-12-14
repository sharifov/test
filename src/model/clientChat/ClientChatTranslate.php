<?php

namespace src\model\clientChat;

use Yii;

/**
 * Class ClientChatTranslate
 * @package src\model\clientChat
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

        $data['waiting_for_response']       = Yii::t('clientChat', 'Waiting for response', [], $language);
        $data['waiting_for_agent']          = Yii::t('clientChat', 'Waiting for an agent', [], $language);

        $data['generic_reply']              = Yii::t('clientChat', 'Message', [], $language);
        $data['video_reply']                = Yii::t('clientChat', 'Video message', [], $language);
        $data['audio_reply']                = Yii::t('clientChat', 'Audio message', [], $language);
        $data['image_reply']                = Yii::t('clientChat', 'Image message', [], $language);
        $data['attachment_reply']           = Yii::t('clientChat', 'Attachment message', [], $language);

        $data['new_message']                = Yii::t('clientChat', 'New message', [], $language);
        $data['new_video']                  = Yii::t('clientChat', 'New video', [], $language);
        $data['new_audio']                  = Yii::t('clientChat', 'New audio', [], $language);
        $data['new_image']                  = Yii::t('clientChat', 'New image', [], $language);
        $data['new_attachment']             = Yii::t('clientChat', 'New attachment', [], $language);
        $data['new_offer']                  = Yii::t('clientChat', 'New offer', [], $language);

        $data['agent']                      = Yii::t('clientChat', 'Agent', [], $language);
        $data['textarea_placeholder']       = Yii::t('clientChat', 'Type a message...', [], $language);
        $data['textarea_rec_placeholder']   = Yii::t('clientChat', 'Recording...', [], $language);
        $data['is_typing']                  = Yii::t('clientChat', '{{ agentName }} is typing', [], $language);
        $data['transfer_department']        = Yii::t('clientChat', 'The chat was transferred to the department {{name}}', [], $language);
        $data['transfer_agent']             = Yii::t('clientChat', 'The chat was transferred to the agent {{name}}', [], $language);
        $data['all_offers']                 = Yii::t('clientChat', 'All offers', [], $language);
        $data['trip_details']               = Yii::t('clientChat', 'Trip details', [], $language);
        $data['conversation_ended']         = Yii::t('clientChat', 'Conversation has ended', [], $language);
        $data['videocall_started']          = Yii::t('clientChat', 'Video call started', [], $language);
        $data['videocall_ended']            = Yii::t('clientChat', 'Video call ended', [], $language);

        $data['departmentTitle']            = Yii::t('clientChat', 'New chat', [], $language);
        $data['departmentTitleTaken']       = Yii::t('clientChat', 'taken by {{agentName}}', [], $language);
        $data['departmentSubtitle']         = Yii::t('clientChat', 'subtitle {{name}}', [], $language);
        $data['departmentSubtitleTaken']    = Yii::t('clientChat', 'subtitle taken {{name}}', [], $language);

        $data['view_offer_details']         = Yii::t('clientChat', 'View details', [], $language);
        $data['draft']                      = Yii::t('clientChat', 'Draft', [], $language);

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
            'delete'                        => Yii::t('clientChat_conversations', 'Delete', [], $language),
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

        $dataEmojiPicker['search']   = Yii::t('clientChat_emojipicker', 'Search', [], $language);
        $dataEmojiPicker['clear']   = Yii::t('clientChat_emojipicker', 'Clear', [], $language);
        $dataEmojiPicker['notfound']   = Yii::t('clientChat_emojipicker', 'No Emoji Found', [], $language);

        $dataEmojiPicker['skintext']   = Yii::t('clientChat_emojipicker', 'Choose your default skin tone', [], $language);

        $dataEmojiPicker['categories'] = [
            'search'       => Yii::t('clientChat_emojipicker', 'Search Results', [], $language),
            'recent'       => Yii::t('clientChat_emojipicker', 'Frequently Used', [], $language),
            'smileys'       => Yii::t('clientChat_emojipicker', 'Smileys & Emotion', [], $language),
            'people'       => Yii::t('clientChat_emojipicker', 'People & Body', [], $language),
            'nature'       => Yii::t('clientChat_emojipicker', 'Animals & Nature', [], $language),
            'foods'       => Yii::t('clientChat_emojipicker', 'Food & Drink', [], $language),
            'activity'       => Yii::t('clientChat_emojipicker', 'Activity', [], $language),
            'places'       => Yii::t('clientChat_emojipicker', 'Travel & Places', [], $language),
            'objects'       => Yii::t('clientChat_emojipicker', 'Objects', [], $language),
            'symbols'       => Yii::t('clientChat_emojipicker', 'Symbols', [], $language),
            'flags'       => Yii::t('clientChat_emojipicker', 'Flags', [], $language),
            'custom'       => Yii::t('clientChat_emojipicker', 'Custom', [], $language),
        ];

        $dataEmojiPicker['categorieslabel']   = Yii::t('clientChat_emojipicker', 'Emoji categories', [], $language);
        $dataEmojiPicker['skintones'] = [
            1       => Yii::t('clientChat_emojipicker', 'Default Skin Tone', [], $language),
            2       => Yii::t('clientChat_emojipicker', 'Light Skin Tone', [], $language),
            3       => Yii::t('clientChat_emojipicker', 'Medium-Light Skin Tone', [], $language),
            4       => Yii::t('clientChat_emojipicker', 'Medium Skin Tone', [], $language),
            5       => Yii::t('clientChat_emojipicker', 'Medium-Dark Skin Tone', [], $language),
            6       => Yii::t('clientChat_emojipicker', 'Dark Skin Tone', [], $language),
        ];

        $data['emojipicker'] = $dataEmojiPicker;

        $data['form'] = [
            'text_field' => Yii::t('clientChat_form', 'Text Field', [], $language),
            'option_1' => Yii::t('clientChat_form', 'Option 1', [], $language),
            'option_2' => Yii::t('clientChat_form', 'Option 2', [], $language),
            'option_3' => Yii::t('clientChat_form', 'Option 3', [], $language),
        ];

        return $data;
    }
}

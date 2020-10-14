<?php

namespace sales\model\clientChatChannel\entity;

class ClientChatChannelDefaultSettings
{
    private static array $settings = [
        'enabled' => true,
        'default' => false,
        'maxConversations' => 2,
        'maxMessageLength' => 500,
        'showOnRegister' => true,
        'max_dialog_count' => 1,
        'count_of_active_chats' => 1,
        'feedback_rating_enabled' => true,
        'feedback_message_enabled' => true,
        'history_email_enabled' => true,
        'history_download_enabled' => true,
        'allow_transfer_to_channel_with_active_chat' => false,
        'canStartFromArchived' => true,
        'audioRecording' => [
            'enabled' => true,
            'maxLength' => 30
        ],
        'feedback' => [
            'enabled' => true,
            'ratingEnabled' => true,
            'commentEnabled' => true,
            'maxCommentLength' => 500,
            'showOnRoomClose' => true,
            'userCanInitiate' => true,
            'userCanInitiateAfterMessages' => 2,
            'multiple' => false,
        ],
        'fileUpload' => [
            'enabled' => true,
            'accept' => [
                0 => 'image/*',
                1 => 'audio/*',
                2 => 'application/pdf',
                3 => 'text/*',
            ],
            'maxFileSize' => 10000000,
        ],
        'registration' => [
            'enabled' => true,
            'formFields' => [
                'name' => [
                    'enabled' => true,
                    'required' => true,
                    'maxLength' => 40,
                    'minLength' => 3,
                ],
                'email' => [
                    'enabled' => false,
                    'required' => true,
                    'maxLength' => 40,
                    'minLength' => 3,
                ]
            ]
        ]
    ];

    public static function getAll(): array
    {
        return self::$settings;
    }
}

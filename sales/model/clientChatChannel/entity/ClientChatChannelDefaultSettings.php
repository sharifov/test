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
            'formFieldsEnabled' => true,
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
        ],
        'system' => [
            'allowTransferChannelActiveChat' => false,
            'displayOrder' => 0
        ]
    ];

    public static function getAll(): array
    {
        return self::$settings;
    }

    public static function isAllowTransferChannelActiveChat(): bool
    {
        return self::$settings['system']['allowTransferChannelActiveChat'] ?? false;
    }
}

<?php

namespace sales\model\clientChat\entity\projectConfig;

class ClientChatProjectConfigDefaultParams
{
    private static $params = [
        "endpoint" => "chatbot.travel-dev.com",
        "notificationSound" => "https://cdn.travelinsides.com/npmstatic/assets/chime.mp3",
        "registrationEnabled" => false,
        "autoMessage" => [
            "enabled" => false,
            "repeatDelay" => 3600,
            "botName" => "Agent",
            "botAvatar" => "",
            "denyPath" => ["*checkout/quote*"],
            "messageTypes" => [
                "withoutFlightParams" => [
                    "messages" => [
                        [
                            "message" => "Hi! Would you like to check for a discount?",
                            "delay" => 4,
                            "showTyping" => false
                        ]
                    ]
                ],
                "withFlightParams" => [
                    "messages" => [
                        [
                            "message" => "Hi!",
                            "delay" => 4,
                            "showTyping" => false
                        ],
                        [
                            "message" => "We have private deals for {{originCityParam}} to {{destinationCityParam}}!",
                            "delay" => 4,
                            "showTyping" => true
                        ],
                        [
                            "message" => "Would you like to check for a discount?",
                            "delay" => 4,
                            "showTyping" => true
                        ]
                    ]
                ]
            ]
        ],
        "autoMessageTranslates" => [
            "ru-RU" => "",
            "en-US" => ""
        ]
    ];

    private static $theme = [
        "theme" => "linear-gradient(270deg, #0AAB99 0%, #1E71D1 100%)",
        "primary" => "#0C89DF",
        "primaryDark" => "#0066BA",
        "accent" => "#0C89DF",
        "accentDark" => "#0066BA"
    ];

    private static $registration = [
        "enabled" => true,
        "departments" => [
            "Sales",
            "Support"
        ],
        "registrationTitle" => "Registration title if registration is enabled",
        "registrationSubtitle" => "Registration subtitle if it is enabled",
        "formFields" => [
            "name" => [
                "enabled" => true,
                "required" => true,
                "maxLength" => 40,
                "minLength" => 3
            ],
            "email" => [
                "enabled" => true,
                "required" => true,
                "maxLength" => 40,
                "minLength" => 3
            ],
            "department" => [
                "enabled" => true,
                "required" => true
            ]
        ]
    ];

    private static $settings = [
        "fileUpload" => true,
        "maxMessageLength" => 500
    ];

    public static function getParams(): array
    {
        return self::$params;
    }

    public static function getTheme(): array
    {
        return self::$theme;
    }

    public static function getRegistration(): array
    {
        return self::$registration;
    }

    public static function getSettings(): array
    {
        return self::$settings;
    }
}

<?php

namespace sales\model\project\entity\params;

/**
 * Class CallParams
 *
 * @property $url_say_play_hold
 * @property $url_music_play_hold
 * @property $play_direct_message
 * @property $play_redirect_message
 * @property $say_direct_message
 * @property $say_redirect_message
 * @property $call_recording_disabled
 */
class CallParams
{
    public $url_say_play_hold;
    public $url_music_play_hold;
    public $play_direct_message;
    public $play_redirect_message;
    public $say_direct_message;
    public $say_redirect_message;
    public $call_recording_disabled;

    public function __construct(array $params)
    {
        $this->url_say_play_hold = $params['url_say_play_hold'] ?? self::default()['url_say_play_hold'];
        $this->url_music_play_hold = $params['url_music_play_hold'] ?? self::default()['url_music_play_hold'];
        $this->play_direct_message = $params['play_direct_message'] ?? self::default()['play_direct_message'];
        $this->play_redirect_message = $params['play_redirect_message'] ?? self::default()['play_redirect_message'];
        $this->say_direct_message = $params['say_direct_message'] ?? self::default()['say_direct_message'];
        $this->say_redirect_message = $params['say_redirect_message'] ?? self::default()['say_redirect_message'];
        $this->call_recording_disabled = (bool)($params['call_recording_disabled'] ?? self::default()['call_recording_disabled']);
    }

    public function isCallRecordingDisabled(): bool
    {
        return $this->call_recording_disabled === true;
    }

    public static function default(): array
    {
        return [
            'url_say_play_hold' => '',
            'url_music_play_hold' => '',
            'play_direct_message' => '',
            'play_redirect_message' => '',
            'say_direct_message' => '',
            'say_redirect_message' => '',
            'call_recording_disabled' => false,
        ];
    }
}

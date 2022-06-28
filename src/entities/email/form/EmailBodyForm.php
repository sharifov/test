<?php

namespace src\entities\email\form;

use yii\base\Model;
use src\entities\email\EmailBody;

class EmailBodyForm extends Model
{
    public $id;
    public $subject;
    public $text;
    public $bodyHtml;
    public $data;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public static function fromArray(array $data)
    {
        $instance = new static();
        $instance->setAttributes($data);
        return $instance;
    }

    public static function fromModel(EmailBody $body, $config = [])
    {
        $instance = new static($config);
        $instance->subject = $body->embd_email_subject;
        $instance->text = $body->embd_email_body_text;
        $instance->bodyHtml = $body->bodyHtml;
        $instance->data = $body->embd_email_data;
        $instance->id = $body->embd_id;

        return $instance;
    }

    public static function replyFromModel(EmailBody $body, $userName = '', $config = [])
    {
        $instance = new static($config);
        $instance->subject = EmailBody::getReSubject($body->embd_email_subject);
        $instance->text = $body->embd_email_body_text;
        $instance->bodyHtml = EmailBody::getReBodyHtml($body->email->emailFrom, $userName, $body->bodyHtml);
        $instance->data = $body->embd_email_data;
        $instance->id = $body->embd_id;

        return $instance;
    }

    public function rules(): array
    {
        return [
            ['id', 'integer'],
            ['text', 'string'],
            [['data', 'bodyHtml'], 'safe'],
            ['subject', 'string', 'max' => 255],
        ];
    }
}

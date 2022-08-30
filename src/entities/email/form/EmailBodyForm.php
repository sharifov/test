<?php

namespace src\entities\email\form;

use yii\base\Model;
use src\entities\email\EmailBody;
use src\helpers\email\TextConvertingHelper;

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

    public function getText(): ?string
    {
        return ($this->text) ?? ($this->bodyHtml !== null ? TextConvertingHelper::htmlToText($this->bodyHtml) : null);
    }

    public function getBodyHtml(): ?string
    {
        return ($this->bodyHtml !== null ? TextConvertingHelper::compress($this->bodyHtml) : null);
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

    public function fields()
    {
        return [
            'embd_email_subject' => 'subject',
            'embd_email_body_text' => 'text',
            'embd_email_data' => 'data',
            'embd_id' => 'id',
            'bodyHtml',
        ];
    }

    public function getAttributesForModel()
    {
        $result = [];
        foreach ($this->fields() as $index => $name) {
            $key = is_int($index) ? $name : $index;
            $result[$key] = $this->$name;
        }
        return $result;
    }
}

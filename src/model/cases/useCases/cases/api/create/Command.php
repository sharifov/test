<?php

namespace src\model\cases\useCases\cases\api\create;

/**
 * Class Command
 *
 * @property string|null $contact_email
 * @property string|null $contact_phone
 * @property int $category_id
 * @property string|null $order_uid
 * @property array $order_info
 * @property int $project_id
 * @property string|null $subject
 * @property string|null $description
 * @property string|null $project_key
 * @property string|null $chat_visitor_id
 * @property string|null $contact_name
 */
class Command
{
    public $contact_email;
    public $contact_phone;
    public $category_id;
    public $order_uid;
    public $order_info;
    public $project_id;
    public $project_key;
    public $subject;
    public $description;
    public $chat_visitor_id;
    public $contact_name;

    public function __construct(
        ?string $contact_email,
        ?string $contact_phone,
        ?int $category_id,
        ?string $order_uid,
        array $order_info,
        int $project_id,
        ?string $subject,
        ?string $description,
        ?string $project_key,
        ?string $chat_visitor_id,
        ?string $contact_name
    ) {
        $this->contact_email = $contact_email;
        $this->contact_phone = $contact_phone;
        $this->category_id = $category_id;
        $this->order_uid = $order_uid;
        $this->order_info = $order_info;
        $this->project_id = $project_id;
        $this->project_key = $project_key;
        $this->subject = $subject;
        $this->description = $description;
        $this->chat_visitor_id = $chat_visitor_id;
        $this->contact_name = $contact_name;
    }
}

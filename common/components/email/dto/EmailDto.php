<?php

namespace common\components\email\dto;

class EmailDto
{
    public string $to = '';
    public string $from = '';
    public string $title = '';
    public string $body = '';

    public function __construct(
        string $to,
        string $from,
        string $title,
        string $body
    ) {
        $this->to = $to;
        $this->from = $from;
        $this->title = $title;
        $this->body = $body;
    }
}

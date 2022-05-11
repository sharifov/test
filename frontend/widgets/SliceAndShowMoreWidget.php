<?php

namespace frontend\widgets;

use yii\base\Widget;

class SliceAndShowMoreWidget extends Widget
{
    public array $data = [];
    public string $separator = '';
    public int $limit = 10;

    private bool $is_slice = false;
    private array $slice_data = array();

    public function run(): string
    {
        if (count($this->data) == 0) {
            return '';
        }

        if (count($this->data) > $this->limit) {
            $this->slice_data = array_slice($this->data, 0, $this->limit);
            $this->data = array_slice($this->data, $this->limit);
            $this->is_slice = true;
        }

        return $this->render('slice_show_more', [
            'data' => $this->data,
            'slice_data' => $this->slice_data,
            'separator' => $this->separator,
            'is_slice' => $this->is_slice,
        ]);
    }
}

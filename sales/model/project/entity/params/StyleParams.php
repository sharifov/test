<?php

namespace sales\model\project\entity\params;

/**
 * Class StyleParams
 *
 * @property array $values
 */
class StyleParams
{
    private array $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function isEmpty(): bool
    {
        return empty($this->values);
    }

    public function toString(): string
    {
        $result = '';
        foreach ($this->values as $key => $value) {
            if (!empty($value)) {
                $result .= $key . ':' . $value . ';';
            }
        }
        return $result;
    }

    public static function default(): array
    {
        return [
            'background-color' => '#f4f7fa',
            'color' => '#000000',
        ];
    }
}

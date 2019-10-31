<?php

namespace sales\logger\formatter;

interface Formatter
{
	public function getFormattedAttributeLabel(string $attribute): string;

	public function getFormattedAttributeValue($attribute, $value);

	public function getExceptedAttributes(): array;
}
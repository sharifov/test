<?php
namespace modules\rbacImportExport\src\helpers;

class RbacDataHelper
{
	public static function encode(array $data): string
	{
		return serialize($data);
	}

	public static function decode(string $data): array
	{
		return @unserialize($data) ?? [];
	}

	public static function isAssoc(array $arr): bool
	{
		return array_keys($arr) != range(0, count($arr) - 1);
	}

	public static function getFileSize(string $filePath)
	{
		if ($filePath && file_exists($filePath)) {
			return filesize($filePath);
		}
		return 0;
	}

}
<?php
namespace modules\rbacImportExport\src\formatters;

class FileSizeFormatter
{
	public static function asSize($value, int $base = 1024, int $decimals = 2): string
	{
		$units=array('B','KB','MB','GB','TB');
		for($i=0; $base<=$value; $i++) {
			$value /= $base;
		}
		return round($value, $decimals).$units[$i];
	}
}
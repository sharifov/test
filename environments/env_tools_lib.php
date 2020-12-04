<?php

/**
 * @param $root
 * @param string $basePath
 * @return array
 */
function getFileList($root, $basePath = '')
{
    //echo $root; exit;
    $files = [];
    $handle = opendir($root);
    while (($path = readdir($handle)) !== false) {
        if ($path === '.git' || $path === '.svn' || $path === '.' || $path === '..') {
            continue;
        }
        $fullPath = "$root/$path";
        $relativePath = $basePath === '' ? $path : "$basePath/$path";
        if (is_dir($fullPath)) {
            $files = array_merge($files, getFileList($fullPath, $relativePath));
        } else {
            $files[] = $relativePath;
        }
    }
    closedir($handle);
    return $files;
}

/**
 * @param $root
 * @param $paths
 * @return array
 */
function getEnvironmentList($root, $paths)
{
    $envList = [];
    foreach ($paths as $n => $file) {
        $fileName = $root . '/' . $file;
        $content = file_get_contents($fileName);

        $matches = [];
        preg_match_all('~\{\{(.*)\}\}~U', $content, $matches);
        $count = 0;
        if (!empty($matches[1])) {
            foreach ($matches[1] as $key) {
                $key = trim($key);
                $keyVal = $key;
                $keyArray = explode(':', $key);
                if ($keyArray && !empty($keyArray[1])) {
                    if (in_array($keyArray[1], ['bool', 'int', 'str'])) {
                        $keyVal = $keyArray[0];
                    }
                }

                $envList[$key] = '{{ ' . str_replace(['.', '-'], '_', $keyVal) . ' }}';
            }
            $count = count($matches[1]);
        }
        if ($count) {
            echo formatMessage($n . '. "' . $file . '" => ' . $count . " keys \n", ['fg-blue']);
        }
    }
    return $envList;
}

/**
 * @param $envList
 * @param $root
 * @param $file
 * @return false|int
 */
function saveEnvironmentList($envList, $root, $file)
{
    $lines = [];
    ksort($envList);
    if ($envList) {
        foreach ($envList as $key => $value) {
            $lines[] = $key . '=' . $value;
        }
    }
    $file = $root . '/' . $file;
    $content = implode("\r\n", $lines);
    return file_put_contents($file, $content);
}


/**
 * Prints error message.
 * @param string $message message
 */
function printError($message)
{
    echo "\n  " . formatMessage("Error. $message", ['fg-red']) . " \n";
}

/**
 * Returns true if the stream supports colorization. ANSI colors are disabled if not supported by the stream.
 *
 * - windows without ansicon
 * - not tty consoles
 *
 * @return boolean true if the stream supports ANSI colors, otherwise false.
 */
function ansiColorsSupported()
{
    return DIRECTORY_SEPARATOR === '\\'
        ? getenv('ANSICON') !== false || getenv('ConEmuANSI') === 'ON'
        : function_exists('posix_isatty') && @posix_isatty(STDOUT);
}

/**
 * Get ANSI code of style.
 * @param string $name style name
 * @return integer ANSI code of style.
 */
function getStyleCode($name)
{
    $styles = [
        'bold' => 1,
        'fg-black' => 30,
        'fg-red' => 31,
        'fg-green' => 32,
        'fg-yellow' => 33,
        'fg-blue' => 34,
        'fg-magenta' => 35,
        'fg-cyan' => 36,
        'fg-white' => 37,
        'bg-black' => 40,
        'bg-red' => 41,
        'bg-green' => 42,
        'bg-yellow' => 43,
        'bg-blue' => 44,
        'bg-magenta' => 45,
        'bg-cyan' => 46,
        'bg-white' => 47,
    ];
    return $styles[$name];
}

/**
 * Formats message using styles if STDOUT supports it.
 * @param string $message message
 * @param string[] $styles styles
 * @return string formatted message.
 */
function formatMessage($message, $styles)
{
    if (empty($styles) || !ansiColorsSupported()) {
        return $message;
    }

    return sprintf("\x1b[%sm", implode(';', array_map('getStyleCode', $styles))) . $message . "\x1b[0m";
}

/**
 * @param array $files
 * @param string $root
 * @param bool $exit
 * @return bool
 */
function checkRequiredFiles(array $files, string $root, bool $exit = true)
{
    $errors = [];
    foreach ($files as $file) {
        if (!is_readable($root . '/' . $file)) {
            $errors[] = 'File (' . $file . ') not exist or not readable';
        }
    }
    if ($errors) {
        printError(implode(', ', $errors));
        if ($exit) {
            exit();
        }
    }
    return $errors ? false : true;
}

/**
 * @param array $data
 * @param string $contentComparison
 * @return array ['tagsNotExist' => $tagsNotExist, 'allTags' => $allTags]
 */
function searchDiff(array $data, string $contentComparison): array
{
    $tagsNotExist = $allTags = [];
    foreach ($data as $key => $row) {
        $row = trim($row);
        if ($row === '') {
            continue;
        }
        if ($tag = substr($row, 0, strpos($row, '='))) {
            $allTags[] = $tag;
            if (strpos($contentComparison, $tag) === false) {
                $tagsNotExist[$key] = $row;
            }
        }
    }
    return ['tagsNotExist' => $tagsNotExist, 'allTags' => $allTags];
}

<?php

$gitBranch = $gitHash = '';
$dir = __DIR__ . '/../../.git/';

$file = $dir . 'HEAD';
if (file_exists($file)) {
    $gitBranch = trim(substr(@file_get_contents($file), 4));

    $file = $dir . $gitBranch;
    $gitHash = trim(@file_get_contents($file));
}

return [
    'version' => '3.59.0',
    'git_branch' => $gitBranch,
    'git_hash' => $gitHash,
];

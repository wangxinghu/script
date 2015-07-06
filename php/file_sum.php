#! /usr/local/php5.4/bin/php
<?php
$sDirScan = '/mnt/funplus/logs/fp_rstory/history/';
$sFileSum = __DIR__.'/sha1sum.json';

$sDirScan = rtrim(trim($sDirScan), '/');

chdir($sDirScan);

if (!file_exists($sFileSum)) {
    file_put_contents($sFileSum, '{}');
}
$lFileSum = json_decode(file_get_contents($sFileSum), TRUE);
if (!is_array($lFileSum)) {
    die('can not write file '.$sFileSum);
}

$lFile = scandir('.');
$lSave = [];
foreach ($lFile as $sFile) {
    if (substr($sFile, 0, 1) === '.') {
        continue;
    }

    echo "\n";
    echo sprintf('%-30s ', $sFile);

    if (array_key_exists($sFile, $lFileSum)) {
        $lSave[$sFile] = $lFileSum[$sFile];
        echo 'skip';
        continue;
    }

    $aInfo = [
        'size' => filesize($sFile),
            'sha1' => sha1_file($sFile),
            ];
    $lSave[$sFile] = $aInfo;
    echo sprintf('%15s %s', number_format($aInfo['size']), $aInfo['sha1']);
}
echo "\n";

file_put_contents($sFileSum, json_encode($lSave, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

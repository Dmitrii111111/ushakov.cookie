<?php

use Bitrix\Main\Config\Option;

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$siteId = $_POST['SITE_ID'] ?? 's1';
$siteId = trim(strip_tags($siteId));

$disableMob = Option::get('ushakov.cookie', 'disableMob_' . $siteId, 'N');
$color = Option::get('ushakov.cookie', 'link_color_' . $siteId, '#34a0ff');
$bgColor = Option::get('ushakov.cookie', 'bg_color_' . $siteId, '#000000'); //цвет плашки
$zIndex = Option::get('ushakov.cookie', 'z_index_' . $siteId, '9999');
$textButton = Option::get('ushakov.cookie', 'textButton_' . $siteId, '');

// Замена текста в решётках на тег <a>
$link = Option::get('ushakov.cookie', 'link_' . $siteId);
if (trim($link) === '') {
    $link = '/cookies-agreement.php';
}
// $textTemplate = Option::get('ushakov.cookie', 'text_' . $siteId);
$textTemplate = Option::get('ushakov.cookie', 'text_' . $siteId, '');
if (trim($textTemplate) === '') {
    $textTemplate = 'Пользуясь нашим сайтом, вы соглашаетесь с тем, что #мы используем cookies#';
}

$textHtml = preg_replace('/#(.*?)#/', '<a href="' . $link . '" target="_blank">$1</a>', $textTemplate);
$text = trim(strip_tags($textHtml, '<a><p><b><div><span><br>'));

$responseData = [
    'status' => 'success',
    'message' => 'Cookie applied successfully',
    'data' => [
        'disableMob' => in_array($disableMob, ['Y', 'N']) ? $disableMob : 'N',
        'text' => $text,
        'color' => $color,
        'bgColor' => $bgColor, // цвет плашки
        'zIndex' => intval($zIndex) >= 0 ? intval($zIndex) : '9999',
        'textButton' => $textButton ? : '',
    ]
];

header('Content-Type: application/json');

if (!$json = json_encode($responseData)) {
    if (!mb_check_encoding($text, 'UTF-8')) {
        $responseData['data']['text'] = mb_convert_encoding($text, 'UTF-8', 'Windows-1251');
        $json = json_encode($responseData);
    }
}

echo $json;

<?php

use Bitrix\Main\Config\Option;

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$siteId = $_POST['SITE_ID'] ?? 's1';
$siteId = trim(strip_tags($siteId));

$disableMob = Option::get('ushakov.cookie', 'disableMob_' . $siteId, 'N');
$bgColor = Option::get('ushakov.cookie', 'bg_color_' . $siteId, 'rgba(0, 0, 0, 0.85)'); //цвет плашки
$borderRadius = Option::get('ushakov.cookie', 'border_radius_' . $siteId, '6px');
$shadow = Option::get('ushakov.cookie', 'shadow_' . $siteId, 'Y');

$position = Option::get('ushakov.cookie', 'position_' . $siteId, 'bottom');

$align = Option::get('ushakov.cookie', 'align_' . $siteId, 'center');
$align = in_array($align, ['left','center','right'], true) ? $align : 'center';

$maxWidth = Option::get('ushakov.cookie', 'max_width_' . $siteId, '640px');
$offsetX  = Option::get('ushakov.cookie', 'offset_x_'  . $siteId, '0px');
$offsetY  = Option::get('ushakov.cookie', 'offset_y_'  . $siteId, '7px');

$zIndex = Option::get('ushakov.cookie', 'z_index_' . $siteId, '9999');
$delayMs = \Bitrix\Main\Config\Option::get('ushakov.cookie', 'delay_ms', '0');
$delayMs = (is_numeric($delayMs) && (int)$delayMs >= 0) ? (int)$delayMs : 0;
$textButton = Option::get('ushakov.cookie', 'textButton_' . $siteId, '');

// Замена текста в решётках на тег <a>
$link = Option::get('ushakov.cookie', 'link_' . $siteId);
if (trim($link) === '') {
    $link = '/cookies-agreement.php';
}
// $textTemplate = Option::get('ushakov.cookie', 'text_' . $siteId);
$textTemplate = Option::get('ushakov.cookie', 'text_' . $siteId, '');
if (trim($textTemplate) === '') {
    $textTemplate = 'Мы используем файлы cookie для обеспечения работы сайта, сбора анонимной статистики и улучшения пользовательского опыта. Продолжая использование сайта, вы соглашаетесь на обработку ваших данных с использованием cookie-файлов в соответствии с нашей политикой конфиденциальности.';
}

$allowed = '<a><p><b><strong><i><em><u><br><div><span><font>'
         . '<ul><ol><li>'
         . '<h1><h2><h3><h4><h5><h6><blockquote>';
$text = trim(strip_tags($textTemplate, $allowed));

// простая валидация радиуса (разрешим px|rem|em|%)
$borderRadius = trim($borderRadius);
if (!preg_match('/^\d+(\.\d+)?(px|rem|em|%)$/i', $borderRadius)) {
    $borderRadius = '6px';
}

// нормализуем
$position = ($position === 'top') ? 'top' : 'bottom';
$unitRe = '/^\s*\d+(\.\d+)?(px|rem|em|%)\s*$/i';
$maxWidth = preg_match($unitRe, $maxWidth) ? trim($maxWidth) : '640px';
$offsetX  = preg_match($unitRe, $offsetX)  ? trim($offsetX)  : '0px';
$offsetY  = preg_match($unitRe, $offsetY)  ? trim($offsetY)  : '7px';

$responseData = [
    'status' => 'success',
    'message' => 'Cookie applied successfully',
    'data' => [
        'disableMob' => in_array($disableMob, ['Y', 'N']) ? $disableMob : 'N',
        'text' => $text,
        'bgColor' => $bgColor, // цвет плашки
        'borderRadius' => $borderRadius,
        'shadow' => in_array($shadow, ['Y','N'], true) ? $shadow : 'Y',

        'position' => $position,

        'align' => $align,

        'maxWidth' => $maxWidth,
        'offsetX'  => $offsetX,
        'offsetY'  => $offsetY,

        'zIndex' => intval($zIndex) >= 0 ? intval($zIndex) : '9999',
        'delayMs' => $delayMs,
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

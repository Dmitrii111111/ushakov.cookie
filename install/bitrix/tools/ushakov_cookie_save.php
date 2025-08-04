<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$days = intval(Option::get('ushakov.cookie', 'days', '60'));
if ($days < 1) {
    $days = 60;
}

$server = Application::getInstance()->getContext()->getServer()->getServerName();
setcookie('ushakov_cookie', 'cat', time() + 60 * 60 * 24 * $days, '/', $server);

$responseData = [
    'status' => 'success',
    'message' => 'Cookie applied successfully'
];

header('Content-Type: application/json');

echo json_encode($responseData);

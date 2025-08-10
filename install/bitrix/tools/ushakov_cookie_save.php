<?php

// use Bitrix\Main\Application;
// use Bitrix\Main\Config\Option;

// require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

// $days = intval(Option::get('ushakov.cookie', 'days', '60'));
// if ($days < 1) {
//     $days = 60;
// }

// $server = Application::getInstance()->getContext()->getServer()->getServerName();
// setcookie('ushakov_cookie', 'cat', time() + 60 * 60 * 24 * $days, '/', $server);

// $responseData = [
//     'status' => 'success',
//     'message' => 'Cookie applied successfully'
// ];

// header('Content-Type: application/json');

// echo json_encode($responseData);



use Bitrix\Main\Config\Option;

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

// режим хранения
$mode = Option::get('ushakov.cookie', 'consent_mode', 'days');

// кол-во дней (для режима days)
$days = (int) Option::get('ushakov.cookie', 'days', '365');
if ($days <= 0) { $days = 365; }

// expires: 0 = сессионная кука (до закрытия браузера)
$expires = ($mode === 'session') ? 0 : (time() + $days * 86400);

$secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

// одна кука на весь сайт
setcookie('ushakov_cookie', '1', [
    'expires'  => $expires,
    'path'     => '/',
    'secure'   => $secure,
    'httponly' => false,
    'samesite' => 'Lax',
]);

header('Content-Type: text/plain; charset=UTF-8');
echo 'OK';

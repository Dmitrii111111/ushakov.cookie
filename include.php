<?php

// use Bitrix\Main\Config\Option;
// use Bitrix\Main\Page\Asset;

// // Получаем опцию: Включено для данного сайта?
// $active = Option::get('ushakov.cookie', 'active_' . SITE_ID);

// // Прерываем выполнения если выключено или мы в админке
// if ($active !== 'Y' || strpos($_SERVER['REQUEST_URI'], '/bitrix/admin') !== false) {
//     return;
// }

// // Подключаем стили и скрипты
// Asset::getInstance()->addJs('/bitrix/js/ushakov.cookie/script.js');
// Asset::getInstance()->addCss('/bitrix/css/ushakov.cookie/style.css');


use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;

$active = Option::get('ushakov.cookie', 'active_' . SITE_ID);

// Прерываем выполнение только если явно отключено или мы в админке
if ($active === 'N' || strpos($_SERVER['REQUEST_URI'], '/bitrix/admin') !== false) {
    return;
}

Asset::getInstance()->addJs('/bitrix/js/ushakov.cookie/script.js');
Asset::getInstance()->addCss('/bitrix/css/ushakov.cookie/style.css');
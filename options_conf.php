<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SiteTable;

Loc::loadMessages(dirname(__FILE__) . '/options.php');

$arConf = [];

// Получаем сайты и генерируем столько блоков настроек, сколько сайтов
$res = SiteTable::getList([]);
while ($item = $res->fetch()) {
    $options[] = [
        'type' => 'heading',
        'heading' => $item['NAME'] . ' <sup>' . $item['LID'] . '</sup>',
    ];

    $options[] = [
        'type' => 'checkbox',
        'name' => 'active_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_ACTIVE'),
        'value' => $item['DEF'] === 'Y' ? 'Y' : 'N',
    ];

    $options[] = [
        'type' => 'checkbox',
        'name' => 'disableMob_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_ACTIVE_MOB'),
        'value' => 'N',
    ];

    $options[] = [
        'type' => 'textarea',
        'name' => 'text_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_TEXT_LABEL'),
        'value' => Loc::getMessage('USHAKOV_COOKIE_OPT_TEXT'),
        'rows' => 3,
    ];

    $options[] = [
        'type' => 'text',
        'name' => 'link_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_LINK'),
        'value' => $item['DIR'] . 'cookies-agreement.php',
    ];

    $options[] = [
        'type' => 'text',
        'name' => 'textButton_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_TEXT_BUTTON_LABEL'),
        'value' => '',
        'placeholder' => Loc::getMessage('USHAKOV_COOKIE_TEXT_BUTTON_PLACEHOLDER'),
    ];

    $options[] = [
        'type' => 'text',
        'name' => 'link_color_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_LINK_COLOR'),
        'value' => '#34a0ff',
    ];

    $options[] = [
        'type' => 'text',
        'name' => 'z_index_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_ZINDEX'),
        'value' => '9999',
        'size' => 5
    ];
}

$options[] = [
    'type' => 'heading',
    'heading' => Loc::getMessage('USHAKOV_COOKIE_OPT_COMMON'),
];

$options[] = [
    'type' => 'text',
    'name' => 'days',
    'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_DAYS'),
    'value' => '365',
    'size' => '10'
];

$options[] = [
    'type' => 'message',
    'message' => Loc::getMessage('USHAKOV_COOKIE_OPT_HELP_MESSAGE'),
];

return [
    'edit1' => [
        'TAB_NAME' => Loc::getMessage('USHAKOV_COOKIE_TAB'),
        'TAB_TITLE' => Loc::getMessage('USHAKOV_COOKIE_TITLE'),
        'ICON' => '',
        'options' => $options,
    ],
];

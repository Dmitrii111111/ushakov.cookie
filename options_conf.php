<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SiteTable;
use Bitrix\Main\Config\Option;

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

    // цвет кодом
    // $options[] = [
    //     'type' => 'text',
    //     'name' => 'link_color_' . $item['LID'],
    //     'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_LINK_COLOR'),
    //     'value' => '#34a0ff',
    // ];

    // цвет ссылки
    $options[] = [
        'type' => 'custom',
        'name' => 'link_color_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_LINK_COLOR'),
        // 'value' => '#34a0ff',
        'html' => '<input type="color" name="link_color_' . $item["LID"] . '" value="' . htmlspecialcharsbx(Option::get("ushakov.cookie", "link_color_" . $item["LID"], "#34a0ff")) . '" style="width: 60px; height: 30px; padding: 0; border: none; cursor:pointer;">'
    ];

    // цвет текста плашки
    $options[] = [
        'type' => 'custom',
        'name' => 'text_color_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_TEXT_COLOR'),
        'html' => '<input type="color" name="text_color_' . $item["LID"] . '" value="' . 
            htmlspecialcharsbx(\Bitrix\Main\Config\Option::get("ushakov.cookie", "text_color_" . $item["LID"], "#ffffff")) . 
            '" style="width: 60px; height: 30px; padding: 0; border: none; cursor:pointer;" class="spectrum-text-color">'
    ];

    // цвет плашки
    $options[] = [
        'type' => 'custom',
        'name' => 'bg_color_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_BG_COLOR'),
        'html' => '<input class="spectrum-bg-color" type="text" name="bg_color_' . $item["LID"] . '" value="' . htmlspecialcharsbx(\Bitrix\Main\Config\Option::get("ushakov.cookie", "bg_color_" . $item["LID"], "rgba(0,0,0,0.8)")) . '" style="width: 140px;">'
    ];

    // размер текста
    $options[] = [
        'type' => 'text',
        'name' => 'font_size_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_FONT_SIZE'),
        'value' => '14px',
        'size' => 6,
        'placeholder' => Loc::getMessage('USHAKOV_COOKIE_OPT_FONT_SIZE_PLACEHOLDER'),
    ];

    // радиус скругления
    $options[] = [
        'type'  => 'text',
        'name'  => 'border_radius_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_BORDER_RADIUS'),
        'value' => '6px',
        'size'  => 6,
        'placeholder' => Loc::getMessage('USHAKOV_COOKIE_OPT_BORDER_RADIUS_PLACEHOLDER'),
    ];

    // тень (вкл/выкл)
    $options[] = [
        'type'  => 'checkbox',
        'name'  => 'shadow_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_SHADOW'),
        'value' => 'Y', // по умолчанию включена
    ];

    // Позиция плашки
    $options[] = [
        'type'  => 'list',
        'name'  => 'position_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_POSITION'),
        'list'  => [
            'bottom' => Loc::getMessage('USHAKOV_COOKIE_OPT_POSITION_BOTTOM'),
            'top'    => Loc::getMessage('USHAKOV_COOKIE_OPT_POSITION_TOP'),
        ],
        'value' => 'bottom',
    ];

    // Макс. ширина (на десктопе)
    $options[] = [
        'type' => 'text',
        'name' => 'max_width_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_MAX_WIDTH'),
        'value' => '640px',
        'size' => 8,
        'placeholder' => Loc::getMessage('USHAKOV_COOKIE_OPT_MAX_WIDTH_PLACEHOLDER'),
    ];

    // Горизонтальный отступ
    $options[] = [
        'type' => 'text',
        'name' => 'offset_x_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_OFFSET_X'),
        'value' => '0px',
        'size' => 6,
        'placeholder' => Loc::getMessage('USHAKOV_COOKIE_OPT_OFFSET_X_PLACEHOLDER'),
    ];

    // Вертикальный отступ
    $options[] = [
        'type' => 'text',
        'name' => 'offset_y_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_OFFSET_Y'),
        'value' => '7px',
        'size' => 6,
        'placeholder' => Loc::getMessage('USHAKOV_COOKIE_OPT_OFFSET_Y_PLACEHOLDER'),
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

// селект "Хранение согласия"
$options[] = [
    'type'  => 'list',
    'name'  => 'consent_mode',
    'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_CONSENT_MODE'),
    'list'  => [
        'days'    => Loc::getMessage('USHAKOV_COOKIE_OPT_CONSENT_MODE_DAYS'),
        'session' => Loc::getMessage('USHAKOV_COOKIE_OPT_CONSENT_MODE_SESSION'),
    ],
    'value' => 'days', // дефолт: хранить N дней
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

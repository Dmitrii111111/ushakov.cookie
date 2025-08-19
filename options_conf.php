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
        'group' => 'SITE_SETTINGS'
    ];

    $options[] = [
        'type' => 'checkbox',
        'name' => 'active_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_ACTIVE'),
        'value' => $item['DEF'] === 'Y' ? 'Y' : 'N',
        'group' => 'SITE_SETTINGS'
    ];

    $options[] = [
        'type' => 'checkbox',
        'name' => 'disableMob_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_ACTIVE_MOB'),
        'value' => 'N',
        'group' => 'SITE_SETTINGS'
    ];

    $options[] = [
        'type'  => 'custom',
        'name'  => 'text_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_TEXT_LABEL'),
        'html'  => (function() use ($item) {
            $name    = 'text_' . $item['LID'];
            $default = Loc::getMessage('USHAKOV_COOKIE_OPT_TEXT');
            $content = \Bitrix\Main\Config\Option::get('ushakov.cookie', $name, $default);

            if (\Bitrix\Main\Loader::includeModule('fileman')) {
                ob_start();
                $editor = new \CHTMLEditor();
                $editor->Show([
                    'id'                  => $name,
                    'inputName'           => $name,
                    'content'             => $content,
                    'siteId'              => $item['LID'],

                    'width'               => '100%',
                    'height'              => 220,
                    'autoResize'          => true,
                    'autoResizeOffset'    => 40,
                    'bbCode'              => false,
                    'useFileDialogs'      => false,
                    'askBeforeUnloadPage' => false,
                    'showNodeNavi'        => false,
                    'showTaskbars'        => false,
                    'SeoUniqText'         => false,

                    // ['SearchButton', 'ChangeView', 'Undo', 'Redo', 'StyleSelector', 'FontSelector', 'FontSize', 'Bold', 'Italic', 'Underline', 'Strikeout', 'Color', 'RemoveFormat', 'TemplateSelector', 'OrderedList', 'UnorderedList', 'IndentButton', 'OutdentButton', 'AlignList', 'InsertLink', 'InsertImage', 'InsertVideo', 'InsertAnchor', 'InsertTable', 'InsertChar', 'Settings', 'Fullscreen', 'PrintBreak', 'PageBreak', 'InsertHr', 'Spellcheck', 'Code', 'Quote', 'Smile', 'Sub', 'Sup', 'More', 'BbCode', 'SeoUniqText']
                    // SearchButton — поиск/замена по содержимому редактора.
                    // ChangeView — переключение режимов: визуальный / HTML-код / сплит.
                    // Undo / Redo — отмена/повтор последнего действия (Ctrl+Z / Ctrl+Y).
                    // StyleSelector — выпадающий список "Абзац / Заголовок H1–H6 / и т.п." Меняет блочную обёртку (<p>, <h1>…).
                    // FontSelector — выбор шрифта, добавляет inline-стиль font-family (обычно <span style="font-family:...">).
                    // FontSize — размер шрифта, даёт inline-стиль font-size.
                    // Bold / Italic / Underline / Strikeout — жирный, курсив, подчёркивание, зачёркивание (<b>/<strong>, <i>/<em>, <u>, <s>/<strike>).
                    // Color — цвет текста и/или фона текста; выдаёт <span style="color:..."> и/или background-color.
                    // RemoveFormat — убрать форматирование (чистит лишние <span style=...>, <b>, и т.д.).
                    // TemplateSelector — выбор шаблона сайта для предпросмотра стилей внутри фрейма редактора (чаще всего не нужен в настройках модуля).
                    // OrderedList / UnorderedList — нумерованный/маркированный список (<ol> / <ul> + <li>).
                    // IndentButton / OutdentButton — увеличить/уменьшить отступ; в списках меняет вложенность (<li> внутри <ol>/<ul>), вне списков может добавлять стилевой отступ/обёртку.
                    // AlignList — группа выравнивания: по левому/центру/правому краю, по ширине (эквивалентно text-align: left/center/right/justify).
                    // InsertLink — вставить/редактировать ссылку (<a href>).
                    // InsertImage — вставить картинку (с диалогом файла, работает при установленном fileman).
                    // InsertVideo — вставить видео (YouTube/Vimeo и т.п., вставляет <iframe>/встраивание).
                    // InsertAnchor — якорь на странице (<a name="..."> или id).
                    // InsertTable — вставка/редактирование таблиц (<table>, thead/tbody, кол-во строк/столбцов).
                    // InsertChar — "спецсимвол" (набор символов типа ™, ©, ↑ и др.).
                    // Settings — настройки редактора (панели, поведение и т.д.).
                    // Fullscreen — на весь экран.
                    // PrintBreak — "разрыв для печати" (служебная метка для печатных шаблонов Bitrix).
                    // PageBreak — "разделитель страниц" (служебный тег <BREAK />, используется некоторыми компонентами/шаблонами для пагинации контента).
                    // InsertHr — горизонтальная линия (<hr>).
                    // Spellcheck — орфография (если доступна конфигурация словаря).
                    // Code — оформление как код (обычно <pre><code> или аналогичный блочный стиль).
                    // Quote — цитата (<blockquote> или стилизованный блок).
                    // Smile — смайлики (вставляет изображения/эмодзи, если разрешено).
                    // Sub / Sup — нижний/верхний индекс (<sub>, <sup>).
                    // More — "Ещё…": выпадающий контейнер для лишних кнопок, если панель узкая.
                    // BbCode — переключатель BB-кода (меняет модель разметки; обычно не используем, если хотим чистый HTML).
                    // SeoUniqText — служебная кнопка модуля SEO Bitrix "Отправить уникальный текст в Яндекс".

                        // минимальный набор
                        'controlsMap' => [
                            ['id' => 'ChangeView',    'compact' => true],
                            ['id' => 'StyleSelector', 'compact' => true],
                            ['id' => 'FontSelector',  'compact' => true],
                            ['id' => 'FontSize',      'compact' => true],
                            ['id' => 'Bold',          'compact' => true],
                            ['id' => 'Italic',        'compact' => true],
                            ['id' => 'Underline',     'compact' => true],
                            ['id' => 'AlignList',     'compact' => true],
                            ['id' => 'InsertLink',    'compact' => true],
                            ['id' => 'Color',         'compact' => true],
                            ['id' => 'Undo',          'compact' => true],
                            ['id' => 'Redo',          'compact' => true],
                            ['id' => 'OrderedList',   'compact' => true],
                            ['id' => 'UnorderedList', 'compact' => true],
                            ['id' => 'IndentButton',  'compact' => true],
                            ['id' => 'OutdentButton', 'compact' => true],
                            ['id' => 'InsertChar',    'compact' => true],
                            ['id' => 'Fullscreen',    'compact' => true],
                        ],
                ]);
                return ob_get_clean();
            }

            // Фолбэк, если fileman недоступен
            return '<textarea name="' . htmlspecialcharsbx($name) . '" cols="43" rows="4">' .
                htmlspecialcharsbx($content) .
                '</textarea>';
        })(),
        'group' => 'CONTENT'
    ];

    $options[] = [
        'type' => 'text',
        'name' => 'textButton_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_TEXT_BUTTON_LABEL'),
        'value' => '',
        'placeholder' => Loc::getMessage('USHAKOV_COOKIE_TEXT_BUTTON_PLACEHOLDER'),
        'group' => 'CONTENT'
    ];

    $options[] = [
        'type'  => 'list',
        'name'  => 'accept_btn_position_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_ACCEPT_BTN_POSITION'),
        'list'  => [
            'left'   => Loc::getMessage('USHAKOV_COOKIE_OPT_ACCEPT_BTN_POSITION_LEFT'),
            'right'  => Loc::getMessage('USHAKOV_COOKIE_OPT_ACCEPT_BTN_POSITION_RIGHT'),
            'bottom' => Loc::getMessage('USHAKOV_COOKIE_OPT_ACCEPT_BTN_POSITION_BOTTOM'),
        ],
        'value' => 'right',
        'group' => 'CONTENT'
    ];

    $options[] = [
        'type'  => 'list',
        'name'  => 'close_btn_position_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_CLOSE_BTN_POSITION'),
        'list'  => [
            'left-top'        => Loc::getMessage('USHAKOV_COOKIE_OPT_CLOSE_BTN_POSITION_LEFT_TOP'),
            'right-top'       => Loc::getMessage('USHAKOV_COOKIE_OPT_CLOSE_BTN_POSITION_RIGHT_TOP'),
            'left-middle'     => Loc::getMessage('USHAKOV_COOKIE_OPT_CLOSE_BTN_POSITION_LEFT_MIDDLE'),
            'right-middle'    => Loc::getMessage('USHAKOV_COOKIE_OPT_CLOSE_BTN_POSITION_RIGHT_MIDDLE'),
        ],
        'value' => 'right-top',
        'group' => 'CONTENT'
    ];

    $acceptBg = htmlspecialcharsbx(\Bitrix\Main\Config\Option::get('ushakov.cookie', 'accept_btn_bg_color_' . $item['LID'], '#4CAF50'));
    $options[] = [
        'type'  => 'custom',
        'name'  => 'accept_btn_bg_color_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_ACCEPT_BTN_BG_COLOR'),
        'html'  => '<input class="spectrum-bg-color" type="text" name="accept_btn_bg_color_' . $item['LID'] . '" value="' . $acceptBg . '" style="width:140px;">',
        'group' => 'APPEARANCE'
    ];

    $acceptText = htmlspecialcharsbx(\Bitrix\Main\Config\Option::get('ushakov.cookie', 'accept_btn_text_color_' . $item['LID'], '#FFFFFF'));
    $options[] = [
        'type'  => 'custom',
        'name'  => 'accept_btn_text_color_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_ACCEPT_BTN_TEXT_COLOR'),
        'html'  => '<input class="spectrum-bg-color" type="text" name="accept_btn_text_color_' . $item['LID'] . '" value="' . $acceptText . '" style="width:140px;">',
        'group' => 'APPEARANCE'
    ];

    $closeColor = htmlspecialcharsbx(\Bitrix\Main\Config\Option::get('ushakov.cookie', 'close_btn_color_' . $item['LID'], 'rgb(255, 7, 7)'));
    $options[] = [
        'type'  => 'custom',
        'name'  => 'close_btn_color_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_CLOSE_BTN_COLOR'),
        'html'  => '<input class="spectrum-bg-color" type="text" name="close_btn_color_' . $item['LID'] . '" value="' . $closeColor . '" style="width:140px;">',
        'group' => 'APPEARANCE'
    ];

    // Интеграция с системой согласий Bitrix
    $saveToRegistry = htmlspecialcharsbx(\Bitrix\Main\Config\Option::get('ushakov.cookie', 'save_to_registry_' . $item['LID'], 'N'));
    $options[] = [
        'type'  => 'list',
        'name'  => 'save_to_registry_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_SAVE_TO_REGISTRY'),
        'list'  => [
            'Y' => Loc::getMessage('USHAKOV_COOKIE_OPT_SAVE_TO_REGISTRY_Y'),
            'N' => Loc::getMessage('USHAKOV_COOKIE_OPT_SAVE_TO_REGISTRY_N'),
        ],
        'value' => $saveToRegistry,
        'group' => 'INTEGRATION'
    ];

    $agreementId = htmlspecialcharsbx(\Bitrix\Main\Config\Option::get('ushakov.cookie', 'agreement_id_' . $item['LID'], ''));
    $options[] = [
        'type'  => 'custom',
        'name'  => 'agreement_id_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_AGREEMENT_ID'),
        'html'  => '<input type="text" name="agreement_id_' . $item['LID'] . '" value="' . $agreementId . '" placeholder="' . Loc::getMessage('USHAKOV_COOKIE_OPT_AGREEMENT_ID_PLACEHOLDER') . '" style="width:140px;"> <a href="/bitrix/admin/agreement_admin.php" target="_blank" style="margin-left: 10px; color: #0066cc; text-decoration: underline;">' . Loc::getMessage('USHAKOV_COOKIE_OPT_VIEW_AGREEMENTS_LINK') . '</a>',
        'group' => 'INTEGRATION'
    ];

    // Настройки внешнего вида
    $bgColor = htmlspecialcharsbx(\Bitrix\Main\Config\Option::get('ushakov.cookie', 'bg_color_' . $item['LID'], 'rgba(0, 0, 0, 0.85)'));
    $options[] = [
        'type'  => 'custom',
        'name'  => 'bg_color_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_BG_COLOR'),
        'html'  => '<input class="spectrum-bg-color" type="text" name="bg_color_' . $item['LID'] . '" value="' . $bgColor . '" style="width:140px;">',
        'group' => 'APPEARANCE'
    ];

    $borderRadius = htmlspecialcharsbx(\Bitrix\Main\Config\Option::get('ushakov.cookie', 'border_radius_' . $item['LID'], '6px'));
    $options[] = [
        'type' => 'text',
        'name' => 'border_radius_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_BORDER_RADIUS'),
        'value' => $borderRadius,
        'size' => 8,
        'placeholder' => '6px',
        'group' => 'APPEARANCE'
    ];

    $shadow = htmlspecialcharsbx(\Bitrix\Main\Config\Option::get('ushakov.cookie', 'shadow_' . $item['LID'], 'Y'));
    $options[] = [
        'type'  => 'list',
        'name'  => 'shadow_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_SHADOW'),
        'list'  => [
            'Y' => Loc::getMessage('USHAKOV_COOKIE_OPT_SHADOW_Y'),
            'N' => Loc::getMessage('USHAKOV_COOKIE_OPT_SHADOW_N'),
        ],
        'value' => $shadow,
        'group' => 'APPEARANCE'
    ];

    // Настройки положения
    $position = htmlspecialcharsbx(\Bitrix\Main\Config\Option::get('ushakov.cookie', 'position_' . $item['LID'], 'bottom'));
    $options[] = [
        'type'  => 'list',
        'name'  => 'position_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_POSITION'),
        'list'  => [
            'top'    => Loc::getMessage('USHAKOV_COOKIE_OPT_POSITION_TOP'),
            'bottom' => Loc::getMessage('USHAKOV_COOKIE_OPT_POSITION_BOTTOM'),
        ],
        'value' => $position,
        'group' => 'POSITION'
    ];

    $align = htmlspecialcharsbx(\Bitrix\Main\Config\Option::get('ushakov.cookie', 'align_' . $item['LID'], 'center'));
    $options[] = [
        'type'  => 'list',
        'name'  => 'align_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_ALIGN'),
        'list'  => [
            'left'   => Loc::getMessage('USHAKOV_COOKIE_OPT_ALIGN_LEFT'),
            'center' => Loc::getMessage('USHAKOV_COOKIE_OPT_ALIGN_CENTER'),
            'right'  => Loc::getMessage('USHAKOV_COOKIE_OPT_ALIGN_RIGHT'),
        ],
        'value' => 'center',
        'group' => 'POSITION'
    ];

    // Макс. ширина (на десктопе)
    $options[] = [
        'type' => 'text',
        'name' => 'max_width_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_MAX_WIDTH'),
        'value' => '640px',
        'size' => 8,
        'placeholder' => Loc::getMessage('USHAKOV_COOKIE_OPT_MAX_WIDTH_PLACEHOLDER'),
        'group' => 'POSITION'
    ];

    // Горизонтальный отступ
    $options[] = [
        'type' => 'text',
        'name' => 'offset_x_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_OFFSET_X'),
        'value' => '0px',
        'size' => 6,
        'placeholder' => Loc::getMessage('USHAKOV_COOKIE_OPT_OFFSET_X_PLACEHOLDER'),
        'group' => 'POSITION'
    ];

    // Вертикальный отступ
    $options[] = [
        'type' => 'text',
        'name' => 'offset_y_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_OFFSET_Y'),
        'value' => '7px',
        'size' => 6,
        'placeholder' => Loc::getMessage('USHAKOV_COOKIE_OPT_OFFSET_Y_PLACEHOLDER'),
        'group' => 'POSITION'
    ];

    $options[] = [
        'type' => 'text',
        'name' => 'z_index_' . $item['LID'],
        'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_ZINDEX'),
        'value' => '9999',
        'size' => 5,
        'group' => 'POSITION'
    ];
}

$options[] = [
    'type' => 'heading',
    'heading' => Loc::getMessage('USHAKOV_COOKIE_OPT_COMMON'),
    'group' => 'BEHAVIOR'
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
    'group' => 'BEHAVIOR'
];

$options[] = [
    'type' => 'text',
    'name' => 'days',
    'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_DAYS'),
    'value' => '365',
    'size' => '10',
    'group' => 'BEHAVIOR'
];

$options[] = [
    'type'  => 'custom',
    'name'  => 'delay_ms',
    'title' => Loc::getMessage('USHAKOV_COOKIE_OPT_DELAY_MS'),
    'html'  => '<input type="number" id="delay_ms" name="delay_ms" value="' .
        htmlspecialcharsbx(\Bitrix\Main\Config\Option::get("ushakov.cookie", "delay_ms", "0")) .
        '" min="0" step="100" style="width: 120px;">' .
        '<span style="margin-left:8px;color:#80868b;">мс (1000 мс = 1 сек.)</span>' .
        '<span id="delay_ms_hint" style="margin-left:12px;color:#80868b;"></span>',
    'group' => 'BEHAVIOR'
];

$options[] = [
    'type' => 'message',
    'message' => Loc::getMessage('USHAKOV_COOKIE_OPT_HELP_MESSAGE'),
    'group' => 'BEHAVIOR'
];

return [
    'edit1' => [
        'TAB_NAME' => Loc::getMessage('USHAKOV_COOKIE_TAB'),
        'TAB_TITLE' => Loc::getMessage('USHAKOV_COOKIE_TITLE'),
        'ICON' => '',
        'options' => $options,
    ],
];

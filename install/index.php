<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\SiteTable;

Loc::loadMessages(__FILE__);

if (class_exists('ushakov_cookie')) {
    return;
}

class ushakov_cookie extends CModule
{
    public $MODULE_ID = 'ushakov.cookie';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS = 'Y';

    public function __construct()
    {
        $arModuleVersion = [];

        require __DIR__ . '/version.php';

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

        $this->MODULE_NAME = Loc::getMessage('USHAKOV_COOKIE_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('USHAKOV_COOKIE_MODULE_DESCRIPTION');

        $this->PARTNER_NAME = Loc::getMessage('USHAKOV_COOKIE_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('USHAKOV_COOKIE_PARTNER_URI');
    }

    public function DoInstall()
    {
        global $APPLICATION, $step;

        if (!$this->isVersionD7()) {
            $APPLICATION->ThrowException(Loc::getMessage('USHAKOV_COOKIE_INSTALL_ERROR_VERSION'));
        }

        if ($step < 2) {
            $APPLICATION->IncludeAdminFile(GetMessage('USHAKOV_COOKIE_INSTALL_TITLE'), __DIR__ . '/step1.php');
        } elseif ($step == 2) {
            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallFiles();
            ModuleManager::registerModule($this->MODULE_ID);
        }
    }

    public function DoUninstall()
    {
        global $APPLICATION, $step, $obModule;

        if ($step < 2) {
            $APPLICATION->IncludeAdminFile(GetMessage('USHAKOV_COOKIE_UNINSTALL_TITLE'), __DIR__ . '/unstep1.php');
        } elseif ($step == 2) {
            $GLOBALS['CACHE_MANAGER']->CleanAll();
            ModuleManager::unRegisterModule($this->MODULE_ID);

            $this->UnInstallDB(['savedata' => $_REQUEST['savedata'] ?? false]);
            $this->UnInstallEvents();
            $this->UnInstallFiles();

            $obModule = $this;
            $APPLICATION->IncludeAdminFile(GetMessage('USHAKOV_COOKIE_INSTALL_TITLE'), __DIR__ . '/unstep2.php');
        }
    }

    public function InstallDB($arParams = [])
    {
        $defaultOptions = require __DIR__ . '/../options_conf.php';
        if (!is_array($defaultOptions) || !isset($defaultOptions['edit1']['options']) || !is_array($defaultOptions['edit1']['options'])) {
            return true;
        }

        foreach ($defaultOptions['edit1']['options'] as $option) {
            if (
                isset($option['value']) && $option['value'] &&
                isset($option['name']) && $option['name']
            ) {
                Option::set($this->MODULE_ID, $option['name'], $option['value']);
            }
        }

        return true;
    }

    public function UnInstallDB($arParams = [])
    {
        Option::delete($this->MODULE_ID);
        return true;
    }

    public function InstallEvents()
    {
        // Include module
        EventManager::getInstance()->registerEventHandler('main', 'OnProlog', $this->MODULE_ID);
        return true;
    }

    public function UnInstallEvents()
    {
        // Include module
        EventManager::getInstance()->unRegisterEventHandler('main', 'OnProlog', $this->MODULE_ID);
        return true;
    }

    /**
     * Копируем страницу пользовательского соглашения на выбранные при установке сайты
     *
     * @param array $arParams
     * @return bool
     * @throws Exception
     */
    public function InstallFiles(array $arParams = [])
    {
        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();
        $server = $context->getServer();

        // Копируем обязательные скрипты
        CopyDirFiles(__DIR__ . '/bitrix/', $server->getDocumentRoot() . '/bitrix/', true, true);

        // Если был выбран хотя бы один сайт
        if (is_array($request->getPost('site')) && count($request->getPost('site'))) {
            // Получаем имеющиеся сайты из базы
            $arSites = [];
            $res = SiteTable::getList([]);
            while ($item = $res->fetch()) {
                $arSites[$item['LID']] = $item;
            }
            // Копируем страницу соглашения на выбранные при установке сайты
            foreach ($request->getPost('site') as $lid) {
                if (isset($arSites[$lid])) {
                    $docRoot = $arSites[$lid]['DOC_ROOT'] ?: $server->getDocumentRoot();
                    $dest = $docRoot . $arSites[$lid]['DIR'];
                    CopyDirFiles(__DIR__ . '/public/', $dest);
                    // Активируем показ уведомления для тех сайтов которым копируется соглашение
                    Option::set($this->MODULE_ID, 'active_' . $lid, 'Y');
                }
            }
        }

        return true;
    }

    /**
     * Удаляем страницу пользовательского соглашения
     *
     * @return bool
     * @throws Exception
     */
    public function UnInstallFiles()
    {
        $context = Application::getInstance()->getContext();
        $server = $context->getServer();

        // Удаляем скрипты
        DeleteDirFilesEx('bitrix/css/ushakov.cookie/');
        DeleteDirFilesEx('bitrix/images/ushakov.cookie/');
        DeleteDirFilesEx('bitrix/js/ushakov.cookie/');
        DeleteDirFiles(__DIR__ . '/bitrix/tools/', $server->getDocumentRoot() . '/bitrix/tools/');

        // Удаляем страницу соглашение
        $res = SiteTable::getList([]);
        while ($item = $res->fetch()) {
            $docRoot = $item['DOC_ROOT'] ?: $server->getDocumentRoot();
            DeleteDirFiles(__DIR__ . '/public', $docRoot . $item['DIR']);
        }

        return true;
    }

    private function isVersionD7()
    {
        return CheckVersion(ModuleManager::getVersion('main'), '14.00.00');
    }
}

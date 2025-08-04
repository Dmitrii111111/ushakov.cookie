<?php

IncludeModuleLangFile(__FILE__);

?>

<form action="<?= $APPLICATION->GetCurPage(); ?>" method="post">
  <?= bitrix_sessid_post(); ?>
  <p><?= \Bitrix\Main\Localization\Loc::getMessage('USHAKOV_COOKIE_INSTALL_P1')?></p>
  <p style="font-weight: bold"><?= \Bitrix\Main\Localization\Loc::getMessage('USHAKOV_COOKIE_INSTALL_P2') ?></p>
  <?php
  $res = \Bitrix\Main\SiteTable::getList([]);
  while ($item = $res->fetch()):
  ?>
  <label style="display: flex; line-height: 23px; user-select: none; cursor: pointer">
    <input name="site[]" value="<?= $item['LID'] ?>" type="checkbox" style="margin-right: 5px"<?=$item['DEF']==='Y'?' checked':''?>>
    <?= '[' . $item['LID'] . '] ' . $item['NAME']?>
  </label>
  <?php
  endwhile;
  ?>

  <br>
  <input type="submit" name="inst" value="<?= GetMessage('USHAKOV_COOKIE_INSTALL_BUTTON') ?>">

  <input type="hidden" name="lang" value="<?= LANG ?>">
  <input type="hidden" name="id" value="ushakov.cookie">
  <input type="hidden" name="install" value="Y">
  <input type="hidden" name="step" value="2">
</form>

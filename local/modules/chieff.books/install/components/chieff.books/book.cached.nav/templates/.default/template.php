<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// Включаем голосование "за" технологии композитный сайт
$this->setFrameMode(true);
if (!empty($arResult["ITEMS"])): ?>
    <? foreach ($arResult["ITEMS"] as $item): ?>
        <pre><? print_r($item); ?></pre>
    <?php endforeach; ?>
    <?php
        $APPLICATION->IncludeComponent(
            "bitrix:main.pagenavigation",
            ".default",
            array(
                'NAV_TITLE'   => 'Элементы',
                "NAV_OBJECT"  => $arResult["NAV"],
                "SEF_MODE" => "N",
            ),
            // $this относится уже к объекту шаблона, доступ к объекту компонента осуществляется через $component
            // Т.к. в шаблоне у нас вложенный компонент, то нужно обязательно передать компонент родителя
            // Если не укажем, то при кешировании не будут подгружены файлы style.css, script.js и т.п.
            $component
        );
    ?>
<?php endif; ?>
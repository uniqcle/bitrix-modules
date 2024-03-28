<?
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

// пространство имен для подключений ланговых файлов
use Bitrix\Main\Localization\Loc;

// подключение ланговых файлов
Loc::loadMessages(__FILE__);

// сформируем верхний пункт меню
$aMenu = array(
    // пункт меню в разделе Контент
    'parent_menu' => 'global_menu_services',
    // сортировка
    'sort' => 1,
    // название пункта меню
    'text' => "Модули Эйч Маркетинг",
    // идентификатор ветви
    "items_id" => "menu_webforms",
    // иконка
    "icon" => "form_menu_icon",
);

// дочерния ветка меню
$aMenu["items"][] =  array(
    // название подпункта меню
    'text' => 'Страница модуля',
    // ссылка для перехода
    'url' => 'hmarketing.php?lang=' . LANGUAGE_ID
);

// дочерния ветка меню
$aMenu["items"][] =  array(
    // название подпункта меню
    'text' => 'Админка модуля',
    // ссылка для перехода
    'url' => 'settings.php?lang=ru&mid=hmarketing.d7'
);

// возвращаем основной массив $aMenu
return $aMenu;

<?php

if($APPLICATION->GetGroupRight("uniqcle.orm")>"D"){

	$aMenu = array(
	"parent_menu" => "global_menu_content",  // Поместим в раздел "Сервис"
        "sort"        => 100,                    // Относительный "вес" пункта меню для сортировки.
        "url"         => "uniqcle_books_list.php?lang=".LANGUAGE_ID,  // ссылка на пункте меню
        "more_url"    => "", // Список дополнительных URL, по которым данный пункт меню должен быть подсвечен.
        "text"        => "Uniqcle модуля - Модуль книг", // текст пункта меню
        "title"       => "Скелет модуля - Модуль книг", // текст всплывающей подсказки
        "icon"        => "form_menu_icon", // CSS-класс иконки пункта меню (малая иконка)
        "page_icon"   => "form_page_icon", // CSS-класс иконки пункта меню для вывода на странице индекса (класс увеличенной иконки) (большая иконка)
        "module_id"   => "chieff.books",   // Идентификатор модуля, к которому относится меню.
        "dynamic"     => false,            // Флаг, показывающий, должна ли ветвь, начинающаяся с текущего пункта, подгружаться динамически.
        "items_id"    => "uniqcle.orm",   // Идентификатор ветви меню. Используется для динамического обновления ветви.
        "items"       => array(),          // Список дочерних пунктов меню. Представляет собой массив, каждый элемент которого является ассоциативным массивом аналогичной структуры. (сформируем ниже)
    );

    // массив каждого пункта формируется аналогично
    $aMenu["items"][] =  array(
	    "title" => "Список",
	    "text" => "Список",
	    "url"  => "uniqcle_books_list.php?lang=".LANGUAGE_ID,
	    "icon" => "form_menu_icon",
	    "page_icon" => "form_page_icon",
	    // Может принимать остальные значения, как указано сверху
    );
    $aMenu["items"][] =  array(
	    "title" => "Добавить",
	    "text" => "Добавить",
	    "url"  => "uniqcle_books_edit.php?lang=".LANGUAGE_ID,
	    "icon" => "form_menu_icon",
	    "page_icon" => "form_page_icon",
	    // Может принимать остальные значения, как указано сверху
    );

	$aMenu["items"][] =  array(
		"title" => "Настройки",
		"text" => "Настройки",
		"url"  => "uniqcle_data.php?lang=".LANGUAGE_ID,
		"icon" => "form_menu_icon",
		"page_icon" => "form_page_icon",
		// Может принимать остальные значения, как указано сверху
	);

    return $aMenu;
}
// если нет доступа, вернем false
return false;
<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
// пространство имен для загрузки необходимых файлов, классов, модулей
use Bitrix\Main\Loader;
// пространство имен для работы с параметрами модулей хранимых в базе данных
use Bitrix\Main\Config\Option;
// подключение ланговых файлов
Loc::loadMessages(__FILE__);

// Обязательное условие, наличие данной переменной, должна называться именно так, т.к. некоторые методы старого ядра завязаны на ней и рассчитывают, что она будет присутствовать
$module_id = "uniqcle.orm";

// Получаем права пользователя для модуля, если они меньше редактирования настроек, то открываем форму авторизации
if ($APPLICATION->GetGroupRight($module_id) < "S")
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));

// Подключаем наш модуль
Loader::includeModule($module_id);

// Получение запроса из контекста для обработки данных, которые придут с форм
$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();

// Массив вкладок и полей настроек модуля
$aTabs = Array(
	array(
		"DIV"   => "edit1", // Идентификатор вкладки (используется для javascript)
		"TAB"   => Loc::getMessage("UNIQCLE_MAIN_TAB_NAME"), // Название вкладки
		"TITLE" => Loc::getMessage("UNIQCLE_MAIN_TAB_TITLE"),    // Заголовок и всплывающее сообщение вкладки
		// Массив настроек опций для вкладки
		"OPTIONS" => Array(
			Array(
				"field_text", // Имя поля для хранения в бд
				Loc::getMessage("UNIQCLE_BOOKS_FIELD_TEXT_TITLE"), // Заголовок поля для вывода
				"", // Значение по умолчанию (не обязательно получать уже установленное значение для вывода, т.к. метод далее может это делать автоматически)
				Array(
					"textarea", // Тип поля
					10, // Ширина
					50  // Высота
				)
			),
			Array(
				"field_line", // Имя поля для хранения в бд
				Loc::getMessage("UNIQCLE_BOOKS_FIELD_LINE_TITLE"), // Заголовок поля для вывода
				"", // Значение по умолчанию (не обязательно получать уже установленное значение для вывода, т.к. метод далее может это делать автоматически)
				Array(
					"text", // Тип поля
					10 // Ширина
				)
			),

			"Это строка с подсветкой. Используется для разделения настроек в одной вкладке",

			array('field_logo',
			      Loc::getMessage("UNIQCLE_LOGO_FILE"),
			      //Option::get($module_id, "field_logo"),
			      array('file'),
			),


			// ПРИМЕЧАНИЕ (NOTE)
			array(
				"note" => "Это уведомление с подсветкой.
			Можно использовать для информирования пользователя.
			Если тут расположен очень, очень, очень длинный текст,
			то выглядит очень даже не дурно :)"
			),

			// ПАРОЛЬ (PASSWORD)
			// 4 параметр: тип (input password), величина(парамерт size)
			array(
				"field_password",
				"Какой-нибудь пароль",
				"example123456",
				array(
					"password",
					"12"
				)
			),

			// ФЛАГ (CHECKBOX)
			// Если значение по умолчанию Y, то галочка стоит, иначе - нет.
			// 4 параметр: тип (input checkbox), бесполезен, дополнительный код в теге input.
			array(
				"field_checkbox",
				"Флаг",
				"Y",
				array(
					"checkbox",
					"",
					"disabled"
				)
			),

			// СЕЛЕКТ (SELECT)
			// Значение по умолчанию - ключ в массиве.
			// 4 параметр: тип (select), ассоциативный массив значений.
			array(
				"field_select",
				"Селект",
				"key_2",
				array(
					"selectbox",
					array(
						"key_1" => "значение 1",
						"key_2" => "значение 2",
						"key_3" => "значение 3"
					)
				)
			),

			// МУЛЬТИСЕЛЕСТ (MULTISELECT)
			// Значение по умолчанию - ключ в массиве(список перечисляется через запятую).
			// 4 параметр: тип (select), ассоциативный массив значений.
			array(
				"field_multiselect",
				Loc::getMessage("UNIQCLE_BOOKS_FIELD_LIST_TITLE"),
				"key_2,key_3",
				array(
					"multiselectbox",
					array(
						"key_1" => "значение 1",
						"key_2" => "значение 2",
						"key_3" => "значение 3"
					)
				)
			),

			// СТАТИЧЕСКИЙ ТЕКСТ/HTML (STATIC TEXT/HTML)
			// Просто выводит значение параметра.
			// 4 параметр: тип (statictext, statichtml).
			array(
				"OPTION_NAME_7",
				"Блок статического текста",
				"Значение по умолчанию",
				array(
					"statictext"
				)
			),

		),
	),
	array(
		"DIV"   => "edit2", // Идентификатор вкладки (используется для javascript)
		"TAB"   => Loc::getMessage("MAIN_TAB_RIGHTS"),      // Название вкладки (из основного языкового файла битрикс)
		"TITLE" => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS") // Заголовок и всплывающее сообщение вкладки (из основного языкового файла битрикс)
	)
);

// Если пришел запрос на обновление и сессия активна, то обходим массив созданных полей
if ($request->isPost() && $request["Update"] && check_bitrix_sessid()) {
	foreach ($aTabs as $aTab) {
		foreach ($aTab["OPTIONS"] as $arOption) {

			// Существуют строки с подстветкой, которые не нужно обрабатывать, поэтому пропускаем их
			if (!is_array($arOption))
				continue;
			if ($arOption["note"])
				continue;

			// Имя настройки
			$optionName = $arOption[0];
			// Значение настройки, которое пришло в запросе
			$optionValue = $request->getPost($optionName);
			// Установка значения по айди модуля и имени настройки
			// Хранить можем только текст, значит если приходит массив, то разбиваем его через запятую
			Option::set($module_id, $optionName, is_array($optionValue) ? implode(",", $optionValue) : $optionValue);
		}
	}
}

// Создаем объект класса AdminTabControl
$tabControl = new CAdminTabControl('tabControl', $aTabs);

// Начинаем формирование формы
$tabControl->Begin();

?>
<form method="post" name="chieff_books_settings" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($request["mid"])?>&lang=<?=$request["lang"]?>">
	<?
	echo bitrix_sessid_post();
	foreach ($aTabs as $aTab):
		if ($aTab["OPTIONS"]):
			// Указываем начало формирования первой вкладки
			$tabControl->BeginNextTab();
			// Отрисовываем поля по заданному массиву (автоматически подставляет значения, если они были заданы)
			__AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
		endif;
	endforeach;

	// Т.к. цикл не затрагивает вкладку прав (у неё нет опций), то вызовем её отдельно
	// Если в install/index.php не определены свои параметры прав, то выведутся значения по умолчанию
	$tabControl->BeginNextTab();
	require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php"; // Именно в этом вызове используется $module_id

	// Отрисуем кнопки
	$tabControl->Buttons();
	?>
	<input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>">
	<input type="reset" name="reset" value="<?=GetMessage("MAIN_RESET")?>">
</form>

<?php

// Заканчиваем формирование формы
$tabControl->End();

// Пример получения значения из настроек
// $op = \Bitrix\Main\Config\Option::get(
//    "chieff.books", // ID модуля. Обязательный.
//    "field_text", // Имя параметра. Обязательный.
//     "", // Возвращается значение по умолчанию, если значение не задано. Значение по умолчанию. Если default_value не задан, то значение для default_value будет браться из массива с именем ${module_id."_default_option"} заданного в файле /bitrix/modules/module_id/default_option.php.
//    false // ID сайта, если значение параметра различно для разных сайтов.
// );
// \Bitrix\Main\Config\Option::getForModule(chieff.books); // Вернет все настройки
// Остальные команды https://dev.1c-bitrix.ru/api_d7/bitrix/main/config/option/index.php

?>


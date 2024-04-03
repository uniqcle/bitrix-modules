<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';
// пространство имен для автозагрузки модулей
use \Bitrix\Main\Loader;

// получим права доступа текущего пользователя на модуль
$POST_RIGHT = $APPLICATION->GetGroupRight("uniqcle.orm");

// если нет прав - отправим к форме авторизации с сообщением об ошибке
if ($POST_RIGHT == "D") {
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

// вывод заголовка
$APPLICATION->SetTitle("Настройк модуля Uniqcle");

// подключаем языковые файлы
IncludeModuleLangFile(__FILE__);

$aTabs = array(
	array(
		// название вкладки в табах
		"TAB" => "Параметры",
		// заголовок и всплывающее сообщение вкладки
		"TITLE" => "Параметры вывода uniqcle"
	)
);

// отрисовываем форму, для этого создаем новый экземпляр класса CAdminTabControl, куда и передаём массив с настройками
$tabControl = new CAdminTabControl(
	"tabControl",
	$aTabs
);


// подключаем модуль для того что бы был видем класс ORM
Loader::includeModule("uniqcle.orm");

if (
	// проверка метода вызова страницы
	$REQUEST_METHOD == "POST"
	&&
	// проверка нажатия кнопок Сохранить
	$save != ""
	&&
	// проверка наличия прав на запись для модуля
	$POST_RIGHT == "W"
	&&
	// проверка идентификатора сессии
	check_bitrix_sessid()
){
	// класс таблицы в базе данных
	$dataTable = new \Uniqcle\ORM\DataTable;

	// обработка данных формы
	$arFields = array(
		"ACTIVE" => ($ACTIVE == '') ? 'N' : 'Y',
		"SITE" => json_encode($SITE),
		"LINK" => htmlspecialchars($LINK),
		"LINK_PICTURE" => htmlspecialchars($LINK_PICTURE),
		"ALT_PICTURE" => htmlspecialchars($ALT_PICTURE),
		"EXCEPTIONS" => $EXCEPTIONS == "" ? "" : trim(htmlspecialchars($EXCEPTIONS)),
		"DATE" => new \Bitrix\Main\Type\DateTime(date("d.m.Y H:i:s")),
		"TARGET" => htmlspecialchars($TARGET),
	);

	// обновляем запись
	$res = $dataTable->Update(1, $arFields);

	// если обновление прошло успешно
	if ($res->isSuccess()) {
		// перенаправим на новую страницу, в целях защиты от повторной отправки формы нажатием кнопки Обновить в браузере
		if ($save != "") {
			// если была нажата кнопка Сохранить, отправляем обратно на форму
			LocalRedirect("/bitrix/admin/uniqcle_data.php?mess=ok&lang=" . LANG);
		}
	}
	// если обновление прошло не успешно
	if (!$res->isSuccess()) {
		// если в процессе сохранения возникли ошибки - получаем текст ошибки
		if ($e = $APPLICATION->GetException())
			$message = new CAdminMessage("Ошибка сохранения: ", $e);
		else {
			$mess = print_r($res->getErrorMessages(), true);
			$message = new CAdminMessage("Ошибка сохранения: " . $mess);
		}
	}
}

// подготовка данных для формы, полученные из БД данные будем сохранять в переменные с префиксом str_
$result = \Uniqcle\ORM\DataTable::GetByID(1);

if ($result->getSelectedRowsCount()) {
	//debug($dataTable);

	$dataTable = $result->fetch();
	$str_ACTIVE = $dataTable["ACTIVE"];
	$str_SITE = json_decode($dataTable["SITE"]);
	$str_LINK = $dataTable["LINK"];
	$str_LINK_PICTURE = $dataTable["LINK_PICTURE"];
	$str_ALT_PICTURE = $dataTable["ALT_PICTURE"];
	$str_EXCEPTIONS = $dataTable["EXCEPTIONS"];
	$str_TARGET = $dataTable["TARGET"];
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';

// eсли есть сообщения об успешном сохранении, выведем их
if ($_REQUEST["mess"] == "ok") {
	CAdminMessage::ShowMessage(array("MESSAGE" => "Сохранено успешно", "TYPE" => "OK"));
}
// eсли есть сообщения об не успешном сохранении, выведем их
if ($message) {
	echo $message->Show();
}
// eсли есть сообщения об не успешном сохранении от ORM, выведем их
if ($dataTable->LAST_ERROR != "") {
	CAdminMessage::ShowMessage($dataTable->LAST_ERROR);
}

?>

<form method="POST" action="<?= $APPLICATION->GetCurPage() ?>" ENCTYPE="multipart/form-data" name="post_form">

<?
// проверка идентификатора сессии
echo bitrix_sessid_post();

// отобразим заголовки закладок
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
	<tr>
		<td width="40%"><?= "Активность" ?></td>
		<td width="60%"><input type="checkbox" name="ACTIVE" value="Y" <? if ($str_ACTIVE == "Y") echo " checked" ?>></td>
	</tr>
	<tr>
		<td>
			<label for="SITE"><?= "Сайты" ?></label>
		</td>
		<td>
			<select name="SITE[]" multiple>
				<option value="s1" <?= in_array('s1', $str_SITE) ? 'selected' : '' ?>>Для России</option>
				<option value="kz" <?= in_array('kz', $str_SITE) ? 'selected' : '' ?>>Для Казахстана</option>
			</select>
		</td>
	</tr>
	<tr>
		<td width="40%"><?= "Ссылка для перехода" ?></td>
		<td width="60%"><input type="text" name="LINK" value="<?= $str_LINK ?>" /></td>
	</tr>
	<tr>
		<td width="40%"><?= "Ссылка на картинку" ?></td>
		<td width="60%"><input type="text" name="LINK_PICTURE" value="<?= $str_LINK_PICTURE ?>" /></td>
	</tr>
	<tr>
		<td width="40%"><?= "Alt картинки" ?></td>
		<td width="60%"><input type="text" name="ALT_PICTURE" value="<?= $str_ALT_PICTURE ?>" /></td>
	</tr>
	<tr>
		<td width="40%"><?= "Исключения" ?></td>
		<td width="60%"><textarea cols="50" rows="15" name="EXCEPTIONS"><?= $str_EXCEPTIONS ?></textarea></td>
	</tr>
	<tr>
		<td width="40%"><?= "Значение TARGET (self/blank)" ?></td>
		<td width="60%"><input type="text" name="TARGET" value="<?= $str_TARGET ?>" /></td>
	</tr>

<?
// выводит стандартные кнопки отправки формы
$tabControl->Buttons();
?>

	<input class="adm-btn-save" type="submit" name="save" value="Сохранить настройки" />

<?
// завершаем интерфейс закладки
$tabControl->End();

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
?>
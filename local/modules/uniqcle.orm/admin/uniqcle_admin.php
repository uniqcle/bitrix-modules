<?
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';

// пространство имен для автозагрузки модулей
use \Bitrix\Main\Loader;

// получим права доступа текущего пользователя на модуль
$POST_RIGHT = $APPLICATION->GetGroupRight("hmarketing.d7");

// если нет прав - отправим к форме авторизации с сообщением об ошибке
if ($POST_RIGHT == "D") {
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

echo 'test uniqcle page';


require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
?>
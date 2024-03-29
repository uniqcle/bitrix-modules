<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;
Loc::loadMessages(__FILE__);

class Uniqcle_ORM extends CModule{

	public $MODULE_ID;
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $PARTNER_NAME;
	public $PARTNER_URI;
	public $SHOW_SUPER_ADMIN_GROUP_RIGHTS;
	public $MODULE_GROUP_RIGHTS;
	public $errors;
	public $arResponse = [
		"STATUS" => true,
		"MESSAGE" => ""
	];

	public function setResponse($status, $message = ""){
		$this->arResponse["STATUS"] = $status;
		$this->arResponse["MESSAGE"] = $message;
	}

	function __construct(){
		$arModuleVersion = array();

		include_once(__DIR__ . '/version.php');
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_ID = "uniqcle.orm";
		$this->MODULE_NAME = "Пример модуля ORM D7 (uniqcle)";
		$this->MODULE_DESCRIPTION = "Можно использовать как основу для разработки других модулей.";
		$this->PARTNER_NAME = "uniqcle";
		$this->PARTNER_URI = "https://uniqcle.ru";
		$this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = "Y";
		$this->MODULE_GROUP_RIGHTS="Y";
	}

	function DoInstall(){
		// Пример с установкой в один шаг
		global $APPLICATION;
		ModuleManager::RegisterModule($this->MODULE_ID);

		if (Loader::includeModule($this->MODULE_ID)){

			//$this->installFiles();
			//$this -> installFilesLocal();

			$this->installDB();

		}
	}

	function DoUnInstall(){
		global $APPLICATION;

		$this->unInstallFiles();
		//$this->unInstallFilesLocal();

		ModuleManager::UnRegisterModule($this->MODULE_ID);
	}

	function installDB(){

		// Создаем таблицу BookTable
		$connectionName = \Uniqcle\ORM\BookTable::getConnectionName();
		$instanceDbTable = Base::getInstance("\Uniqcle\ORM\BookTable");
		$tableName = $instanceDbTable -> getDBTableName();

		if(!Application::getConnection($connectionName)->isTableExists($tableName) ){
			$instanceDbTable ->createDbTable();
		}

		return true;
	}

	function unInstallDB(){
		return true;
	}

	function installFiles(){
		$this->unInstallFiles();
		$resMsg = "";

		$res = CopyDirFiles(
			__DIR__ . "/admin",
			$_SERVER['DOCUMENT_ROOT']. "/bitrix/admin",
			true, // Перезаписывает файлы
			true // Копирует рекурсивно
		);
		$res = CopyDirFiles(
			__DIR__ . "/components",
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/components',
			true,
			true
		);
		$res = CopyDirFiles(
			__DIR__ . '/files',
			$_SERVER['DOCUMENT_ROOT'] . '/',
			true,
			true
		);
		if(!$res)
			$resMsg = ($resMsg) ? $resMsg . "; " . Loc::getMessage("UNIQCLE_ORM_INSTALL_ERROR_FILES_COM") : Loc::getMessage("UNIQCLE_ORM_INSTALL_ERROR_FILES_COM");
		if ($resMsg) {
			$this->setResponse(false, $resMsg);
			return false;
		}
		$this->setResponse(true);
		return true;
	}

	// Опциональная установка в папку local
	function installFilesLocal() {
		$this->unInstallFiles();
		$resMsg = "";
		$res = CopyDirFiles(
			__DIR__ . "/admin",
			$_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin",
			true,
			true
		);
		if (!$res)
			$resMsg = Loc::getMessage("UNIQCLE_ORM_INSTALL_ERROR_FILES_COM");

		if (!is_dir($_SERVER["DOCUMENT_ROOT"] . "/local/components"))
			mkdir($_SERVER["DOCUMENT_ROOT"] . "/local/components", 0777, true);

		$res = CopyDirFiles(
			__DIR__ . "/components",
			$_SERVER["DOCUMENT_ROOT"] . "/local/components",
			true,
			true
		);
		if (!$res)
			$resMsg = ($resMsg) ?
				$resMsg . "; " . Loc::getMessage("UNIQCLE_ORM_INSTALL_ERROR_LOCAL_FILES_COM") :
				Loc::getMessage("UNIQCLE_ORM_INSTALL_ERROR_LOCAL_FILES_COM");
		if ($resMsg) {
			$this->setResponse(false, $resMsg);
			return false;
		}
		$this->setResponse(true);
		return true;
	}

	function unInstallFiles(){
		// удалим файлы из папки в битрикс на страницы админки, удаляет одноименные файлы из одной директории,
		// которые были найдены в другой директории, функция не работает рекурсивно
		DeleteDirFiles(
			__DIR__ . "/admin",
			$_SERVER['DOCUMENT_ROOT'] . "/bitrix/admin"
		);
		if(is_dir($_SERVER['DOCUMENT_ROOT']. "/bitrix/components/" . $this->MODULE_ID)){
			// удаляет папка из указанной директории, функция работает рекурсивно
			DeleteDirFilesEx(
				"/bitrix/components/" . $this->MODULE_ID
			);
		}
		DeleteDirFiles(
			__DIR__ . "/files",
			$_SERVER['DOCUMENT_ROOT'] . '/'
		);
		return true;
	}

	// Опциональное удаление из папки local
	function unInstallFilesLocal() {
		$res = true;
		$resMsg = "";

		DeleteDirFiles(
			__DIR__ . "/admin",
			$_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin"
		);

		if (is_dir($_SERVER["DOCUMENT_ROOT"] . "/local/components" . "/" . $this->MODULE_ID))
			$res = DeleteDirFilesEx("/local/components/" . $this->MODULE_ID);
		if (!$res)
			$resMsg = Loc::getMessage("UNIQCLE_ORM_UNINSTALL_ERROR_LOCAL_FILES_COM");

		if ($resMsg) {
			$this->setResponse(false, $resMsg);
			return false;
		}
		$this->setResponse(true);
		return true;
	}
}
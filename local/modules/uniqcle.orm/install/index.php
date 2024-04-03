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

			$this->installFiles();
			//$this -> installFilesLocal();

			$this->installDB();
			$this->addData();

		}
	}

	function DoUnInstall(){
		global $APPLICATION;

		$this->unInstallFiles();
		//$this->unInstallFilesLocal();
		$this->unInstallDB();

		ModuleManager::UnRegisterModule($this->MODULE_ID);
	}

	function installDB(){

		// Создаем таблицу BookTable
		$connectionName = \Uniqcle\ORM\BookTable::getConnectionName();
		$instanceBookTable = Base::getInstance("\Uniqcle\ORM\BookTable");
		$tableName = $instanceBookTable -> getDBTableName();

		if(!Application::getConnection($connectionName)->isTableExists($tableName) ){
			$instanceBookTable ->createDbTable();
		}

		// Создаем таблицу AuthorTable
		$connectionName = \Uniqcle\ORM\AuthorTable::getConnectionName();
		$instanceAuthorTable = Base::getInstance("\Uniqcle\ORM\AuthorTable");
		$authorName = $instanceAuthorTable-> getDBTableName();
		if(!Application::getConnection($connectionName)->isTableExists($authorName)){
			$instanceAuthorTable->createDbTable();
		}

		// Создаем таблицу DataTable
		$connectionDataName = \Uniqcle\ORM\DataTable::getConnectionName();
		$instanceDataTable = Base::getInstance("\Uniqcle\ORM\DataTable");
		$dataName = $instanceDataTable-> getDBTableName();
		if(!Application::getConnection($connectionDataName)->isTableExists($dataName)){
			$instanceDataTable->createDbTable();
		}

		return true;
	}

	function unInstallDB(){
		Loader::includeModule($this->MODULE_ID);

		$connectionName = \Uniqcle\ORM\BookTable::getConnectionName();
		$bookInstance = Base::getInstance("Uniqcle\ORM\BookTable");
		$tableBookName = $bookInstance->getDBTableName();
		Application::getConnection($connectionName)->queryExecute('DROP TABLE IF EXISTS ' . $tableBookName);


		$connectionAuthorName = \Uniqcle\ORM\AuthorTable::getConnectionName();
		$authorInstance = Base::getInstance("Uniqcle\ORM\AuthorTable");
		$tableAuthorName = $authorInstance->getDBTableName();
		Application::getConnection($connectionAuthorName)->queryExecute('DROP TABLE IF EXISTS ' .$tableAuthorName);


		$connectionDataName = \Uniqcle\ORM\DataTable::getConnectionName();
		$dataInstance = Base::getInstance("Uniqcle\ORM\DataTable");
		$tableDataName = $dataInstance->getDBTableName();
		Application::getConnection($connectionDataName)->queryExecute('DROP TABLE IF EXISTS ' .$tableDataName);

		Option::delete($this->MODULE_ID);
	}

	// заполнение таблиц тестовыми данными
	function addData()
	{
		// подключаем модуль для видимости ORM класса
		Loader::includeModule($this->MODULE_ID);

		// добавляем запись в таблицу БД
		\Uniqcle\ORM\DataTable::add(
			array(
				"ACTIVE" => "N",
				"SITE" => '["s1"]',
				"LINK" => " ",
				"LINK_PICTURE" => "/bitrix/components/uniqcle.orm/img/banner.jpg",
				"ALT_PICTURE" => " ",
				"EXCEPTIONS" => " ",
				"DATE" => new \Bitrix\Main\Type\DateTime(date("d.m.Y H:i:s")),
				"TARGET" =>  "self",
				"AUTHOR_ID" =>  "1",
			)
		);

		// добавляем запись в таблицу БД
		\Uniqcle\ORM\AuthorTable::add(
			array(
				"NAME" => "Иван",
				"LAST_NAME" => "Иванов",
			)
		);

		// для успешного завершения, метод должен вернуть true
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
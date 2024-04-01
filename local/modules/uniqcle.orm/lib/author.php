<?php
namespace Uniqcle\ORM;

use \Bitrix\Main\Entity;

class AuthorTable extends Entity\DataManager{

	public static function getConnectionName(){
		return "default";
	}

	public static function getTableName(){
		return "uniqcle_author";
	}

	public static function getMap(){
		return [
			new Entity\IntegerField(
				"ID",
				[
					"primary" => true,
					"autocomplete" => true
				]
			),
			new Entity\StringField(
				"NAME",
				[
					"required" => true
				]
			),
			new Entity\StringField("LAST_NAME")
		];
	}
}
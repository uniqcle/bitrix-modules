<?php
namespace Uniqcle\ORM;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Type;

class BookTable extends Entity\DataManager{

	public static function getTableName(){
		return 'uniqcle_books_book_table';
	}

	public static function getConnectionName(){
		return "default";
	}

	public static function getMap(){
		return array(
			new Entity\IntegerField(
				"ID",
				array(
					"primary" => true,
					"autocomplete" => true
				)
			),
			new Entity\StringField(
				"NAME",
				array(
					"required" => true
				)
			),
			new Entity\StringField("LAST_NAME")
		);
	}
}
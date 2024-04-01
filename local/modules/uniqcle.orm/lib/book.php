<?php
namespace Uniqcle\ORM;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Type;

class BookTable extends Entity\DataManager{

	public static function getTableName(){
		return 'uniqcle_book';
	}

	public static function getConnectionName(){
		return "default";
	}

	public static function getMap(){
		return Array(
			new Entity\IntegerField(
				"ID",
				[
					"primary" => true,
					"autocomplete" => true
				]
			),
			new Entity\BooleanField(
				"ACTIVE",
				[
					"values" => Array("N", "Y")
				]
			),
			new Entity\EnumField(
				"TYPE",
				[
					"values" => Array('Техническая литература', 'Художественная литература', 'Научная литература'),
				]
			),
			new Entity\StringField(
				"NAME",
				[
					"required" => true
				]
			),
			new Entity\IntegerField(
				"RELEASED",
				[
					"required" => true
				]
			),
			new Entity\StringField(
				"ISBN",
				[
					"required" => true,
					"column_name" => "ISBNCODE",
					"validation" => function(){
						return [
							new Entity\Validator\Unique,
							function($value, $primary, $row, $field){
								// value - значение поля
								// primary - массив с первичным ключом, в данном случае [ID => 1]
								// row - весь массив данных, переданный в ::add или ::update
								// field - объект валидируемого поля - Entity\StringField('ISBN', ...)
								$clean = str_replace(['-', ' '], '', $value);
								if (preg_match("/^\d{1,13}$/", $clean))
									return true;
								else
									return "Код ISBN должен содержать не более 13 цифр, разделенных дефисом или пробелами";
							}
						];
					}
				]
			),
			new Entity\IntegerField("AUTHOR_ID"),
			new Entity\ReferenceField(
				"AUTHOR",
				"\Uniqcle\Book\AuthorTable",
				array("=this.AUTHOR_ID" => "ref.ID")
			),
			new Entity\DatetimeField("TIME_ARRIVAL"),
			new Entity\TextField("DESCRIPTION"),
			new Entity\ExpressionField(
				"AGE_YEAR",
				"YEAR(CURDATE())-%s",
				Array("RELEASED")
			)
		);
	}

}
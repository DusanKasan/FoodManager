<?php
namespace FoodsModule;

/**
 * Operations on foodsPictures
 *
 * @package FoodsModule
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class FoodsPicturesModel extends \BaseTableAccessModel
{
	/**
	 * @var string 
	 */
	protected $table = 'foods_pictures';
	
	/**
	 * Assigns uploaded file (picture) to food
	 * 
	 * @param integer $id_food
	 * @param integer $id_file
	 * 
	 * @return \Nette\Database\Table\ActiveRow
	 * 
	 * @throws \DatabaseException  
	 */
	public function addPictureToFood($id_food, $id_file)
	{
		$data = array(
			'id_food' => $id_food,
			'id_uploaded_file' => $id_file,
		);
		
		$result = $this->database->table($this->table)->insert($data);
		if (FALSE === $result) {
			throw new \DatabaseException("Unable to insert to foods_pictures using data: " . var_export($data, TRUE));
		}
		
		return $result;
	}
}
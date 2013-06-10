<?php
namespace FoodsModule;

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
	
	/**
	 * Deletes picture with $id_food_picture
	 * 
	 * @param type $id_food_picture
	 * 
	 * @throws \DatabaseException 
	 */
	public function deleteFoodPicture($id_food_picture)
	{
		$result = $this->database->table($this->table)->get($id_food_picture)->delete();
		if (FALSE === $result) {
			throw new \DatabaseException("Unable to delete from foods_pictures row with id {$id_food_picture}");
		}
	}
}
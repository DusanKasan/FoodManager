<?php
namespace FoodsModule;

class FoodsModel extends \BaseTableAccessModel
{
	/**
	 * @var string 
	 */
	protected $table = 'foods';

	/**
	 * Get subset of foods using filter
	 * 
	 * @param FoodsFilter $filter
	 * 
	 * @return \Nette\Database\Table\Selection 
	 */
	public function getFoods(FoodsFilter $filter)
	{		
		$search = $filter->get('search', FALSE);
		$order = $filter->get('order_by', 'added_at DESC');
		$filter->setDefault('is_finished', 1);
		
		$query = $this->database->table('foods')->where($filter->exportWhere());
		
		//TODO: Implement some search engine :)
		if ($search) {
			$fulltext_search = "lower(food) LIKE lower(?) OR lower(description) LIKE lower(?)";
			$query->where($fulltext_search, "%{$search}%", "%{$search}%");
		}
		
		$query->order($order);
		
		return $query;
	}
	
	/**
	 * Assigns uploaded file (image) to food
	 * 
	 * @param integer $id_food
	 * @param integer $id_file
	 * 
	 * @return \Nette\Database\Table\ActiveRow 
	 */
	public function addImageToFood($id_food, $id_file)
	{
		$data = array(
			'id_food' => $id_food,
			'id_uploaded_file' => $id_file,
		);
		return $this->database->table('foods_pictures')->insert($data);
	}
	
	/**
	 * Assigns tag to food
	 * 
	 * @param integer $id_food
	 * @param integer $id_tag
	 * 
	 * @return \Nette\Database\Table\ActiveRow 
	 */
	public function addTagToFood($id_food, $id_tag)
	{
		$data = array(
			'id_food' => $id_food,
			'id_tag' => $id_tag,
		);
		return $this->database->table('foods_tags')->insert($data);
	}
	
	/**
	 * Adds ingredient to food
	 * TODO: Write trigger to merge multiple quantities of the same ingredient ie.: 1cup + 1 cup = 2 cup
	 * 
	 * @param integer $id_food
	 * @param integer $id_ingredient
	 * @param string $amount
	 * 
	 * @return \Nette\Database\Table\ActiveRow 
	 */
	public function addIngredientToFood($id_food, $id_ingredient, $amount = '')
	{
		$data = array(
			'id_food' => $id_food,
			'id_ingredient' => $id_ingredient,
			'amount' => $amount,
		);
		return $this->database->table('foods_ingredients')->insert($data);
	}
	
	/**
	 * Deletes all tags from food
	 * 
	 * @param type $id_food
	 * 
	 * @return integer Number of affected rows 
	 */
	public function deleteAllTagsFromFood($id_food)
	{
		return $this->database->table('foods_tags')->where('id_food', $id_food)->delete();
	}
	
	/**
	 * Deletes all ingredient from food
	 * 
	 * @param integer $id_food
	 * 
	 * @return integer Number of affected rows 
	 */
	public function deleteAllIngredientsFromFood($id_food)
	{
		return $this->database->table('foods_ingredients')->where('id_food', $id_food)->delete();
	}
}
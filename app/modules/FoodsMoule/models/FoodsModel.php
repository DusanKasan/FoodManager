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
		
		$result = $this->database->table('foods_tags')->insert($data);
		if (FALSE === $result) {
			throw new \DatabaseException("Unable to insert to foods_tags using data: " . var_export($data, TRUE));
		}
		
		return $result;
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
		
		$result = $this->database->table('foods_ingredients')->insert($data);
		if (FALSE === $result) {
			throw new \DatabaseException("Unable to insert to foods_ingredients using data: " . var_export($data, TRUE));
		}
		
		return $result;
	}
	
	/**
	 * Deletes all tags from food
	 * 
	 * @param type $id_food
	 */
	public function deleteAllTagsFromFood($id_food)
	{
		$result = $this->database->table('foods_tags')->where('id_food', $id_food)->delete();
		if (FALSE === $result) {
			throw new \DatabaseException("Unable to delete all tags from food with id:{$id_food}");
		}
	}
	
	/**
	 * Deletes all ingredient from food
	 * 
	 * @param integer $id_food
	 */
	public function deleteAllIngredientsFromFood($id_food)
	{
		$result = $this->database->table('foods_ingredients')->where('id_food', $id_food)->delete();
		if (FALSE === $result) {
			throw new \DatabaseException("Unable to delete all ingredients from food with id:{$id_food}");
		}
	}
}
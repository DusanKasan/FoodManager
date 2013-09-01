<?php
namespace FoodsModule;

/**
 * Operations on tags
 *
 * @package FoodsModule
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class TagsModel extends \BaseTableAccessModel
{
	/**
	 * @var string 
	 */
	protected $table = 'tags';
	
	/**
	 * Get Tag by name, creates if it does not exist
	 * 
	 * @param string $tag_name 
	 * 
	 * @return ActiveRow
	 */
	public function createTagIfNotExists($tag_name)
	{		
		$condition = array(
			'match' => \Nette\Utils\Strings::webalize($tag_name, ' '),
		);
		
		$result = $this->database->table($this->table)->where($condition);
		$count = $result->count();
		
		if ($count === 1) {
			$tag = $result->fetch();
		} elseif ($count > 1) {
			//attempt better match
			$full_match_result = $this->database->table($this->table)->where(array('tag' => $tag_name));
			if ($full_match_result->count()) {
				$tag = $full_match_result->fetch();
			} else {
				$tag = $result->fetch();
			}
		} elseif ($count === 0) {
			$condition['tag'] = $tag_name;
			$tag = $this->createOne($condition);
		} else {
			throw new \DatabaseException('Negative tag count on select! World will soon end!');
		}
		
		return $tag;
	}
	
	/**
	 * Gets categories
	 * 
	 * @return \Nette\Database\Table\Selection 
	 */
	public function getCategories()
	{
		return $this->database->table($this->table)
				->select('tags.*, count(foods_tags:id_tag) AS "number_of_foods"')
				->where('is_category = ?', TRUE)
				->group('id_tag');
	}
	
	public function promoteTagToCategory($id_ingredient)
	{
		$this->getOne($id_ingredient)->update(array('is_category' => 1));
	}
	
	public function demoteTagToDefault($id_ingredient)
	{
		$this->getOne($id_ingredient)->update(array('is_category' => 0));
	}
}

<?php
namespace FoodsModule;

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
	 * @return ActiveRow or FALSE if there is no such row 
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
			//match against full tag name if possible
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
			throw new \Exception('Negative tag count on select!');
		}
		
		return $tag;
	}
}

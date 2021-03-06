<?php

namespace FoodsModule;

/**
 * Helper. Filtering foods. Layer between presenter and model.
 *
 * @package FoodsModule
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class FoodsFilter extends \Internal\Helpers\Filter
{
	protected function provideParseInfo()
	{
		$parse_info_array = array(
            'favourited_by' => 'favourite_foods:id_user',
 			'page' => 'page',
			'order_by' => 'order_by',
			'search' => 'search',
			'food' => 'id_food',
			'ingredient' => 'ingredients:id_ingredient',
			'tags' => 'tags:id_tag',
			'id_user' => 'id_user',
			'is_finished' => 'is_finished',
		);
				
		return $parse_info_array;
	}
	
	//Zprehladnit! vsetko viazat na prve kluce!
	public function exportWhere()
	{
		$keys_to_export = array(
			'id_food',
			'ingredients:id_ingredient',
			'tags:id_tag',
			'id_user',
			'is_finished',
            'favourite_foods:id_user',
		);
		
		return $this->exportSubset($keys_to_export);
	}
}

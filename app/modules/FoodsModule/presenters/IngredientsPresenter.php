<?php

use Nette\Application\UI;

/**
 * Ingredients presenter
 * ATM just for ajax
 * 
 * @package FoodsModule
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class IngredientsPresenter extends \BasePresenter
{	
	/**
	 * Return JSON encoded pairs id_ingredient => ingredient
	 * 
	 * @return Nette\Http\Response 
	 */
	public function handleAjax()
	{
		$ingredients = $this->context->ingredients_model
				->getAll()
				->fetchPairs('id_ingredient', 'ingredient');
		
		$this->sendResponse(new \Nette\Application\Responses\JsonResponse(array_values($ingredients)));
	}
}

<?php
use Nette\Application\UI;

/**
 * Tags presenter
 * ATM just for ajax
 * 
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class TagsPresenter extends \BasePresenter
{	
	/**
	 * Return JSON encoded pairs id_tag => tag
	 * 
	 * @return Nette\Http\Response 
	 */
	public function handleAjax()
	{
		$tags = $this->context->tags_model
				->getAll()
				->fetchPairs('id_tag', 'tag');
		
		$this->sendResponse(new \Nette\Application\Responses\JsonResponse(array_values($tags)));
	}
}

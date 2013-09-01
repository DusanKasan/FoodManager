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
	
	public function renderManage()
	{
		if ($this->user->isInRole(\UsersModule\UsersModel::ADMIN)) {
			$this->template->ingredients = $this->context->ingredients_model->getAll();
		} else {
			throw new UnauthorizedException();
		}
	}
	
	public function handleDelete($id_ingredient)
	{
		if ($this->user->isInRole(\UsersModule\UsersModel::ADMIN)) {
			try {
				$this->context->ingredients_model->deleteOne($id_ingredient);
				$this->context->logger->log("Ingredient with id:{$id_ingredient} deleted");
				$this->invalidateControl('ingredients-manage');
			} catch (DatabaseException $exception) {
				$this->flashMessage('Deleting ingredient falied', 'error');
				$this->context->logger->setLogType('error')->log("Unable to delete ingredient with id:{$id_ingredient}");
			}
		} else {
			throw new UnauthorizedException();
		}
	}
}

<?php

use Nette\Application\UI;

/**
 * Foods presenter
 * Listing, deleting, updating, creating
 * 
 * @package FoodsModule
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class FoodsPresenter extends \BasePresenter
{			
	const FOODS_PER_PAGE = 15;
	const TAGS_ON_LIST_COUNT = 3;
	
	/**
	 * Checks if $user is owner(has created) $food. Beware that you can supply wrong ActiveRecord and it breaks!
	 * @todo think about it!
	 * 
	 * @param \Nette\Security\User $user
	 * @param Nette\Database\Table\ActiveRow $food
	 * 
	 * @return boolean 
	 */
	private function checkIsFoodOwner(\Nette\Security\User $user, Nette\Database\Table\ActiveRow $food)
	{
		return (($user->isInRole(\Roles::USER) && $user->id == $food->id_user) || $user->isInRole(\Roles::ADMIN));
	}
	
	/**
	 * Redirect to foods list 
	 */
	public function renderDefault()
	{
		$this->redirect('Foods:list');
	}
	
	/**
	 * Display one food 
	 */
	public function renderShow($id)
	{
		$this->template->food = $this->context->foods_model->getOne($id);
		
		if (FALSE === $this->template->food) {
			throw new \Nette\Application\BadRequestException();
		}
		
		$this->template->hasOwnerPrivilege = $this->checkIsFoodOwner($this->user, $this->template->food);
	}
	
	/**
	 * Display categories 
	 */
	public function renderCategories()
	{
		$this->template->categories = $this->context->tags_model->getCategories();
	}
	
	/**
	 * List foods
	 */
	public function renderList()
	{		
		$foods_filter = new \FoodsModule\FoodsFilter($this->request->parameters);
		$foods = $this->context->foods_model->getFoods($foods_filter);
		
		$foods_count = $foods->count();
		$page = $foods_filter->get('page', 1);
		$this->template->foods = $foods->page($page, self::FOODS_PER_PAGE);
		
		//Custom "paginator" Shame on me!
		//TODO: Pouzit nette paginator
		$this->template->current_page = $page;
		$this->template->page_count = $page_count = ceil($foods_count/(self::FOODS_PER_PAGE));

		$this->template->prev_page = $this->template->current_page-1;
		$this->template->next_page = $this->template->current_page+1;
			
		$this->template->neighbor_pages = array($page);
		$neighbors_count = 3;
		
		for ($i=1; $i<$neighbors_count; $i++) {
			$higher = $this->template->current_page + $i;
			$lower = $this->template->current_page - $i;			
			
			if ($higher <= $page_count) {
				array_push($this->template->neighbor_pages, $higher);
			}
			
			if ($lower >= 1) {
				array_unshift($this->template->neighbor_pages, $lower);
			}
		}
		
		//---------------Hack pre paginator
		$current_url = new \Nette\Http\Url($this->getHttpRequest()->url);
		$current_url->setQuery(preg_replace('/(page=[0-9]*&|&page=[0-9]*$)/i', '',$current_url->getQuery()));
		$url_string = (string)$current_url;
		
		if (FALSE === strstr($url_string, '?')) {
			$url_string .= '?';
		}
		
		$this->template->current_url = $url_string;
		//---------------/Hack pre paginator
	}
	
	public function renderAdd()
	{
		if (!$this->user->isLoggedIn()) {
			throw new \UnauthorizedException;
		}	
		
		$data = array(
			'food' => '',
			'id_user' => $this->user->id,
		);
		
		$new_food = $this->context->foods_model->createOne($data);
		
		$this->redirect('Foods:edit', array('id' => $new_food->id_food));
	}
	
	public function createComponentEditFoodForm()
	{		
		$id_food = $this->params['id'];
		$food = $this->presenter->context->foods_model->getOne($id_food);
		
		//Toto cele sa ma riesit cez control v kompoente s vlastnou sablonou!
		if (isset($this->request->post['ingredients'])) {
			$ingredients = $this->request->post['ingredients'];
		} else {
			$ingredients = NULL;
		}
		
		return new \FoodsModule\EditFoodForm($food, $ingredients);
	}
	
	public function renderEdit($id)
	{
		$food = $this->context->foods_model->getOne($this->params['id']);
		$user = $this->user;
		
		if (!$this->checkIsFoodOwner($user, $food)) {
			throw new \UnauthorizedException;
		}
		
		$tags = array();
		foreach ($food->related('tags') as $tag) {
			$tags[] = $tag->tag->tag;
		}
		
		$this->template->tags_string = json_encode($tags);
		$this->template->assigned_ingredients_count = $food->related('ingredients')->count();
		$this->template->pictures_count = $food->related('foods_pictures')->count();
		$this->template->food = $food;
	}
	
	public function handleDelete()
	{
		$food = $this->context->foods_model->getOne($this->params['id']);
		$user = $this->user;
		
		if (!$this->checkIsFoodOwner($user, $food)) {
			throw new \UnauthorizedException;
		}
			
		$this->context->foods_model->deleteOne($food->id_food);
		$this->redirect('Foods:List');	
	}
	
	public function handleDeletePicture($id_food_picture)
	{		
		$this->context->foods_pictures_model->deleteFoodPicture($id_food_picture);
		$this->invalidateControl('pictures-edit');
	}
	
	public function createComponentAddCommentForm()
	{		
		$id_food = $this->params['id'];
		$food = $this->presenter->context->foods_model->getOne($id_food);
		
		return new \FoodsModule\AddFoodCommentForm($food);
	}
}

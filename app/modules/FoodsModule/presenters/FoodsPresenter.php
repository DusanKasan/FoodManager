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
        $this->template->is_favourite = $this->context->foods_model->isFavourite($this->user->id, $id);
		
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
	
	/**
	 * Render add
	 * 
	 * @throws \UnauthorizedException 
	 */
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
	
	/**
	 * Creates component for editing/adding foods
	 * 
	 * @return \FoodsModule\EditFoodForm 
	 */
	public function createComponentEditFoodForm()
	{		
		$id_food = $this->params['id']; //TODO: Preco treba params a nejde to cez argument?
		$food = $this->presenter->context->foods_model->getOne($id_food);
		
		//Toto cele sa ma riesit cez control v kompoente s vlastnou sablonou!
		if (isset($this->request->post['ingredients'])) {
			$ingredients = $this->request->post['ingredients'];
		} else {
			$ingredients = NULL;
		}
		
		return new \FoodsModule\EditFoodForm($food, $ingredients);
	}
	
	/**
	 * Creates component for adding comments
	 * 
	 * @return \FoodsModule\AddFoodCommentForm 
	 */
	public function createComponentAddCommentForm()
	{
		$id_food = $this->params['id']; //TODO: Preco treba params a nejde to cez argument?
		$food = $this->presenter->context->foods_model->getOne($id_food);
		
		return new \FoodsModule\AddFoodCommentForm($food);
	}
	
	/**
	 * Renders food edit form
	 * 
	 * @param type $id
	 * @throws \UnauthorizedException 
	 */
	public function renderEdit($id)
	{		
		$food = $this->context->foods_model->getOne($id);
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

    public function renderCopy($id)
    {
        $food = $this->context->foods_model->getOne($id);

        $new_food_data = $food->toArray();
        unset($new_food_data['id_food']);
        unset($new_food_data['is_finished']);
        $new_food = $this->context->foods_model->createOne($new_food_data);

        foreach ($food->related('foods_ingredients') as $food_ingredient) {
            $this->context->foods_model->addIngredientToFood($new_food->id_food, $food_ingredient->id_ingredient, $food_ingredient->amount);
        }

        foreach ($food->related('foods_tags') as $food_tag) {
            $this->context->foods_model->addTagToFood($new_food->id_food, $food_tag->id_tag);
        }

        foreach ($food->related('foods_pictures') as $picture) {
            $this->context->foods_pictures_model->addPictureToFood($new_food->id_food, $picture->file);
        }

        $this->redirect('Foods:edit', array('id' => $new_food->id_food));
    }
	
	/**
	 * Delete food
	 * 
	 * @param integer $id
	 * @throws \UnauthorizedException 
	 */
	public function handleDelete($id)
	{
		$food = $this->context->foods_model->getOne($id);
		$user = $this->user;
		$is_ok = TRUE;
		
		if (!$this->checkIsFoodOwner($user, $food)) {
			throw new \UnauthorizedException;
		}
		
		try {
			$this->context->foods_model->deleteOne($food->id_food);
			$this->context->logger->log('Deleting food.', $food);
		}catch (Exception $exception) {
			$is_ok = FALSE;
			$this->context->logger
					->setLogType(Logger\ILogger::TYPE_ERROR)
					->log('Unable to delete food.', $food, 'Error:', $exception->getMessage());
			$this->presenter->flashMessage('Unable to delete food.', 'warning');
		}
		
		if ($is_ok) {
			$this->redirect('Foods:list');
		}
	}
	
	/**
	 * Delete picture from food
	 * 
	 * @param integer $id_food_picture 
	 */
	public function handleDeletePicture($id_food_picture)
	{		
		try {
			$food_picture = $this->context->foods_pictures_model->getOne($id_food_picture);
			$this->context->foods_pictures_model->deleteOne($id_food_picture);
			$this->context->logger->log('Picture was deleted from food.', $food_picture);
		}catch (Exception $exception) {
			$this->presenter->flashMessage('Unable to delete picture.', 'warning');
			$this->context->logger
					->setLogType(Logger\ILogger::TYPE_ERROR)
					->log('Unable to delete picture.', $food_picture, 'Error:', $exception->getMessage());
		}
		$this->invalidateControl('pictures-edit');
	}

    public function handleDuplicateFood($id_food)
    {
        try {
            $food_picture = $this->context->foods_pictures_model->getOne($id_food_picture);
            $this->context->foods_pictures_model->deleteOne($id_food_picture);
            $this->context->logger->log('Picture was deleted from food.', $food_picture);
        }catch (Exception $exception) {
            $this->presenter->flashMessage('Unable to delete picture.', 'warning');
            $this->context->logger
                ->setLogType(Logger\ILogger::TYPE_ERROR)
                ->log('Unable to delete picture.', $food_picture, 'Error:', $exception->getMessage());
        }
        $this->invalidateControl('pictures-edit');
    }

    public function handleFavourite($id)
    {
        $this->context->foods_model->favouriteFood($this->user->id, $id);
        $this->presenter->invalidateControl('headline');
    }

    public function handleUnfavourite($id)
    {
        $this->context->foods_model->unfavouriteFood($this->user->id, $id);
        $this->presenter->invalidateControl('headline');
    }
}

<?php
namespace FoodsModule;

use Nette\Application\UI;

/**
 * Description of AddFoodForm
 *
 * @author Dusan Kasan <dusan@kasan.sk>
 * TODO: Prerobit aj s editom na Componentu s vlastnou sablonou!
 */
class CreateFoodForm extends \Nette\Application\UI\Form
{	
    /**
     * @param \Nette\Security\User
     */
    public function __construct()
    {
        parent::__construct();
		
		$this->getElementPrototype()->class = "form-big";
		$this->addText('food_name', 'Food name:')->setRequired('This field is required!')->setAttribute('autofocus');;
		$this->addTextArea('description', 'Description:');
		
		$this->addUpload('image', 'Food image:'); //TODO: image types validation
		
		$ingredients_textarea = $this->addTextArea('ingredients', 'Ingredients');
		$ingredients_textarea->getControlPrototype()->class = 'textarea ingredient-input';
		$ingredients_textarea->getControlPrototype()->rows = '1';
		$ingredients_textarea->getControlPrototype()->placeholder = 'Select ingredient';
		
		$tags_textarea = $this->addTextArea('tags', 'Tags');
		$tags_textarea->getControlPrototype()->class = 'textarea tags-input';
		$tags_textarea->getControlPrototype()->rows = '2';
		$tags_textarea->getControlPrototype()->placeholder = 'Add tag by pressing enter...';
				
		$this->addSubmit('add_food', 'Add food')->onClick[] = callback($this, 'addFoodFormSubmitted');
	}

    public function addFoodFormSubmitted(\Nette\Forms\Controls\SubmitButton $submit_button)
	{	
		//Get all data, we changged inputs so we need data unknown to nette
		$data = $this->getHttpData();
		
		$form = $submit_button->form;
		$context = $this->presenter->context;

		$food_name = $form->getValues()->food_name;
		$description = $form->getValues()->description;
		$tags = json_decode($data['tags']);
		$ingredients = array_filter($data['ingredients']);
			
		$image = $form->getValues()->image;
		
		$food_data = array(
			'food' => $food_name,
			'description' => $description,
			'id_user' => $this->presenter->user->id,
		);
		$food = $context->foods_model->createOne($food_data);
		
		if ($image->isOk()) {
			$id_file = $context->uploader->upload($image, $this->presenter->user);
			$context->foods_pictures_model->addPictureToFood($food->id_food, $id_file);
		}
		
		foreach ($tags as $tag) {
			$tag_data = $context->tags_model->createTagIfNotExists($tag);
			
			$context->foods_model->addTagToFood($food->id_food, $tag_data->id_tag);
		}
		
		foreach ($ingredients as $key => $ingredient) {
			$ingredient_data = $context->ingredients_model->createIngredientIfNotExists($ingredient);
			$amount = empty($data['amounts'][$key]) ? '' : $data['amounts'][$key];
			
			$context->foods_model->addIngredientToFood($food->id_food, $ingredient_data->id_ingredient, $amount);
		}
		$this->presenter->redirect('Foods:show', $food->id_food);
	}
}
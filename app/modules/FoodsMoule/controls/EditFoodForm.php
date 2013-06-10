<?php
namespace FoodsModule;

use Nette\Application\UI;

/**
 * Edit Food form class
 *
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class EditFoodForm extends \Nette\Application\UI\Form
{	
	
	public $id_food;
	
    /**
     * @param \Nette\Security\User
     */
    public function __construct($food, $ingredients = NULL)
    {		
        parent::__construct();
		
		$this->getElementPrototype()->class = "form-big";
		$this->id_food = $food->id_food;
		if (NULL == $ingredients) {
			$ingredients = $food->related('ingredients')->order('id_food_ingredient');
		}
		
		$this->addText('food_name', 'Food name:')->setRequired('This field is required!')->setValue($food->food)->getLabelPrototype()->addAttributes(array(
			'data-tooltip' => '',
			'class' => 'has-tip',
			'title' => 'Food name is required!.',
		));
		$this->addTextArea('description', 'Description:')->setValue($food->description)->getLabelPrototype()->addAttributes(array(
			'data-tooltip' => '',
			'class' => 'has-tip',
			'title' => 'Food description will reflect new lines as inputed.',
		));
		
		//Skurvene nette. Jak to nemoze mat multiple file upload!!!
		//Takze zatim kym sa neodhodlam prepisat to na zvlast komponentu
		//Bude to ohackovane cez js
		//@todo: toto sa musi prerobit ako nejake male rozsirenie formu + makro do sablon
		$image_upload = $this->addUpload('images', 'Food images:')->setAttribute('multiple'); //TODO: image types validation
		$image_upload->getLabelPrototype()->addAttributes(array(
			'data-tooltip' => '',
			'class' => 'has-tip',
			'title' => 'You can select multiple files at once!',
		));
		
		$ingredients_container = $this->addContainer('ingredients');
		$ingredients_iterator = 1;
		foreach ($ingredients as $ingredient) {
			$ingredient_container = $ingredients_container->addContainer($ingredients_iterator++);
			
			if (is_object($ingredient)) {
				$this->generatIngredientInput($ingredient_container, $ingredient->ingredient->ingredient, $ingredient->amount);
			} else {
				$this->generatIngredientInput($ingredient_container);
			}
		}
		
		//Prepare one empty input
		$ingredient_container = $ingredients_container->addContainer($ingredients_iterator++);
		$this->generatIngredientInput($ingredient_container);
		
		$tags_textarea = $this->addTextArea('tags', 'Tags', 40, 2);
		$tags_textarea->getControlPrototype()->class = 'textarea tags-input';
		$tags_textarea->getControlPrototype()->placeholder = 'Add tag by pressing enter...';
		$tags_textarea->getLabelPrototype()->addAttributes(array(
			'data-tooltip' => '',
			'class' => 'has-tip',
			'title' => 'You can select from existing ingredients or create new ones. New tags are added by pressing enter. They will become wraped in blue bubble to display correct addition.',
		));

		$submit = $this->addSubmit('edit_food', 'Edit food');
		$submit->onClick[] = callback($this, 'editFoodFormSubmitted');
		$submit->getControlPrototype()->class = 'button';
	}

    public function editFoodFormSubmitted(\Nette\Forms\Controls\SubmitButton $submit_button)
	{
		$files = $this->getHttpRequest()->getFiles();
		$images = \Nette\Utils\Arrays::get($files, 'images');
		
		$form = $submit_button->form;
		$context = $this->presenter->context;
		
		$food_name = $form->getValues()->food_name;
		$description = $form->getValues()->description;
		$ingredients = $form->getValues()->ingredients;
		$tags = json_decode($form->getValues()->tags);
				
		$food_data = array(
			'food' => $food_name,
			'description' => $description,
			'id_user' => $this->presenter->user->id,
		);
		
		try {
			$context->database->beginTransaction();
			
			$context->foods_model->updateOne($this->id_food, $food_data);
			$food = $context->foods_model->getOne($this->id_food);

			$context->foods_model->deleteAllTagsFromFood($this->id_food);
			foreach ($tags as $tag) {
				$tag_data = $context->tags_model->createTagIfNotExists($tag);
				$context->foods_model->addTagToFood($this->id_food, $tag_data->id_tag);
			}

			$context->foods_model->deleteAllIngredientsFromFood($this->id_food);
			foreach ($ingredients as $ingredient) {
				$ingredient_name = json_decode($ingredient['ingredient']);
				if (empty($ingredient_name)) {
					continue;
				}

				$ingredient_data = $context->ingredients_model->createIngredientIfNotExists($ingredient_name);

				$context->foods_model->addIngredientToFood($food->id_food, 
						$ingredient_data->id_ingredient,
						$ingredient['amount']);
			}
			
			foreach ($images as $image) {
				if ($image->isOk()) {
					$id_file = $context->uploader->upload($image, $this->presenter->user);
					$context->foods_pictures_model->addPictureToFood($food->id_food, $id_file);
				}
			}
			
			$context->database->commit();
		} catch (\Exception $exception) {
			throw $exception;
			$context->database->rollBack();
			$this->presenter->flashMessage('Unable to add food. Wrong data supplied. DB Error.');
			$this->presenter->redirect('Foods:edit', $this->id_food);
		}
		
		$this->presenter->redirect('Foods:show', $this->id_food);
	}
	
	private function generatIngredientInput(\Nette\Forms\Container $container = NULL, $ingredient_value = NULL, $ingredient_amount = NULL)
	{
		$real_container = (isset($container)) ? $container : $this;
		
		$ingredients_textarea = $real_container->addTextArea('ingredient', 'Ingredients:', 40, 1);
		$ingredients_textarea->getControlPrototype()->class = 'textarea ingredient-input';
		$ingredients_textarea->getControlPrototype()->placeholder = 'Select ingredient';
		$ingredients_textarea->getLabelPrototype()->addAttributes(array(
			'data-tooltip' => '',
			'class' => 'has-tip',
			'title' => 'To add new ingredient simply fill the ingredient input box. You can select from existing ingredients or create new ones.',
		));

		$ingredients_value = $real_container->addText('amount');
		$ingredients_value->getControlPrototype()->placeholder = 'amount';
		$ingredients_value->getControlPrototype()->class = 'amount-input';

		if (isset($ingredient_value)) {
			$ingredients_textarea->setValue($ingredient_value);
		}
		
		if (isset($ingredient_amount)) {
			$ingredients_value->setValue($ingredient_amount);
		}
	}
}
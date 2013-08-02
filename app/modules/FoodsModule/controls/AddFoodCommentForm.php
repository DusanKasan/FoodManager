<?php
namespace FoodsModule;

use Nette\Application\UI;

/**
 * Add food comment form.
 *
 * @package FoodsModule
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class AddFoodCommentForm extends \Nette\Application\UI\Form
{	
	/**
	 * @var \Nette\Database\Table\ActiveRow 
	 */
	public $food;
	
    /**
	 * Construct Form
	 * 
	 * @param \Nette\Database\Table\ActiveRow $food
	 */
    public function __construct($food)
    {		
        parent::__construct();
		
		$this->getElementPrototype()->class = "form-big ajax";
		$this->food = $food;
		
		$comment_input = $this->addText('food_comment', 'Comment:');
		$comment_input->setRequired('This field is required!');
		$comment_input->getControlPrototype()->placeholder = 'Comment text...';
		$comment_input->getControlPrototype()->value = '';
		$comment_input->getLabelPrototype()->addAttributes(array(
			'data-tooltip' => '',
			'class' => 'has-tip',
			'title' => 'Food name is required!.',
		));

		$submit = $this->addSubmit('save_comment', 'Send Comment');
		$submit->onClick[] = callback($this, 'addFoodCommentFormSubmitted');
		$submit->getControlPrototype()->class = 'button';
	}

    public function addFoodCommentFormSubmitted(\Nette\Forms\Controls\SubmitButton $submit_button)
	{
		$form = $submit_button->form;
		$context = $this->presenter->context;
		
		$food_comment = $form->getValues()->food_comment;
		$id_user = $context->user->id;
				
		try {
			$context->foods_model->addCommentToFood($this->food->id_food, $food_comment, $id_user);
		} catch (\DatabaseException $exception) {
			$this->presenter->flashMessage('Unable to add food comment. DB Error.');
		}
		
		$this->parent->invalidateControl('comments');
	}
}
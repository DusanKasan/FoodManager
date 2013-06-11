<?php
namespace UsersModule;

use Nette\Application\UI;

/**
 * Form handling registrations
 *
 * @package UsersModule
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class RegisterForm extends \Nette\Application\UI\Form
{
    /**
	 * Constructor
	 */
    public function __construct()
    {
        parent::__construct();
		
		$this->addText('username', 'Username:')->setRequired('This field is required!');;
		$this->addText('email', 'Email:')->setType('email')->setRequired('This field is required!')->addRule(\Nette\Forms\Form::EMAIL);
		$this->addPassword('password', 'Password:')->setRequired('This field is required!');
		$this->addSubmit('register', 'Register');
		$this->onSuccess[] = callback($this, 'registerFormSubmitted');
	}

	/**
	 * Register and handle errors
	 * 
	 * @param \Nette\Forms\Form $form 
	 */
    public function registerFormSubmitted(\Nette\Forms\Form $form)
	{
		$context = $this->presenter->context;
		$user = $this->presenter->user;
		
		$username = $this->getValues()->username;
		$password = $this->getValues()->password;
		$email = $this->getValues()->email;
			
		try {
			$context->users_model->createUser($username, $password, $email);
			$user->login($username, $password);		
		} catch (\Exception $e) {
			$this->presenter->flashMessage('Problem creating User! Maybe this username already exists!', 'warning');
		}

		if (!$this->presenter->hasFlashSession()) {
			$this->presenter->redirect('Foods:list');
		}
	}
}


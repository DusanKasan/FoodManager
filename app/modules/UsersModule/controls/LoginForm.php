<?php
namespace UsersModule;

use Nette\Application\UI;

/**
 * Login Form
 *
 * @package UsersModule
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class LoginForm extends \Nette\Application\UI\Form
{
	
   /**
    * Constructor
    */
    public function __construct()
    {
        parent::__construct();
		
		$this->addText('username', 'Username:')->setRequired('This field is required!')->setAttribute('autofocus');;
		$this->addPassword('password', 'Password:')->setRequired('This field is required!');
		
		$this->addSubmit('login', 'Login')->onClick[] = callback($this, 'loginFormSubmitted');
		$forgotten_pass_button = $this->addSubmit('forgotten_pass', 'Forgot your password?');
		$forgotten_pass_button->setValidationScope(NULL);
		$forgotten_pass_button->onClick[] = callback($this, 'forgottenPasswordClicked');		
	}

	/**
	 * Logs user in
	 * 
	 * @param \Nette\Forms\Controls\SubmitButton $submit_button 
	 */
    public function loginFormSubmitted(\Nette\Forms\Controls\SubmitButton $submit_button)
	{		
		$form = $submit_button->form;
		$user = $this->presenter->context->user;
		
		$username = $form->getValues()->username;
		$password = $form->getValues()->password;
				
		try {
			$user->login($username, $password);
		} catch (\Exception $e) {
			$this->presenter->flashMessage('Wrong username or password!', 'warning');
		}
		
		if ($user->isLoggedIn() ) {
			$this->presenter->redirect('Foods:list'); 
		} 
	}
	
	/**
	 * Redirects to password recovery
	 * 
	 * @param \Nette\Forms\Controls\SubmitButton $submit_button 
	 */
	public function forgottenPasswordClicked(\Nette\Forms\Controls\SubmitButton $submit_button)
	{
		$this->presenter->redirect('Users:passwordRecovery');
	}
}



<?php
namespace UsersModule;

use Nette\Application\UI;

/**
 * Form handling password recovery
 *
 * @package UsersModule
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class PasswordRecoveryForm extends \Nette\Application\UI\Form
{
    /**
	 * Constructor 
	 */
    public function __construct()
    {
        parent::__construct();
		
		$this->addText('username', 'Username:')->setRequired('This field is required!');;
		$this->addText('email', 'Email:')->setType('email')->setRequired('This field is required!')->addRule(\Nette\Forms\Form::EMAIL);
		$this->addSubmit('retrieve_new_password', 'Retrieve new password');
		$this->onSuccess[] = callback($this, 'passwordRecoveryFormSubmitted');
	}

	/**
	 * Sends recovery email
	 * 
	 * @param \Nette\Forms\Form $form 
	 */
	public function passwordRecoveryFormSubmitted(\Nette\Forms\Form $form)
	{
		$username = $this->getValues()->username;
		$email = $this->getValues()->email;
				
		if ($this->presenter->context->users_model->recoverPassword($username, $email)) {
			$this->presenter->flashMessage('New password has been sent to ' . $email . '!');
		} else {
			$this->presenter->flashMessage('Wrong username or e-mail!', 'warning');
		}
	}
}


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
		$values = $this->getValues();
		$username = $values->username;
		$email = $values->email;
		$mailer = $this->presenter->context->mailer_model;
				
		try {
			$this->presenter->context->users_model->recoverPassword($username, $email, $mailer);
			$this->presenter->flashMessage('New password has been sent to ' . $email . '!');
		} catch (\Nette\InvalidArgumentException $e) {
			$this->presenter->flashMessage('Wrong username or e-mail!', 'warning');
		} catch (\Nette\InvalidStateException $e) {
			$this->presenter->flashMessage('Error sending email! Contact administrator at dusan@kasan.sk.', 'warning');
		}
	}
}


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
		$values = $this->getValues();
		
		$username = $values->username;
		$password = $values->password;
		$email = $values->email;
			
		try {
			$context->users_model->createUser($username, $password, $email);
			$user->login($username, $password);
			$context->logger->log('User created! User id:', $user->id);
		} catch (\Exception $exception) {
			$context->logger->setLogType('error')->log('Unable to create user!', $exception->getMessage());
			$this->presenter->flashMessage('Problem creating User! Maybe this username already exists!', 'warning');
		}
		
		try {
			$context->mailer_model->sendWelcomeMail($email, $username, $password);
		} catch (\Nette\InvalidStateException $exception) {
			$context->logger->setLogType('error')->log('Unable to send mail!', $exception->getMessage());
		}
		
		if (!$this->presenter->hasFlashSession()) {
			$this->presenter->redirect('Foods:list');
		}
	}
}


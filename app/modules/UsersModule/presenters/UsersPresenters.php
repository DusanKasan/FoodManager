<?php
use Nette\Application\UI;

/**
 * Users presenter
 * Signing in, register, profile editing, fetching forgotten password etc.
 * 
 * @package UsersModule
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class UsersPresenter extends \BasePresenter
{	
	/**
	 * User registration 
	 */
	public function renderRegistration()
	{
	}
	
	/**
	 * User sing in 
	 */
	public function renderLogin()
	{
	}
	
	/**
	 * Password recovery
	 */
	public function renderPasswordRecovery()
	{
	}
	
	/**
	 * User management 
	 */
	public function renderManage()
	{
		$this->template->users = $this->context->users_model->getAll();
	}
	
	/**
	 * Delete user with $id_user
	 * 
	 * @param integer $id_user 
	 */
	public function handleDelete($id_user)
	{
		if ($this->user->isInRole(\UsersModule\UsersModel::ADMIN)) {
			try {
				$this->context->users_model->deleteOne($id_user);
				$this->context->logger-->log("User with id_user:{$id_user} deleted");
			} catch (DatabaseException $exception) {
				$this->flashMessage('Deleting user falied', 'error');
				$this->context->logger->setLogType('error')->log("Unable to delete user with id_user:{$id_user}");
			}
		} else {
			throw new UnauthorizedException();
		}
	}
	
	/**
	 * Promote user to admin
	 * 
	 * @param integer $id_user 
	 */
	public function handlePromote($id_user)
	{
		if ($this->user->isInRole(\UsersModule\UsersModel::ADMIN)) {
			try {				
				$this->context->users_model->promote($id_user);
				$this->context->logger-->log("User with id_user:{$id_user} promoted");
			} catch (DatabaseException $exception) {
				$this->flashMessage('Promoting user falied', 'error');
				$this->context->logger->setLogType('error')->log("Unable to promote user with id_user:{$id_user}");
			}
		} else {
			throw new UnauthorizedException();
		}
	}
	
	/**
	 * Strip oneself of admin privileges
	 */
	public function handleDemote()
	{
		if ($this->user->isInRole(\UsersModule\UsersModel::ADMIN)) {
			try {
				$this->context->users_model->demote($this->user->id);
				$this->context->logger-->log("User with id_user:{$id_user} demoted");
			} catch (DatabaseException $exception) {
				$this->flashMessage('Demoting user falied', 'error');
				$this->context->logger->setLogType('error')->log("Unable to demote user with id_user:{$id_user}");
			}
		} else {
			throw new UnauthorizedException();
		}
	}


	/**
	 * Registration form
	 * 
	 * @return \Nette\Application\UI\Form 
	 */
	public function createComponentRegisterForm()
	{		
		return new UsersModule\RegisterForm();
	}
	
	/**
	 * Login form
	 * 
	 * @return \Nette\Application\UI\Form 
	 */
	public function createComponentLoginForm()
	{
		return new \UsersModule\LoginForm($this->user);
	}
	
	/**
	 * Password recovery form
	 * 
	 * @return \Nette\Application\UI\Form 
	 */
	public function createComponentPasswordRecoveryForm()
	{
		return new \UsersModule\PasswordRecoveryForm();
	}
}

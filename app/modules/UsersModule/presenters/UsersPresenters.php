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
		if (!$this->user->isInRole(Roles::ADMIN)) {
			//throw new \UnauthorizedException;
		}
		
		$this->template->users = $this->context->users_model->getAll();
		
		foreach ($this->template->users as $user) {
			$user->roles = array();
			foreach ($user->related('role') as $role) {
				$user->roles[] = $role->role->role;
			}
		}
	}
	
	/**
	 * Delete user with $id_user
	 * 
	 * @param integer $id_user 
	 */
	public function handleDelete($id_user)
	{
		if ($this->user->isInRole(\UsersModule\UsersModel::ADMIN) && $this->user->id != $id_user) {
			try {
				$this->context->users_model->deleteOne($id_user);
				$this->context->logger->log("User with id_user:{$id_user} deleted");
				$this->invalidateControl('users-manage');
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
		if ($this->user->isInRole(\UsersModule\UsersModel::ADMIN) && $this->user->id != $id_user) {
			try {				
				$this->context->users_model->promote($id_user);
				$this->context->logger->log("User with id_user:{$id_user} promoted");
				$this->invalidateControl('users-manage');
			} catch (DatabaseException $exception) {
				$this->flashMessage('Promoting user falied', 'error');
				$this->context->logger->setLogType('error')->log("Unable to promote user with id_user:{$id_user}");
			}
		} else {
			throw new UnauthorizedException();
		}
	}
	
	/**
	 * Strip user of admin privileges
	 * 
	 * @param integer $id_user
	 */
	public function handleDemote($id_user)
	{
		if ($this->user->isInRole(\UsersModule\UsersModel::ADMIN) && $this->user->id != $id_user) {
			try {
				$this->context->users_model->demote($id_user);
				$this->context->logger->log("User with id_user:{$id_user} demoted");
				$this->invalidateControl('users-manage');
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

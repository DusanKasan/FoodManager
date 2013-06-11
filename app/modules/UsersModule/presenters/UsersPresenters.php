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

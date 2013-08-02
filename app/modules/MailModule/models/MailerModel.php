<?php
namespace MailModule;

/**
 * Easy sending of mails
 * @todo This is mock. Should be latte based, store latte in DB, create edit UI etc.
 *
 * @package MailModule
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class MailerModel extends \BaseModel
{		
	public function sendPasswordRetrievalMail($mail, $username, $new_password)
	{
		$message = new \Nette\Mail\Message;
		$message->setFrom('password-recovery@foodmanager.kasan.sk', 'Food Manager Password Recovery');
		$message->addTo($mail, $username);
		$message->setSubject('[Food Manager] Password Recovery');
		$message->setHtmlBody("Your new password is <strong>{$new_password}</strong>");
		$message->send();
	}
	
	public function sendWelcome($mail, $username, $password)
	{
		$message = new \Nette\Mail\Message;
		$message->setFrom('welcome@foodmanager.kasan.sk', 'Food Manager');
		$message->addTo($mail, $username);
		$message->setSubject('[Food Manager] Welcome to Food Manager');
		$message->setHtmlBody("Welcome! Your password is <strong>{$password}</strong>");
		$message->send();
	}
}

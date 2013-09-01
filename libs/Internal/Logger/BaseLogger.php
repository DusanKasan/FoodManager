<?php
namespace Logger;

/**
 * Basic logger implementation, extend from here
 *
 * @author Dusan Kasan <dusan@kasan.sk>
 */
abstract class BaseLogger implements ILogger
{
	const DEFAULT_LOG_TYPE = self::TYPE_INFO;
	
	/**
	 * @var \Nette\Security\User 
	 */
	private $user;
	
	/**
	 * @var string 
	 */
	private $log_type = self::DEFAULT_LOG_TYPE;
	
	/**
	 * 
	 * @param \Nette\Security\User $user 
	 */
	public function setUser(\Nette\Security\User $user)
	{
		$this->user = $user;
	}
	
	/**
	 * Sets next log type.
	 * 
	 * @param type $log_type
	 * @return \Logger\BaseLogger 
	 */
	public function setLogType($log_type)
	{
		$this->log_type = $log_type;
		return $this;
	}

	/**
	 * Logs data
	 * 
	 * @param mixed $data 
	 */
	public abstract function log();
	
	/**
	 * Returns user identification token
	 * 
	 * @return string 
	 */
	private function getUserIdentification()
	{
		if ($this->user->isLoggedIn()) {
			$user_identification = 'User ID: ' . $this->user->getId();
		} else {
			$user_identification = 'Guest';
		}
		
		return $user_identification;
	}
}


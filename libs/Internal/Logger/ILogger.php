<?php
namespace Logger;

/**
 * Logger Interface
 * 
 * @author Dusan Kasan <dusan@kasan.sk>
 */
interface ILogger
{
	/**
	 * Log $data 
	 */
	public function log($data);
	
	/**
	 * Set log type for next log. 
	 * 
	 * @return \Logger\ILogger 
	 */
	public function setLogType($log_type);
	
	
	/**
	 * Sets persistent user for logging 
	 */
	public function setUser(\Nette\Security\User $user);
}

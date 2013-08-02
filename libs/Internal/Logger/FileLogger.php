<?php
namespace Logger;

/**
 * Description of FileLogger
 *
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class FileLogger implements ILogger
{
	const DEFAULT_LOG_TYPE = self::TYPE_INFO;
	
	/**
	 * @var string 
	 */
	private $log_file_path;
	
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
	 * @param string $log_file_path
	 * @param \Nette\Security\User $user 
	 */
	public function __construct($log_file_path = NULL, \Nette\Security\User $user = NULL)
	{
		$this->log_file_path = $log_file_path;
		$this->user = $user;
	}
	
	/**
	 * 
	 * @param \Nette\Security\User $user 
	 */
	public function setUser(\Nette\Security\User $user)
	{
		$this->user = $user;
	}
	
	/**
	 *
	 * @param string $log_file_path 
	 */
	public function setLogFile($log_file_path)
	{
		$this->log_file_path = $log_file_path;
	}
	
	public function setLogType($log_type)
	{
		$this->log_type = $log_type;
		return $this;
	}

	/**
	 * Logs $data into $this->log_file_path
	 * 
	 * @param mixed $data 
	 */
	public function log($data, $_ = NULL)
	{		
		$date = date("Y-m-d H:i:s");		
		$user_identification = $this->getUserIdentification();
		$loggable_data = $this->makeDataLoggable(func_get_args());
		$log_string = "[$this->log_type][$date][{$user_identification}]:::{$loggable_data}\n";
		
		$this->writeLog($log_string);
		$this->log_type = self::DEFAULT_LOG_TYPE;
	}
	
	private function getUserIdentification()
	{
		if ($this->user->isLoggedIn()) {
			$user_identification = 'User ID: ' . $this->user->getId();
		} else {
			$user_identification = 'Guest';
		}
		
		return $user_identification;
	}
	
	private function makeDataLoggable(array $data)
	{
		$loggable_data = '';
		
		foreach ($data as $argument) {
			if (is_object($argument) || is_array($argument)) {
				$loggable_data .= var_export($argument, TRUE);
			} else {
				$loggable_data .= $argument;
			}
			
			$loggable_data .= ' ';
		}
		
		return $loggable_data;
	}
	
	private function writeLog($log_string)
	{
		file_put_contents($this->log_file_path, $log_string, FILE_APPEND);
	}
}


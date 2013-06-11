<?php
use Nette\Security,
	Nette\Utils\Strings;

/**
 * Users authenticator.
 * 
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class Authenticator extends Nette\Object implements Security\IAuthenticator
{
	/** @var Nette\Database\Connection */
	private $database;

	/**
	 * Constructor 
	 * 
	 * @param Nette\Database\Connection $database 
	 */
	public function __construct(Nette\Database\Connection $database)
	{
		$this->database = $database;
	}

	/**
	 * Performs an authentication.
	 * 
	 * @param array $credentials
	 * 
	 * @return Nette\Security\Identity
	 * 
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{		
		list($username, $password) = $credentials;
		$row = $this->database->table('users')->where('username', $username)->fetch();
		
		if (!$row) {
			throw new Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
		}

		if ($row->password !== $this->calculateHash($password, $row->password)) {
			throw new Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
		}
		
		$roles = $this->database->table('roles')
				->select('roles.role')
				->where('users_roles:id_user', $row->id_user)
				->fetchPairs('role', 'role');

		unset($row->password);
		return new Security\Identity($row->id_user, (empty($roles)) ? 'guest' : $roles , $row->toArray());
	}

	/**
	 * Computes salted password hash.
	 * 
	 * @param string $hash
	 * @param string $salt
	 * 
	 * @return string
	 */
	public static function calculateHash($password, $salt = NULL)
	{
		if ($password === Strings::upper($password)) { // perhaps caps lock is on
			$password = Strings::lower($password);
		}
		return crypt($password, $salt ?: '$2a$07$' . Strings::random(22));
	}

}

<?php
namespace UsersModule;

/**
 * Handling User registration/login/password retrieval/
 *
 * @package UsersModule
 * 
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class UsersModel extends \BaseModel
{	
	const ADMIN = 1;
	const USER = 2;

	/**
	 * Create user
	 * 
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 */
	public function createUser($username, $password, $email)
	{		
		try {
			$this->database->beginTransaction();
			
			//TODO: This->database->table->insert($data);
			$this->database->query(
					'INSERT INTO users (username, password, email) VALUES (?, ?, ?)', 
					$username, 
					\Authenticator::calculateHash($password), 
					$email
			);
			
			//TODO: This should be trigger
			$this->database->query(
					'INSERT INTO users_roles (id_user, id_role) VALUES (?, ?)', 
					$this->database->lastInsertId(),
					self::USER
			);

			$this->database->commit();		
		} catch (\Exception $exception) {
			$this->database->rollBack();
			throw new \DatabaseException($exception->getMessage());
		}
	}
	
	public function recoverPassword($username = NULL, $email = NULL)
	{
		$conditions = array(
			'username' => $username,
			'email' => $email,
		);
		
		$plaintext_pass = \Nette\Utils\Strings::random();
		
		$new_password = array(
			'password' => \Authenticator::calculateHash($plaintext_pass),
		);

		$result = $this->database->table('users')->where($conditions)->update($new_password);
		
		if (FALSE === $result) {
			throw new \DatabaseException('Updating username in database failed!');
		}

		//TODO: Mail on recovery
		//mail($email, '[Foodstr] New Password', "New password for $username is " . $new_password['password']);
		//setup in neon + DI
		file_put_contents(WWW_DIR . '/../pass.txt', $plaintext_pass);
	}
}

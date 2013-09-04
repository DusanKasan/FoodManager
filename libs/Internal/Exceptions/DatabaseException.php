<?php
/**
 * Database exception, throw this when error in database is not throwing it's own errors.
 *
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class DatabaseException extends \Exception {
	public function __construct($message, $code = NULL, $previous = NULL) {
		parent::__construct($message, $code, $previous);
	}
}


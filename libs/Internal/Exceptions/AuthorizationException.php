<?php
/**
 * Authorization exception, throw this when user is not allowed somewhere.
 *
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class UnauthorizedException extends Nette\Application\BadRequestException {
	public function __construct($message = '') {
		parent::__construct($message, \Nette\Http\IResponse::S401_UNAUTHORIZED, NULL);
	}
}


<?php
use Nette\Application\UI;

/**
 * Tags presenter
 * ATM just for ajax
 * 
 * @package FoodsModule
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class TagsPresenter extends \BasePresenter
{	
	/**
	 * Return JSON encoded pairs id_tag => tag
	 * 
	 * @return Nette\Http\Response 
	 */
	public function handleAjax()
	{
		$tags = $this->context->tags_model
				->getAll()
				->fetchPairs('id_tag', 'tag');
		
		$this->sendResponse(new \Nette\Application\Responses\JsonResponse(array_values($tags)));
	}
	
	/**
	 * Render tag management
	 * 
	 * @throws UnauthorizedException 
	 */
	public function renderManage()
	{
		if ($this->user->isInRole(\UsersModule\UsersModel::ADMIN)) {
			$this->template->tags = $this->context->tags_model->getAll();
		} else {
			throw new UnauthorizedException();
		}
	}
	
	/**
	 * Promote tag to category
	 * 
	 * @param integer $id_tag
	 * 
	 * @throws DatabaseException
	 * @throws UnauthorizedException 
	 */
	public function handlePromote($id_tag)
	{
		if ($this->user->isInRole(\UsersModule\UsersModel::ADMIN)) {
			try {
				$this->context->tags_model->promoteTagToCategory($id_tag);
				$this->context->logger->log('Tag with id:', $id_tag, 'promoted to category');
				$this->invalidateControl('tags-manage');
			} catch (Exception $exception) {
				$this->context->logger->setLogType(Logger\ILogger::TYPE_ERROR)->log('Tag with id:', $id_tag, 'promoting failed. Exception:', $exception->getMessage());
				throw new DatabaseException();
			}
		} else {
			throw new UnauthorizedException();
		}
		
	}
	
	/**
	 * Demote category to tag.
	 * 
	 * @param integer $id_tag
	 * 
	 * @throws DatabaseException
	 * @throws UnauthorizedException 
	 */
	public function handleDemote($id_tag)
	{
		if ($this->user->isInRole(\UsersModule\UsersModel::ADMIN)) {
			try {
				$this->context->tags_model->demoteTagToDefault($id_tag);
				$this->context->logger->log('Tag with id:', $id_tag, 'demoted to basic level');
				$this->invalidateControl('tags-manage');
			} catch (Exception $exception) {
				$this->context->logger->setLogType(Logger\ILogger::TYPE_ERROR)->log('Tag with id:', $id_tag, 'demoting to basic level fail. Exception:', $exception->getMessage());
				throw new DatabaseException();
			}
		} else {
			throw new UnauthorizedException();
		}
	}
	
	/**
	 * Delete tag
	 * 
	 * @param integer $id_tag
	 * 
	 * @throws UnauthorizedException 
	 */
	public function handleDelete($id_tag)
	{
		if ($this->user->isInRole(\UsersModule\UsersModel::ADMIN)) {
			try {
				$this->context->tags_model->deleteOne($id_tag);
				$this->context->logger->log("Tag with id:{$id_tag} deleted");
				$this->invalidateControl('tags-manage');
			} catch (DatabaseException $exception) {
				$this->flashMessage('Deleting tag falied', 'error');
				$this->context->logger->setLogType('error')->log("Unable to delete tag with id:{$id_tag}");
			}
		} else {
			throw new UnauthorizedException();
		}
	}
}

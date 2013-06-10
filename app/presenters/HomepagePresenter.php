<?php

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{

	public function renderDefault()
	{
		$this->redirect('Foods:list');
		
//		if(!$this->user->isLoggedIn()) {
//			$this->redirect('users:login');
//		} else {
//			$this->redirect('users:login');
//		}
	}

}

<?php

use Nette\Application\UI;

/**
 * Diabetes presenter
 * Shows static info for diabetics
 * 
 * @package DiabetesModule
 * @author Dusan Kasan <dusan@kasan.sk>
 */
class DiabetesPresenter extends \BasePresenter
{
	
	/**
	 * Redirect to foods list 
	 */
	public function renderDefault()
	{
		$this->redirect('Diabetes:info');
	}

    /**
     * Display basic info
     */
    public function renderInfo()
    {
    }

    /**
     * Display glycemic index info
     */
    public function renderGlycemic()
    {
    }
}

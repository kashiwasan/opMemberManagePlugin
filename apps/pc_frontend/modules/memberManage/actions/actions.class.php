<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * memberManage actions.
 *
 * @package    OpenPNE
 * @subpackage memberManage
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 9301 2008-05-27 01:08:46Z dwhittle $
 */
class memberManageActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfWebRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->size = 20;
    $this->page = $request->getParameter('page', 1);
    if ($this->page < 1)
    {
      $this->page = 1;
    }
    $this->members = new sfDoctrinePager('Member', $this->size);
    $this->members->setPage($this->page); 
    $this->members->init();
    
    return sfView::SUCCESS;
  }

  public function executeEdit(sfWebRequest $request)
  {

  }

  public function executeEditConfirm(sfWebRequest $request)
  {

  }

  public function executeEditComplete(sfWebRequest $request)
  {

  }

  public function executeDeleteConfirm(sfWebRequest $request)
  {

  }

  public function executeDeleteComplete(sfWebRequest $request)
  {

  }

}

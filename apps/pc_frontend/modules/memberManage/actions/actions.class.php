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
 * @author     shouta kashiwagi <kashiwagi@php.net>
 * @version    SVN: $Id: actions.class.php 9301 2008-05-27 01:08:46Z dwhittle $
 */
class memberManageActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfWebRequest $request A request object
  */

  public function preExecute()
  {
    if (1 !== (int) $this->getUser()->getMemberId())
    {
      $this->forward404();
    }
  }

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
    $this->member = Doctrine::getTable('Member')->find($request->getParameter('id'));
    if (!$this->member)
    {
      $this->forward404();
    }
    $this->profileForms = new opMemberProfileFormForHyperForm($this->member);
    $this->profileForm  = $this->profileForms->getAllWidgets();
    return sfView::SUCCESS;
  }

  public function executeEditConfirm(sfWebRequest $request)
  {

  }

  public function executeEditComplete(sfWebRequest $request)
  {
    $this->member = Doctrine::getTable('Member')->find($request->getParameter('id'));
    if (!$this->member)
    {
      $this->forward404();
    }
    $this->profileForms = new MemberProfileForm($this->member->getProfiles(), array(), false);
    $this->profileForms->setConfigWidgets();
    unset($this->profileForms['_csrf_token']);
    $profileParam = $request->getParameter('profile');
    $this->profileForms->bind($profileParam);
    if ($this->profileForms->isValid())
    {
      $this->profileForms->save($this->member->getId());
    }
    else
    {
      $error_messages = array_map(
        create_function('$e', 'return $e->getMessage();'),
        $this->profileForms->getErrorSchema()->getErrors());
      var_dump($error_messages);
    }

    return sfView::SUCCESS;
  }

  public function executeDeleteConfirm(sfWebRequest $request)
  {
    $this->member = Doctrine::getTable('Member')->find($request->getParameter('id'));
    if (!$this->member)
    {
      $this->forward404();
    }
    $this->csrfForm = new BaseForm();

    return sfView::SUCCESS;
  }

  public function executeDeleteComplete(sfWebRequest $request)
  {
    $this->member = Doctrine::getTable('Member')->find($request->getParameter('id'));
    if (!$this->member || 1 === (int) $request->getParameter('id'))
    {
      $this->forward404();
    }
    $request->checkCSRFProtection();
    $this->getUser()->setFlash('notice', $this->member->getName().' を削除しました。');
    $this->member->delete();
    $this->redirect('@member_manage_index');
  }

  public function executeLoginRejectConfirm(sfWebRequest $request)
  {
    $this->member = Doctrine::getTable('Member')->find($request->getParameter('id'));
    if (!$this->member || 1 === (int) $request->getParameter('id'))
    {
      $this->forward404();
    }
    $this->csrfForm = new BaseForm();

    return sfView::SUCCESS;
  }

  public function executeLoginRejectComplete(sfWebRequest $request)
  {
    $this->member = Doctrine::getTable('Member')->find($request->getParameter('id'));
    if (!$this->member || 1 === (int) $request->getParameter('id'))
    {
      $this->forward404();
    }
    $request->checkCSRFProtection();
    if ($this->member->getIsLoginRejected())
    {
      $this->member->setIsLoginRejected(false);
      $this->getUser()->setFlash('notice', $this->member->getName().' のアカウントを解除しました。');
    }
    else
    {
      $this->member->setIsLoginRejected(true);
      $this->getUser()->setFlash('notice', $this->member->getName().' のアカウントを凍結しました。');
    }
    $this->member->save();
    $this->redirect('@member_manage_index');
  }
}

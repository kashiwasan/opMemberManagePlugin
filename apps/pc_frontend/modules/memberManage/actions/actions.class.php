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
    if (2 < (int) $this->getUser()->getMember()->getProfile('yakusyoku')->getProfileOptionId())
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
    return sfView::SUCCESS;
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->member = new Member();

    return sfView::SUCCESS;
  }

  public function executeGetHyperform(sfWebRequest $request)
  {
    $this->member = Doctrine::getTable('Member')->find($request->getParameter('id'));
    if (!$this->member && !isset($request['is_new']))
    {
      return $this->renderJSON(404, array('status' => 'error', 'message' => 'This member ID does not exist.'));
    }
    if (isset($request['is_new']))
    {
      $this->member = new Member();
      $this->isNew = true;
    }
    else
    {
      $this->isNew = false;
    }
 
    $this->memberForm = array(
      array(
        'key' => 'member[name]',
        'label' => sfContext::getInstance()->getI18N()->__('%nickname%'),
        'input' => array(
          'type' => 'text',
          'isRequired' => true,
          'value' => $this->isNew ? '' : $this->member->getName(),
        ),
      ),
    );
    $this->profileForms = new opMemberProfileFormForHyperForm($this->member);
    $this->profileForm  = $this->profileForms->getAllWidgets();
    $this->configForm = array(
      array(
        'key' => 'member_config[pc_address]',
        'label' => 'メールアドレス',
        'input' => array(
          'type' => 'text',
          'isRequired' => true,
          'value' => $this->isNew ? '' : $this->member->getEmailAddress(),
          'validator' => 'email',
        ),
      ),
      array(
        'key' => 'member_config[password]',
        'label' => 'パスワード',
        'text' => '6文字以上12文字以内で設定してください。変更しない場合は入力不要です。',
        'input' => array(
          'type' => 'password',
          'isRequired' => false,
          'minlength' => 6,
          'maxlength' => 12,
        ),
      ),
    );
    $this->forms = array_merge($this->memberForm, $this->profileForm, $this->configForm);

    return $this->renderJSON(200, array('status' => 'success', 'data' => $this->forms));
  }

  public function executeEditComplete(sfWebRequest $request)
  {
    $this->member = Doctrine::getTable('Member')->find($request->getParameter('id'));
    if (!$this->member && !isset($request['is_new']))
    {
      return $this->renderJSON(404, array('status' => 'error', 'message' => 'This member ID does not exist.'));      
    }

    if (isset($request['is_new']))
    {
      //$this->member = new Member();
      //$this->member->setIsActive(true);
      //$this->member->setIsLoginRejected(false);
      $this->isNew = true;
    }
    else
    {
      $this->isNew = false;
    }
    if ($this->isNew)
    {
      $this->memberForms = new MemberForm(null, array(), false);
      $memberParam = $request->getParameter('member');
      $this->memberForms->bind($memberParam);

      if ($this->memberForms->isValid())
      {
        $this->memberForms->save();
        $this->member = $this->memberForms->getObject();
        $this->member->setIsActive(true);
        $this->member->save();
      }
      else
      {
        return $this->renderJSON(404, array('status' => 'error', 'message' => 'Member form is not valid..'));
      }

      $this->profileForms = new MemberProfileForm($this->member->getProfiles(), array(), false);
      $this->profileForms->setAllWidgets();
      $this->memberConfigForms = new opMemberManagePluginMemberConfigForm($this->member, array('member' => $this->member), false);

      $profileParam = $request->getParameter('profile');
      $memberConfigParam = $request->getParameter('member_config');

      $this->profileForms->bind($profileParam);
      $this->memberConfigForms->bind($memberConfigParam);

      if ($this->profileForms->isValid() && $this->memberConfigForms->isValid())
      {
        $this->profileForms->save($this->member->getId());
        $this->memberConfigForms->save($this->member->getId());
        return $this->renderJSON(200, array('status' => 'success'));
      }
      else
      {
        $profile_messages = array_map(
          create_function('$e', 'return $e->getMessage();'),
          $this->profileForms->getErrorSchema()->getErrors()
        );
        $config_messages = array_map(
          create_function('$e', 'return $e->getMessage();'),
          $this->memberConfigForms->getErrorSchema()->getErrors()
        );
        // var_dump($member_messages);
        // var_dump($profile_messages);
        // var_dump($config_messages);
        $error_messages = array_merge($profile_messages, $config_messages);
        $lists = array();
        foreach ($error_messages as $k => $v)
        {
          try 
          {
            $labelName = $this->profileForms->getWidget($k)->getLabel();
          } 
          catch (Exception $e) 
          {
            continue;
          }
          if (!$labelName) 
          {
            $labelName = $k;
          }
          $labelName = sfContext::getInstance()->getI18N()->__($labelName);
          $lists[$labelName] = sfContext::getInstance()->getI18N()->__($v);
        }
        return $this->renderJSON(400, array('status' => 'error', 'error_detail' => $lists));
      }
    }
    else
    {
      $this->memberForms = new MemberForm($this->member, array(), false);
      $this->profileForms = new MemberProfileForm($this->member->getProfiles(), array(), false);
      $this->profileForms->setAllWidgets();
      $this->memberConfigForms = new opMemberManagePluginMemberConfigForm($this->member, array('member' => $this->member), false);

      $memberParam = $request->getParameter('member');
      $profileParam = $request->getParameter('profile');
      $memberConfigParam = $request->getParameter('member_config');

      $this->memberForms->bind($memberParam);
      $this->profileForms->bind($profileParam);
      $this->memberConfigForms->bind($memberConfigParam);

      if ($this->memberForms->isValid() && $this->profileForms->isValid() && $this->memberConfigForms->isValid())
      {
        $this->memberForms->save();
        $this->profileForms->save($this->member->getId());
        $this->memberConfigForms->save($this->member->getId());
        return $this->renderJSON(200, array('status' => 'success'));
      }
      else
      {
        $member_messages = array_map(
          create_function('$e', 'return $e->getMessage();'),
          $this->memberForms->getErrorSchema()->getErrors()
        );
        $profile_messages = array_map(
          create_function('$e', 'return $e->getMessage();'),
          $this->profileForms->getErrorSchema()->getErrors()
        );
        $config_messages = array_map(
          create_function('$e', 'return $e->getMessage();'),
          $this->memberConfigForms->getErrorSchema()->getErrors()
        );
      // var_dump($member_messages);
      // var_dump($profile_messages);
      // var_dump($config_messages);
        $error_messages = array_merge($member_messages, $profile_messages, $config_messages);
        $lists = array();
        foreach ($member_messages as $k => $v)
        {
          try 
          {
            $labelName = $this->memberForms->getWidget($k)->getLabel();
          } 
          catch (Exception $e) 
          {
            continue;
          }
          if (!$labelName) 
          {
            $labelName = $k;
          }
          $labelName = sfContext::getInstance()->getI18N()->__($labelName);
          $lists[$labelName] = sfContext::getInstance()->getI18N()->__($v);
        }
        foreach ($profile_messages as $k => $v)
        {
          try 
          {
            $labelName = $this->profileForms->getWidget($k)->getLabel();
          } 
          catch (Exception $e) 
          {
            continue;
          }
          if (!$labelName) 
          {
            $labelName = $k;
          }
          $labelName = sfContext::getInstance()->getI18N()->__($labelName);
          $lists[$labelName] = sfContext::getInstance()->getI18N()->__($v);
        }
        foreach ($config_messages as $k => $v)
        {
          try 
          {
            $labelName = $this->memberConfigForms->getWidget($k)->getLabel();
          } 
          catch (Exception $e) 
          {
            continue;
          }
          if (!$labelName) 
          {
            $labelName = $k;
          }
          $labelName = sfContext::getInstance()->getI18N()->__($labelName);
          $lists[$labelName] = sfContext::getInstance()->getI18N()->__($v);
        }
        return $this->renderJSON(400, array('status' => 'error', 'error_detail' => $lists, 'error_messages' => $error_messages));
      }
    }
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

  public function executeAllDeleteConfirm(sfWebRequest $request)
  {
    $ids = $request->getParameter('id');
    $this->members = array();
    if (!is_array($ids))
    {
      $this->forward404();
    }
    $this->csrfForm = new BaseForm();
    foreach ($ids as $id)
    {
      $m = Doctrine::getTable('Member')->find($id);
      if (!$m || 1 === (int) $id)
      {
        continue;
      }
      $this->members[] = $m;
    }
    $this->getUser()->setAttribute('op_all_delete_id', $ids);

    return sfView::SUCCESS;
  }

  public function executeAllDeleteComplete(sfWebRequest $request)
  {
    $ids = $this->getUser()->getAttribute('op_all_delete_id', null);
    if (!is_array($ids))
    {
      $this->forward404();
    }
    $request->checkCSRFProtection();
    foreach ($ids as $id)
    {
      $m = Doctrine::getTable('Member')->find($id);
      if (!$m || 1 === (int) $id)
      {
        continue;
      }
      $m->delete();
    }

    $this->getUser()->setFlash('notice', 'メンバーを削除しました。');
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

  protected function renderJSON($statusCode, array $data)
  {
    $this->getResponse()->setStatusCode($statusCode);
    $this->getResponse()->setContentType('application/json');

    return $this->renderText(json_encode($data));
  }
}

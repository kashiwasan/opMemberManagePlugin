<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * MemberConfig form.
 *
 * @package    form
 * @subpackage member_config
 * @author     Shouta Kashiwagi <kashiwagi@php.net>
 */
class opMemberManagePluginMemberConfigForm extends BaseForm
{

  public $member = null;
  public function setup()
  {
    $this->member = $this->getOption('member');
    $this->setWidget('pc_address', new sfWidgetFormInput());
    $this->setValidator('pc_address', new opValidatorPCEmail());
    $this->setWidget('password', new sfWidgetFormInputPassword());
    $this->setValidator('password', new sfValidatorString(array('required' => false)));
    $this->mergePostValidator(new sfValidatorCallback(array(
      'callback' => array($this, 'isUnique'),
      'arguments' => array('name' => 'pc_address'),
    )));

    $this->widgetSchema->setNameFormat('member_config[%s]');
  }

  public function save()
  {
    $member = $this->getOption('member');
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }
    $member->setConfig('pc_address', $this->getValue('pc_address'));
    if ('' != $this->getValue('password'))
    {
      $member->setConfig('password', md5($this->getValue('password')));
    }
  }

  public function isUnique($validator, $value, $arguments = array())
  {
    if (empty($arguments['name'])) {
      throw new InvalidArgumentException('Invalid argument');
    }

    $name = $arguments['name'];
    $data = Doctrine::getTable('MemberConfig')->retrieveByNameAndValue($name, $value[$name]);
    if (!$data || !$data->getMember()->getIsActive() || $data->getMember()->getId() == $this->member->getId()) {
      return $value;
    }

    throw new sfValidatorError($validator, 'Invalid %name%.', array('name' => $name));
  }
}

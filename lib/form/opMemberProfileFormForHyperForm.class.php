<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * MemberProfile form.
 *
 * @package    OpenPNE
 * @subpackage form
 * @author     Shouta Kashiwagi <kashiwagi@openpne.jp>
 */
class opMemberProfileFormForHyperForm extends BaseForm
{
  public $profileMember;
  public $nameFormat = null;

  public function __construct($profileMember = array(), $options = array(), $CSRFSecret = null)
  {
    parent::__construct(array(), $options, $CSRFSecret);
    if (array() !== $profileMember)
    {
      $this->profileMember = $profileMember;
    }   
  }

  public function configure()
  {
    $this->nameFormat = 'profile[%s]';
  }

  public function save($memberId)
  {
    $values = $this->getValues();

    foreach ($values as $key => $value)
    {
      $profile = Doctrine::getTable('Profile')->retrieveByName($key);
      if (!$profile)
      {
        continue;
      }

      $memberProfile = Doctrine::getTable('MemberProfile')->retrieveByMemberIdAndProfileId($memberId, $profile->getId());

      if (is_null($value['value']))
      {
        if ($memberProfile)
        {
          if ($profile->isMultipleSelect())
          {
            $memberProfile->clearChildren();
          }
          $memberProfile->delete();
        }
        continue;
      }
      if (!$memberProfile)
      {
        $memberProfile = new MemberProfile();
        $memberProfile->setMemberId($memberId);
        $memberProfile->setProfileId($profile->getId());
      }

      $memberProfile->setPublicFlag($memberProfile->getProfile()->getDefaultPublicFlag());
      if (isset($value['public_flag']))
      {
        $memberProfile->setPublicFlag($value['public_flag']);
      }
      $memberProfile->save();

      if ($profile->isMultipleSelect())
      {
        $ids = array();
        $_values = array();
        if ('date' === $profile->getFormType())
        {
          $_values = array_map('intval', explode('-', $value['value']));
          $options = $profile->getProfileOption();
          foreach ($options as $option)
          {
            $ids[] = $option->getId();
          }
          $memberProfile->setValue($value['value']);
        }
        else
        {
          $ids = $value['value'];
        }
        Doctrine::getTable('MemberProfile')->createChild($memberProfile, $memberId, $profile->getId(), $ids, $_values);
      }
      else
      {
        $memberProfile->setValue($value['value']);
      }

      $memberProfile->save();
    }

    return true;
  }

  public function getRegisterWidgets()
  {
    $profiles = Doctrine::getTable('Profile')->retrieveByIsDispRegist();
    return $this->getProfileWidgets($profiles);
  }

  public function getConfigWidgets()
  {
    $profiles = Doctrine::getTable('Profile')->retrieveByIsDispConfig();
    $form = $this->getProfileWidgets($profiles);
    return $form;
  }

  public function getSearchWidgets()
  {
    $profiles = Doctrine::getTable('Profile')->retrieveByIsDispSearch();
    return $this->getProfileWidgets($profiles);
  }

  public function getAllWidgets()
  {
    $profiles = Doctrine::getTable('Profile')->retrievesAll();
    return $this->getProfileWidgets($profiles);
  }

  protected function getProfileWidgets($profiles)
  {
    $forms = array();
    $presetList = opToolkit::getPresetProfileList();

    foreach ($profiles as $profile)
    {
      $form = array();

      $profileI18n = $profile->Translation[sfContext::getInstance()->getUser()->getCulture()]->toArray();
      $profileWithI18n = $profile->toArray() + $profileI18n;
      $profileWithI18nWithCamelize = opFormItemGeneratorForHyperForm::arrayKeyCamelize($profileWithI18n);
      
      $form['key'] = $profile->getName();
      $form['label'] = $profileWithI18nWithCamelize['Caption'];
      $form['input'] = opFormItemGeneratorForHyperForm::generateWidget($profileWithI18n, $this->getFormOptionsValue($profile->getId()));
      if (!in_array($profileWithI18nWithCamelize['FormType'], array('checkbox', 'select', 'radio', 'region_select', 'country_select')) && $this->profileMember)
      {
          $memberProfileValue = $this->profileMember->getProfile($form['key']);
          $form['input']['value'] = (string) $memberProfileValue;
      }
      if (in_array($profileWithI18nWithCamelize['FormType'], array('region_select', 'country_select')) && $this->profileMember)
      {
        $itemCount = count($form['input']['items']);
          for($i=0;$i<$itemCount;$i++)
          {
            if ($form['input']['items'][$i]['value'] === (string) $this->profileMember->getProfile($form['key']))
            {
              $form['input']['items'][$i]['isSelected'] = true;
            }
          }
      }
      if (in_array($profileWithI18nWithCamelize['FormType'], array('checkbox', 'select', 'radio')))
      {
        foreach($this->getFormOptionsValue($profile->getId()) as $name => $value)
        {
          $items['label'] = sfContext::getInstance()->getI18N()->__($value);
          $items['value'] = $name;
          $form['input']['items'][] = $items;
        }
        if (!empty($profileWithI18nWithCamelize['Choices']) && is_array($profileWithI18nWithCamelize['Choices']))
        {
          foreach($profileWithI18nWithCamelize['Choices'] as $name => $value)
          {
            $items['label'] = sfContext::getInstance()->getI18N()->__($value);
            $items['value'] = $name;
            $form['input']['items'][] = $items;
          }
        }
        $itemCount = count($form['input']['items']);
        if ($this->profileMember)
        {
          if (!is_null($this->profileMember->getProfile($form['key'])))
          {
            if ($profile->isPreset())
            {
              for($i=0;$i<$itemCount;$i++)
              {
                if ($form['input']['items'][$i]['value'] === (string) $this->profileMember->getProfile($form['key'])->getValue())
                {
                  $form['input']['items'][$i]['isSelected'] = true;
                }
              }
            }
            else
            {
              for($i=0;$i<$itemCount;$i++)
              {
                if ($form['input']['items'][$i]['value'] === (int) $this->profileMember->getProfile($form['key'])->getValue())
                {
                  $form['input']['items'][$i]['isSelected'] = true;
                }
              }
            }
          }
        }
        elseif (!empty($profileWithI18nWithCamelize['Default']))
        {
          for($i=0;$i<$itemCount;$i++)
          {
            if ($form['input']['items'][$i]['value'] == $profileWithI18nWithCamelize['Default'] )
            {
              $form['input']['items'][$i]['isSelected'] = true;
            }
          }
        }
      }
      $form['text'] = $profileWithI18n['info'];

      // validator
      $form['input']['isRequired'] = $profileWithI18nWithCamelize['IsRequired'];

//            
//      TODO: validator の実装
//
//      $validatorOptions = array(
//        'validator' => opFormItemGenerator::generateValidator($profileWithI18n, $this->getFormOptions($profile->getId())),
//      );

      if ($profile->isPreset())
      {
        $form['label'] = sfContext::getInstance()->getI18N()->__($presetList[$profile->getRawPresetName()]['Caption']);
        if ('op_preset_birthday' === $profile->getName())
        {
          $form['text'] = sfContext::getInstance()->getI18N()->__('The public_flag for your age can be configure at "Settings" page.');
        }
      }
      if (!is_null($this->nameFormat))
      {
        $form['key'] = sprintf($this->nameFormat, $form['key']).'[value]';
      }
      $forms[] = $form;

      if ($profile->getIsEditPublicFlag())
      {
        $form = array();
        $form['key'] = $profile->getName();
        $form['label'] = sfContext::getInstance()->getI18N()->__('Public flag');
        $form['input']['type'] = 'slider'; // TODO: 本当はsliderにしたいがHyperformのバグ？により断念。
        $form['input']['isRequired'] = true;
        foreach ($profile->getPublicFlags() as $k => $v)
        {
          $item = array();
          $item['label'] = $v;
          $item['value'] = $k;
          if ($k == (int) $this->profileMember->getProfile($profile->getName())->getPublicFlag())
          {
            $item['isSelected'] = true;
          }
          $form['input']['items'][] = $item;
        }
        if (!is_null($this->nameFormat))
        {
          $form['key'] = sprintf($this->nameFormat, $form['key']).'[public_flag]';
        }
        $forms[] = $form;
      }
     
    }

    return $forms;
  }

  private function getFormOptions($profileId)
  {
    $result = array();
    $options = Doctrine::getTable('ProfileOption')->retrieveByProfileId($profileId);

    foreach ($options as $option)
    {
      $result[] = $option->getId();
    }

    return $result;
  }

  private function getFormOptionsValue($profileId)
  {
    $result = array();
    $options = Doctrine::getTable('ProfileOption')->retrieveByProfileId($profileId);

    foreach ($options as $option)
    {
      $result[$option->getId()] = $option->getValue();
    }

    return $result;
  }

  private function updateDefaultsFromObject($obj)
  {
    $this->setDefaults(array_merge($this->getDefaults(), $obj->toArray()));
  }
}

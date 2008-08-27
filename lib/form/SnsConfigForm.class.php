<?php

/**
 * SnsConfig form.
 *
 * @package    form
 * @subpackage sns_config
 * @author     Kousuke Ebihara <ebihara@tejimaya.net>
 */
class SnsConfigForm extends sfForm
{
  public function configure()
  {
    $widgets = array();
    $validators = array();
    $labels = array();
    $defaults = array();

    foreach (OpenPNEConfig::loadConfigYaml() as $key => $value) {
      $widgets[$key] = $this->generateWidget($value);
      $validators[$key] = $this->generateValidator($value);
      $labels[$key] = $value['caption'];
      $config = SnsConfigPeer::retrieveByName($key);
      if ($config) {
        $defaults[$key] = OpenPNEConfig::get($key, 'sns', $config->getValue());
      }
    }

    $this->setWidgets($widgets);
    $this->setValidators($validators);
    $this->widgetSchema->setLabels($labels);
    $this->setDefaults($defaults);

    $this->widgetSchema->setNameFormat('sns_config[%s]');
  }

  public function generateWidget($config)
  {
    switch ($config['type']) {
      case 'select':
        $obj = new sfWidgetFormSelect(array('choices' => $this->generateChoices($config['choices_type'])));
        break;
      case 'input':
      default:
        $obj = new sfWidgetFormInput();
    }

    return $obj;
  }

  public function generateValidator($config)
  {
    switch ($config['type']) {
      case 'select':
        $obj = new sfValidatorChoice(array('choices' => $this->generateChoices($config['choices_type'])));
        break;
      case 'input':
      default:
        $obj = new sfValidatorString($config['option']);
    }

    return $obj;
  }

  public function save()
  {
    foreach ($this->getValues() as $key => $value) {
      $snsConfig = SnsConfigPeer::retrieveByName($key);
      if (!$snsConfig) {
        $snsConfig = new SnsConfig();
        $snsConfig->setName($key);
      }
      $snsConfig->setValue($value);
      $snsConfig->save();
    }
  }

  public function generateChoices($mode)
  {
    if ($mode == 'AuthMode') {
      return $this->generateAuthModeChoices();
    }
  }

  private function generateAuthModeChoices()
  {
    $authModes = array();

    $authPlugins = sfFinder::type('directory')->name('opAuth*Plugin')->in(sfConfig::get('sf_plugins_dir'));
    foreach ($authPlugins as $authPlugin) {
      $pluginName = basename($authPlugin);
      $endPoint = strlen($pluginName) - strlen('opAuth') - strlen('Plugin');
      $authMode = substr($pluginName, strlen('opAuth'), $endPoint);
      $authModes[$authMode] = $authMode;
    }

    return $authModes;
  }
}

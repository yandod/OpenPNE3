<?php

/**
 * this file is part of the openpne package.
 * (c) openpne project (http://www.openpne.jp/)
 *
 * for the full copyright and license information, please view the license
 * file and the notice file that were distributed with this source code.
 */

/**
 * design actions.
 *
 * @package    openpne
 * @subpackage design
 * @author     kousuke ebihara <ebihara@tejimaya.com>
 */
class designActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('design', 'layout');
  }

 /**
  * Executes home layout action
  *
  * @param sfRequest $request A request object
  */
  public function executeLayout(sfWebRequest $request)
  {
    $option = array();

    $this->configs = array();
    $gadgetConfigs = sfConfig::get('op_gadget_config', array());
    foreach ($gadgetConfigs as $key => $config)
    {
      if (isset($config['layout']['choices']))
      {
        $this->configs[$key] = $config;
      }
    }

    $type = $request->getParameter('type', 'gadget');
    $this->forward404Unless(isset($this->configs[$type]));
    $this->subtitle = $this->configs[$type]['name'];

    $option['layout_name'] = $type;
    
    $this->form = new PickHomeLayoutForm(array(), $option);

    if ($request->isMethod(sfRequest::POST))
    {
      $this->form->bind($request->getParameter('pick_home_layout'));
      $this->redirectIf($this->form->save(), 'design/layout?type='.$type);
    }

    return sfView::SUCCESS;
  }

 /**
  * Executes gadget action
  *
  * @param sfRequest $request A request object
  */
  public function executeGadget(sfWebRequest $request)
  {
    $this->configs = sfConfig::get('op_gadget_config', array());
    $layouts = sfConfig::get('op_gadget_layout_config', array());
    $this->type = $request->getParameter('type', 'gadget');
    
    $this->forward404Unless(isset($this->configs[$this->type]));
    
    $this->subtitle = $this->configs[$this->type]['name'];
    $this->plotAction = $this->configs[$this->type]['plot_action'];
    

    $this->gadgets = Doctrine::getTable('Gadget')->retrieveGadgetsByTypesName($this->type);

    $this->sortForm = new GadgetSortForm(array(), array('current_gadgets' => $this->gadgets));
    $this->addForm = new GadgetAddForm(array(), array('current_gadgets' => $this->gadgets));
    if ($request->isMethod(sfRequest::POST))
    {
      $this->sortForm->bind($request->getParameter('gadget'));
      $this->addForm->bind($request->getParameter('new'));
      if ($this->sortForm->isValid() && $this->addForm->isValid())
      {
        $this->sortForm->save();
        $this->addForm->save();
        $this->redirect('design/gadget?type='.$this->type);
      }
    }
  }

 /**
  * Executes home gadget plot action
  *
  * @param sfRequest $request A request object
  */
  public function executeHomeGadgetPlot(sfWebRequest $request)
  {
    $configs = sfConfig::get('op_gadget_config');
    $this->layoutPattern = $configs['gadget']['layout']['default'];
    $this->gadgets = Doctrine::getTable('Gadget')->retrieveGadgetsByTypesName('gadget');
    $this->gadgetConfig = sfConfig::get('op_gadget_list');

    $layout = Doctrine::getTable('SnsConfig')->get('home_layout');
    if ($layout)
    {
      $this->layoutPattern = $layout;
    }

    return sfView::SUCCESS;
  }

  /**
  * Executes profile gadget plot action
  *
  * @param sfRequest $request A request object
  */
  public function executeProfileGadgetPlot(sfWebRequest $request)
  {
    $configs = sfConfig::get('op_gadget_config');
    $this->layoutPattern = $configs['profile']['layout']['default'];
    $this->gadgets = Doctrine::getTable('Gadget')->retrieveGadgetsByTypesName('profile');
    $this->gadgetConfig = sfConfig::get('op_profile_gadget_list');

    $layout = Doctrine::getTable('SnsConfig')->get('profile_layout');
    if ($layout)
    {
      $this->layoutPattern = $layout;
    }

    return sfView::SUCCESS;
  }

 /**
  * Executes login gadget plot action
  *
  * @param sfRequest $request A request object
  */
  public function executeLoginGadgetPlot(sfWebRequest $request)
  {
    $configs = sfConfig::get('op_gadget_config');
    $this->layoutPattern = $configs['login']['layout']['default'];
    $this->gadgets = Doctrine::getTable('Gadget')->retrieveGadgetsByTypesName('login');

    $this->gadgetConfig = sfConfig::get('op_login_gadget_list');

    $layout = Doctrine::getTable('SnsConfig')->get('login_layout');
    if ($layout)
    {
      $this->layoutPattern = $layout->getValue();
    }
    return sfView::SUCCESS;
  }
 
 /**
  * Executes mobile home gadget plot action
  *
  * @param sfRequest $request A request object
  */
  public function executeMobileHomeGadgetPlot(sfWebRequest $request)
  {
    $this->gadgets = Doctrine::getTable('Gadget')->retrieveGadgetsByTypesName('mobile');
    $this->gadgetConfig = sfConfig::get('op_mobile_gadget_list');

    return sfView::SUCCESS;
  }

 /**
  * Executes mobile profile gadget plot action
  *
  * @param sfRequest $request A request object
  */
  public function executeMobileProfileGadgetPlot(sfWebRequest $request)
  {
    $this->gadgets = Doctrine::getTable('Gadget')->retrieveGadgetsByTypesName('mobileProfile');
    $this->gadgetConfig = sfConfig::get('op_mobile_profile_gadget_list');

    return sfView::SUCCESS;
  }

  /**
   * Executes mobile login gadget plot action
   *
   * @param sfWebRequest $request A request object
   */
  public function executeMobileLoginGadgetPlot(sfWebRequest $request)
  {
    $this->gadgets = Doctrine::getTable('Gadget')->retrieveGadgetsByTypesName('mobileLogin');
    $this->gadgetConfig = sfConfig::get('op_mobile_login_gadget_list');
    
    return sfView::SUCCESS;
  }

 /**
  * Executes side banner home gadget plot action
  *
  * @param sfRequest $request A request object
  */
  public function executeSideBannerGadgetPlot(sfWebRequest $request)
  {
    $this->gadgets = Doctrine::getTable('Gadget')->retrieveGadgetsByTypesName('sideBanner');
    $this->gadgetConfig = sfConfig::get('op_side_banner_gadget_list');

    return sfView::SUCCESS;
  }

 /**
  * Executes mobile header gadget plot action
  *
  * @param sfRequest $request A request object
  */
  public function executeMobileHeaderGadgetPlot(sfWebRequest $request)
  {
    $this->gadgets = Doctrine::getTable('Gadget')->retrieveGadgetsByTypesName('mobileHeader');
    $this->gadgetConfig = sfConfig::get('op_mobile_header_gadget_list');
  }

 /**
  * Executes mobile footer gadget plot action
  *
  * @param sfRequest $request A request object
  */
  public function executeMobileFooterGadgetPlot(sfWebRequest $request)
  {
    $this->gadgets = Doctrine::getTable('Gadget')->retrieveGadgetsByTypesName('mobileFooter');
    $this->gadgetConfig = sfConfig::get('op_mobile_footer_gadget_list');
  }

 /**
  * Executes add gadget action
  *
  * @param sfRequest $request A request object
  */
  public function executeAddGadget(sfWebRequest $request)
  {
    $this->type = $request->getParameter('type', 'top');
    $this->config = Doctrine::getTable('Gadget')->getGadgetConfigListByType($this->type);

    return sfView::SUCCESS;
  }

 /**
  * Executes home edit gadget action
  *
  * @param sfRequest $request A request object
  */
  public function executeEditGadget(sfWebRequest $request)
  {
    $this->gadget = Doctrine::getTable('Gadget')->find($request->getParameter('id'));

    $type = $this->gadget->getType();
    $config = Doctrine::getTable('Gadget')->getGadgetConfigListByType($type);

    $this->forward404Unless($this->gadget && $config);
    $this->config = $config[$this->gadget->getName()];

    if (!empty($this->config['config']))
    {
      $this->form = new GadgetConfigForm($this->gadget, array('type' => $type));

      if ($request->isMethod(sfRequest::POST))
      {
        $this->form->bind($request->getParameter('gadget_config'));
        if ($this->form->isValid())
        {
          $this->form->save();
          $this->redirect('design/editGadget?id='.$this->gadget->getId());
        }
      }
    }

    return sfView::SUCCESS;
  }
}

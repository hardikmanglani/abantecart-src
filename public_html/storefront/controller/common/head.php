<?php   
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ControllerCommonHead extends AController {
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		
		$this->loadLanguage('common/header');
		
		$this->view->assign('title', $this->document->getTitle());
		$this->view->assign('keywords', $this->document->getKeywords());
		$this->view->assign('description', $this->document->getDescription());
		$this->view->assign('template', $this->config->get('config_storefront_template'));

		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->view->assign('base', HTTPS_SERVER);
		} else {
			$this->view->assign('base', HTTP_SERVER);
		}
		
		$icon_path = $this->config->get('config_icon');
		if( $icon_path){
			if(!is_file(DIR_RESOURCE.$this->config->get('config_icon'))){
				$this->messages->saveWarning('Check favicon.','Warning: please check favicon in your store settings. Current path is "'.DIR_RESOURCE.$this->config->get('config_icon').'" but file does not exists.');
				$icon_path ='';
			}
		}
		$this->view->assign('icon', $icon_path);
		$this->view->assign('lang', $this->language->get('code'));
		$this->view->assign('direction', $this->language->get('direction'));
		$this->view->assign('links', $this->document->getLinks());	
		$this->view->assign('styles', $this->document->getStyles());
		$this->view->assign('scripts', $this->document->getScripts());		
		$this->view->assign('breadcrumbs', $this->document->getBreadcrumbs());
		
		$this->view->assign('store', $this->config->get('store_name'));
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
		    $this->view->assign('ssl', 1);
        }
        $this->view->assign('cart_ajax', (int) $this->config->get('config_cart_ajax'));
        $this->view->assign('cart_ajax_url', $this->html->getURL('r/product/product/addToCart'));

		$this->processTemplate('common/head.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

	}	
	
}
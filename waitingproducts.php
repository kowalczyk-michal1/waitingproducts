<?php
if (!defined('_PS_VERSION_'))
  exit;

require_once dirname(__FILE__) . '/models/WPModel.php';

class WaitingProducts extends Module
{
	public function __construct()
	{
		$this->name = 'waitingproducts';
		$this->tab = 'others';
		$this->version = '1.0.0';
		$this->author = 'Michal Kowalczyk';
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
		$this->bootstrap = true;
	 
		parent::__construct();
	 
		$this->displayName = $this->l('Lista oczekujących');
		$this->description = $this->l('Lista oczekujących na produkt');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
	 
	}
  
	public function install()
	{
		 if (!Db::getInstance()->Execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'waiting_products` 
    		(`id` INT AUTO_INCREMENT, 
            `product_id` INT(11) NOT NULL, 
			`email` VARCHAR(320) NOT NULL, 
			`position` INT(11) NOT NULL, 
    		PRIMARY KEY(id))
		CHARSET=utf8;'))
            return false;
			
			
		if (!parent::install() ||
			!$this->registerHook('displayProductTab') ||
			!Configuration::updateValue('wptext', 'some text')
		)
		return false;
		
		return true;
	}
	
	 public function uninstall() {

        Db::getInstance()->Execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'waiting_products`');
        
        Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration` WHERE `name` LIKE "wptext"');
        
        if (!parent::uninstall() || !$this->unregisterHook('DisplayProductTab'))
            return false;
        return true;

    }
	
	public function getContent()
	{
		$output = null;
	 
		if (Tools::isSubmit('submit'.$this->name))
		{
			$wpvalue = Tools::getValue('wptext');
			
			if (!$wpvalue
			  || empty($wpvalue))
				$output .= $this->displayError($this->l('Niepoprawna wartość.'));
			else
			{
				Configuration::updateValue('wptext', $wpvalue, true);
				$output .= $this->displayConfirmation($this->l('Zaktualizowano!'));
			}
		}
		return $output.$this->displayForm();
	}
	
	public function displayForm()
	{
		
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		 
		
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Ustawienia'),
			),
			'input' => array(
				array(
					'type' => 'textarea',
					'label' => $this->l('Wyświetlany tekst'),
					'name' => 'wptext',
					'autoload_rte' => true,
					'rows' => 10,
                    'cols' => 100,
					'required' => true
				)
			),
			'submit' => array(
				'title' => $this->l('Zapisz'),
				'class' => 'btn btn-default pull-right'
			)
		);
		 
		$helper = new HelperForm();
		 
		
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		 
		
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;
		 
	
		$helper->title = $this->displayName;
		$helper->show_toolbar = true; 
		$helper->toolbar_scroll = true; 
		$helper->submit_action = 'submit'.$this->name;
		$helper->toolbar_btn = array(
			'save' =>
			array(
				'desc' => $this->l('Save'),
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
				'&token='.Tools::getAdminTokenLite('AdminModules'),
			),
			'back' => array(
				'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
				'desc' => $this->l('Back to list')
			)
		);
		 
		
		$helper->fields_value['wptext'] = Configuration::get('wptext');
		 
		return $helper->generateForm($fields_form);
	}

	
	public function hookDisplayProductTab(){
		$output = null;
		$show_text = Configuration::get('wptext');
	 
		if (Tools::isSubmit('SubmitWP'))
		{
			$email = strval(Tools::getValue('email'));
			$check_email = WPModel::getEmail($email);
			$pid =  (int)Tools::getValue('id_product');
			
			if (!empty($check_email['id'])) $output=$this->l('Podany email już istnieje w bazie.');
			else
			{
				$get_max_pos=WPModel::getMaxPos($pid);
				
				if (!is_numeric($get_max_pos['position'])) $position=1;
				else $position=$get_max_pos['position']+1;
					
				$addemail = WPModel::addEmail($email, $pid, $position);
				
				
				$output=$this->l('Jesteś '.$position.' w kolejce oczekujących na ten produkt.');
			}
			
		}
		
		$this->context->smarty->assign('info', $output);
		$this->context->smarty->assign('show_text', $show_text);
		
		
		
		return $this->display(__FILE__, 'index.tpl');
	}
	
	
}
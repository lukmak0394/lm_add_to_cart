<?php
/**
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Lm_addtocart extends Module
{
    protected $config_form = false;
    private $module_name = 'module:lm_addtocart';


    public function __construct()
    {
        $this->name = 'lm_addtocart';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Łukasz Makowski';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Add to cart button');
        $this->description = $this->l('Module displaying add to cart button on product miniature');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('LM_ADDTOCART_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayProductListReviews');
    }

    public function uninstall()
    {
        Configuration::deleteByName('LM_ADDTOCART_LIVE_MODE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitLm_addtocartModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitLm_addtocartModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'LM_ADDTOCART_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'LM_ADDTOCART_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'LM_ADDTOCART_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'LM_ADDTOCART_LIVE_MODE' => Configuration::get('LM_ADDTOCART_LIVE_MODE', true),
            'LM_ADDTOCART_ACCOUNT_EMAIL' => Configuration::get('LM_ADDTOCART_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'LM_ADDTOCART_ACCOUNT_PASSWORD' => Configuration::get('LM_ADDTOCART_ACCOUNT_PASSWORD', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookDisplayProductListReviews($params)
    {

        $id_product = (int) $params['product']['id_product'];

        return $this->generateTemplateInHook($id_product);

    }



    public function generateTemplateInHook($id_product) {

        $cart = $this->context->cart;

        $products_in_cart = $cart->getProducts();

        $products_to_check = [];
 
        foreach($products_in_cart as $prod) {
            array_push($products_to_check,$prod['id_product']);
        }


        $this->context->smarty->assign(array(
            'controller_url' => $this->getControllerUrl(),
            'id_product' => $id_product,
            'available' => $this->checkIfProductIsAvailable($id_product),
            'has_attributes' => $this->checkIfProductHasAttributes($id_product)
        ));


        if(!in_array($id_product,$products_to_check)) {
            return $this->display(__FILE__,'add-to-cart-button.tpl');
        } else  {
            return $this->display(__FILE__,'add-to-cart-button-disabled.tpl');
        }

    }

    public function getControllerUrl() {

        $link = new Link();

        $controller_url = $link->getModuleLink('lm_addtocart', 'ajax');

        return $controller_url;
    }

    public function checkIfProductIsAvailable($id_product) {

        $product = new Product($id_product);

        $result = $product->checkQty(1);

        return $result;
    }

    public function checkIfProductHasAttributes($id_product) {

        $query = Db::getInstance()->getValue('SELECT COUNT(`id_product_attribute`) FROM `ps_product_attribute` WHERE `id_product` = '.$id_product.'');

        if($query > 0) {
            return true;
        } else {
            return false;
        }

    }
    
}

<?php



class Lm_AddtocartajaxModuleFrontController extends ModuleFrontController {

    private $module_name = 'module:lm_addtocart';


    // Post process is a method execuded in a controller when you submit a form or send ajax request to it
    // Its been modified a little here
    // First we check if 'ajax' is set in request - we set it in "ajax=1" in js
    // If it is, then ajaxCall is being executed
    public function postProcess()
    {

        if(Tools::getIsset('ajax')){
            
            $this->ajaxCall(); 

        } else {
            parent::postProcess();

        }

    }

    public function ajaxCall()
    {

        $action = Tools::ucfirst(Tools::getValue('action'));

        if (!empty($action) && method_exists($this, 'ajax' . $action)) {
          
            $result = $this->{'ajax' . $action}();
        } else {
            $result = (array('error' => 'Ajax parameter used, but action \'' . Tools::getValue('action') . '\' is not defined'));
        }
     
        die(json_encode($result));
    }

    public function ajaxGetDisabledButton() {    

        return $this->context->smarty->fetch($this->module_name.'/views/templates/front/add-to-cart-button-disabled.tpl');

    }


}
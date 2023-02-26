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
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/


$(document).ready(function() {
    
    const controller_url = $('button.lm-add-to-cart').data('controllerUrl');

    const updateElement = (element,html) => {
        element.html(html);
    }

    $('body').on('click', 'button.lm-add-to-cart',function() {

        const button_wrapper = $(this).parent().parent();

        getDisabledButton(controller_url,button_wrapper);

    })

    const getDisabledButton = (controller_url,button_wrapper) => {
        $.ajax({
            type: 'POST',
            data: `ajax=1&action=getDisabledButton`,
            url: controller_url,
            dataType: 'json',
            success: (res) => {
                updateElement(button_wrapper,res);
            },
            error: () => {
                console.log('error');
            }
            
        })
    }


})
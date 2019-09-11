<?php
/**
* 2007-2019 PrestaShop
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
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(_PS_MODULE_DIR_.'owlmegamenu/classes/OwlMegamenuClass.php');
include_once(_PS_MODULE_DIR_.'owlmegamenu/classes/OwlMegamenuRowClass.php');
include_once(_PS_MODULE_DIR_.'owlmegamenu/classes/OwlMegamenuColumnClass.php');
include_once(_PS_MODULE_DIR_.'owlmegamenu/classes/OwlMegamenuItemClass.php');
include_once(_PS_MODULE_DIR_.'owlmegamenu/sql/SampleDataMenu.php');

class Menuregenerator extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'menuregenerator';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Agustín Fiori';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Menu Re-Generator');
        $this->description = $this->l('Regenerates Owl Megamenu using product\'s categories');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader');
    }

    public function uninstall()
    {
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
        if (((bool)Tools::isSubmit('mrg_save')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);
        $this->context->smarty->assign('categories_tree', $this->getCategoriesTree());

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output;
    }



    /**
     * Save form data.
     */
    protected function postProcess()
    {


        //die('<pre>'.print_r($_POST, true).'</pre>');

        $this->generateMenu($_POST['use'], $_POST['text'], $this->getCategoriesTree(), false);


    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
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



    function getCategoriesTree(){
        $menu_tree = array();
        $res = $this->getCategories(1);
        if ($res) {
            foreach ($res as $row) {
                $cat = new Category($row['id_category']);
                
                $item['id_category']    = $cat->id_category;
                $item['name']           = $cat->name[1];
                $item['id_parent']      = $cat->id_parent;
                $item['childs']         = array();


                //CHILDS
                $res_childs = $this->getCategories($cat->id_category);
                $childs = array();

                if ($res_childs) {
                    foreach ($res_childs as $row_child){
                        $cat_child = new Category($row_child['id_category']);
                
                        $item_child['id_category']      = $cat_child->id_category;
                        $item_child['name']             = $cat_child->name[1];
                        $item_child['id_parent']        = $cat_child->id_parent;

                        $item['childs'][] = $item_child;
                        
                    }
                }

                $menu_tree[] = $item;

            }
        }

        return $menu_tree;
    }

    function getCategories($id_parent){
        $res = Db::getInstance()->executeS('SELECT `id_category` FROM `'._DB_PREFIX_.'category` WHERE id_parent = '.(int)$id_parent);
        return $res;
    }

    function generateMenu($items, $texts, $categories_tree){

        //DELETE CURRENT MENU
        Db::getInstance()->executeS('DELETE FROM `'._DB_PREFIX_.'owlmegamenu`');
        Db::getInstance()->executeS('DELETE FROM `'._DB_PREFIX_.'owlmegamenu_column`');
        Db::getInstance()->executeS('DELETE FROM `'._DB_PREFIX_.'owlmegamenu_column_shop`');
        Db::getInstance()->executeS('DELETE FROM `'._DB_PREFIX_.'owlmegamenu_item`');
        Db::getInstance()->executeS('DELETE FROM `'._DB_PREFIX_.'owlmegamenu_item_lang`');
        Db::getInstance()->executeS('DELETE FROM `'._DB_PREFIX_.'owlmegamenu_item_shop`');
        Db::getInstance()->executeS('DELETE FROM `'._DB_PREFIX_.'owlmegamenu_lang`');
        Db::getInstance()->executeS('DELETE FROM `'._DB_PREFIX_.'owlmegamenu_row`');
        Db::getInstance()->executeS('DELETE FROM `'._DB_PREFIX_.'owlmegamenu_row_shop`');
        Db::getInstance()->executeS('DELETE FROM `'._DB_PREFIX_.'owlmegamenu_shop`');

        //PARENTS
        foreach($categories_tree AS $category){

            if(in_array($category['id_category'], $items)){
                //echo 'SI '.$category['id_category'].' - '.$texts[$category['id_category']].'<br>';

                $new_id = $category['id_category'];
                $new_tx = $texts[$new_id];
                $menu_item_id = $this->owlMakeItem($new_tx, $new_id);
                $row_item_id = $this->owlMakeRow($menu_item_id);
                $col_item_id = $this->owlMakeCol($row_item_id);

                //CHILDS
                foreach($category['childs'] AS $child){
                    $new_child_id = $child['id_category'];
                    $new_child_tx = $texts[$new_child_id];
                    $child_item_id = $this->owlMakeChild($new_child_tx, $new_child_id, $col_item_id);
                }
            } 

        }

        //ORDER
        $this->orderMenuAlphabetically();

    }


    function owlMakeItem($cat_name, $cat_id){
        $menu_item              = new OwlMegamenuClass();
        $menu_item->position    = 0;
        $menu_item->active      = 1;
        $menu_item->type_link   = 1;
        $menu_item->dropdown    = 0;
        $menu_item->type_icon   = 0;
        $menu_item->align_sub   = //'owl-sub-right';//'owl-sub-auto';
        $menu_item->width_sub   = 'col-xl-3';
        $menu_item->class       = '';
        $menu_item->title[1]    = trim($cat_name);
        $menu_item->link[1]     = '?id_category='.(int)$cat_id.'&controller=category';
        $menu_item->add();

        return $menu_item->id;
    }

    function owlMakeRow($menu_item_id){
        $row_item                   = new OwlMegamenuRowClass();
        $row_item->active           = 1;
        $row_item->id_owlmegamenu   = $menu_item_id;
        $row_item->class            = '';
        $row_item->add();

        return $row_item->id;
    }

    function owlMakeCol($row_item_id){
        $col_item                   = new OwlMegamenuColumnClass();
        $col_item->position         = 0;
        $col_item->active           = 1;
        $col_item->id_row           = $row_item_id;
        $col_item->width            = 'col-lg-12';
        $col_item->class            = '';
        $col_item->add();

        return $col_item->id;
    }

    function owlMakeChild($cat_name, $cat_id, $col_item_id){
        $sub_menu_item                  = new OwlMegamenuItemClass();
        $sub_menu_item->position        = 0;
        $sub_menu_item->active          = 1;
        $sub_menu_item->id_column       = $col_item_id;
        $sub_menu_item->type_link       = 2;
        $sub_menu_item->type_item       = 2;
        $sub_menu_item->id_product      = 0;
        $sub_menu_item->title[1]        = trim($cat_name);
        $sub_menu_item->link[1]         = '?id_category='.(int)$cat_id.'&controller=category';
        $sub_menu_item->add();

        return $sub_menu_item->id;
    }

    function orderMenuAlphabetically(){

        //ORDER PARENTS
        $sql = 'SELECT M.id_owlmegamenu, L.title
                FROM '._DB_PREFIX_.'owlmegamenu M
                LEFT JOIN '._DB_PREFIX_.'owlmegamenu_lang AS L
                ON M.id_owlmegamenu = L.id_owlmegamenu
                WHERE L.id_shop = '.(int)$this->default_shop.'
                AND L.id_lang = '.(int)$this->default_lang.'
                ORDER BY L.title';

        $res = Db::getInstance()->executeS($sql);
        $i = 0;
        foreach ($res as $row) {
            $sql = 'UPDATE '._DB_PREFIX_.'owlmegamenu SET position = '.(int)$i.' WHERE id_owlmegamenu = '.(int)$row['id_owlmegamenu'];
            Db::getInstance()->execute($sql);
            $sql = 'UPDATE '._DB_PREFIX_.'owlmegamenu_shop SET position = '.(int)$i.' WHERE id_owlmegamenu = '.(int)$row['id_owlmegamenu'].' AND id_shop = '.(int)$this->default_shop;
            Db::getInstance()->execute($sql);
            $i++;
        }

        //ORDER CHILDS
        $sql = 'SELECT I.id_item, L.title
                FROM '._DB_PREFIX_.'owlmegamenu_item I
                LEFT JOIN '._DB_PREFIX_.'owlmegamenu_item_lang AS L
                ON I.id_item = L.id_item
                WHERE L.id_shop = '.(int)$this->default_shop.'
                AND L.id_lang = '.(int)$this->default_lang.'
                ORDER BY L.title';

        $res = Db::getInstance()->executeS($sql);
        $i = 0;
        foreach ($res as $row) {
            $sql = 'UPDATE '._DB_PREFIX_.'owlmegamenu_item SET position = '.(int)$i.' WHERE id_item = '.(int)$row['id_item'];
            Db::getInstance()->execute($sql);
            $sql = 'UPDATE '._DB_PREFIX_.'owlmegamenu_item_shop SET position = '.(int)$i.' WHERE id_item = '.(int)$row['id_item'].' AND id_shop = '.(int)$this->default_shop;
            Db::getInstance()->execute($sql);
            $i++;
        }

    }
}

{*
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
*}

<div class="panel">
	<h3><i class="icon icon-credit-card"></i> {l s='Menu Re-Generator' mod='menuregenerator'}</h3>


	<table class="table">
		<tr>
			<td colspan="2"><h4>Product's Categories</h4></td>
			<td colspan="2"><h4>Owl Megamenu</h4></td>
		</tr>

		{foreach from=$categories_tree item=category}
			<tr>
				<td colspan="2">[{$category.id_category}] {$category.name}</td>
				<td colspan="2"> <input type="checkbox" name ="use" id="use" value="{$category.id_category}" checked="checked"> </td>
				<td> <input type="text" id="text" value="{$category.name}"> </td>
			</tr>

			{foreach from=$category.childs item=child}
				<tr>
					<td> --- </td>
					<td>[{$child.id_category}] {$child.name}</td>
					<td> --- </td>
					<td> <input type="checkbox" name ="use" id="use" value="{$child.id_category}" checked="checked"> </td>
					<td> <input type="text" id="text" value="{$child.name}"> </td>
				</tr>
			{/foreach}	
		{/foreach}		

		
      
    </table>




	<p>
		<strong>{l s='Here is my new generic module!' mod='menuregenerator'}</strong><br />
		{l s='Thanks to PrestaShop, now I have a great module.' mod='menuregenerator'}<br />
		{l s='I can configure it using the following configuration form.' mod='menuregenerator'}
	</p>
	<br />
	<p>
		{l s='This module will boost your sales!' mod='menuregenerator'}
	</p>
</div>

<div class="panel">
	<h3><i class="icon icon-tags"></i> {l s='Documentation' mod='menuregenerator'}</h3>
	<p>
		&raquo; {l s='You can get a PDF documentation to configure this module' mod='menuregenerator'} :
		<ul>
			<li><a href="#" target="_blank">{l s='English' mod='menuregenerator'}</a></li>
			<li><a href="#" target="_blank">{l s='French' mod='menuregenerator'}</a></li>
		</ul>
	</p>
</div>

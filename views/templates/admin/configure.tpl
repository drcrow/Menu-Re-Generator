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
			<td colspan="2"><h2>Product's Categories</h4></td>
			<td colspan="2"><h2>Owl Megamenu</h4></td>
		</tr>

		{foreach from=$categories_tree item=category}
			<tr>
				<td colspan="2">[{$category.id_category}] {$category.name}</td>
				<td colspan="2"> <input type="checkbox" class="parentcheck parent_{$category.id_category}" name ="use" id="use" value="{$category.id_category}" checked="checked"> </td>
				<td> <input type="text" id="text" value="{$category.name}"> </td>
			</tr>

			{foreach from=$category.childs item=child}
				<tr>
					<td> --- </td>
					<td>[{$child.id_category}] {$child.name}</td>
					<td> --- </td>
					<td> <input type="checkbox" class="childcheck childof_{$category.id_category}" parent="{$category.id_category}" name ="use" id="use" value="{$child.id_category}" checked="checked"> </td>
					<td> <input type="text" id="text" value="{$child.name}"> </td>
				</tr>
			{/foreach}	
		{/foreach}		

		
      
    </table>

<script type="text/javascript">

	$(document).ready(function(){
		//checks or unchecks all the childs of the clicked parent
	    $(".parentcheck").click(function() {
	        if(this.checked){
	        	$(".childof_"+this.value).prop('checked', true);
	        }else{
	        	$(".childof_"+this.value).prop('checked', false);
	        }
	    }); 
	    //checks the parent of the clicked child
	    $(".childcheck").click(function() {
	    	var parent = $(this).attr('parent');
	        if(this.checked){
	        	$(".parent_"+parent).prop('checked', true);
	        }
	    });                 
	});
</script>


	<div class="text-right" style="margin-top: 40px">

		<button type="button" class="btn btn-primary">Generate Menu</button>
		&nbsp;&nbsp;&nbsp;
		<button type="button" class="btn btn-danger">Clear and then Generate Menu</button>

	</div>

</div>

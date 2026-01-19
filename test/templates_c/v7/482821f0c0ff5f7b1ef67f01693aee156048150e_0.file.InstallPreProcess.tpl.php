<?php
/* Smarty version 4.5.5, created on 2026-01-19 13:19:23
  from 'C:\laragon\www\crm-vtiger8\layouts\v7\modules\Install\InstallPreProcess.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_696e2f5b7ddb33_63242347',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '482821f0c0ff5f7b1ef67f01693aee156048150e' => 
    array (
      0 => 'C:\\laragon\\www\\crm-vtiger8\\layouts\\v7\\modules\\Install\\InstallPreProcess.tpl',
      1 => 1752039682,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_696e2f5b7ddb33_63242347 (Smarty_Internal_Template $_smarty_tpl) {
?>
<input type="hidden" id="module" value="Install" />
<input type="hidden" id="view" value="Index" />
<div class="container-fluid page-container">
	<div class="row">
		<div class="col-sm-6">
			<div class="logo">
				<img src="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'vimage_path' ][ 0 ], array( 'logo.png' ));?>
"/>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="head pull-right">
				<h3><?php echo vtranslate('LBL_INSTALLATION_WIZARD','Install');?>
</h3>
			</div>
		</div>
	</div>
<?php }
}

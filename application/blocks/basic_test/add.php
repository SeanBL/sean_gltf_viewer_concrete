<?php defined('C5_EXECUTE') or die(_("Access Denied.")) ?>
<div class="ccm-ui">
	<div class="alert-message block-message info">
		<?php echo t("This is the add template for the basic test block. Anything you add in this view will automatically be wrapped in a form and, when submitted, sent to the block's controller.") ?>
	</div>

	<?php echo $form->label('content', t('Name')) ?>
	<?php echo $form->text('content') ?>
</div>

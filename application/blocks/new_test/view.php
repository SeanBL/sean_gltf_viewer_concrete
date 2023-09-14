<?php defined('C5_EXECUTE') or die(_("Access Denied.")) ?>
<?php
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\File\FolderItemList;
?>

<div>
    <div><?php echo t('Field 1')?></div>
    <div><?php echo $field1 ?? '' ?></div>
</div>

<div>
    <div><?php echo t('Field 2')?></div>
    <div><?php echo $field2 ?? '' ?></div>
</div>

<div>
    <div><?php echo t('Boolean')?></div>
    <div>
        <?php
        if ($booleanfield ?? false) {
            echo t('Yes');
        } else {
            echo t('No');
        }
        ?>
    </div>
</div>
<div>
    <?php



    ?>
</div>

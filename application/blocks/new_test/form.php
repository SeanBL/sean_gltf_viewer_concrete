<?php defined('C5_EXECUTE') or die(_("Access Denied."));
?>

<div class="form-group">
    <label class="control-label" for="field1"><?=t('Field 1')?> <sup class="fas fa-asterisk"></sup></label>
    <input type="text" class="form-control" name="field1" value="<?php echo $field1 ?? ''?>">
</div>

<div class="form-group">
    <label class="control-label" for="field1"><?=t('Field 2')?></label>
    <input type="text" class="form-control" name="field2" value="<?php echo $field2 ?? ''?>">
</div>

<div class="form-group">
    <div class="checkbox">
        <label>
            <input type="checkbox" name="booleanfield" value="1" <?= $booleanfield ?? false ? 'checked' : '' ?>>
            <?= t('Boolean Field') ?>
        </label>
    </div>
</div>



<div class="form-group">
    <label class="control-label">GLTF</label>
    <?php
        $service = Core::make('helper/concrete/file_manager');
        print $service->file('gltf', 'gltfID', 'Select gltf', $args=[]);
    ?>
</div>
<div class="form-group">
    <label class="control-label">Binary</label>
    <?php
        $service = Core::make('helper/concrete/file_manager');
        print $service->file('binary', 'binaryID', 'Select Binary', $args=[]);
    ?>
</div>



<!-- <a href="#" data-launch="file-manager">Select File</a>
<script type="text/javascript">
    $(function() {
        $('a[data-launch=file-manager]').on('click', function(e) {
            e.preventDefault();
            ConcreteFileManager.launchDialog(function (data) {
                console.log(data.fID);
             }, {
                multipleSelection: true   
            });
        });
    });
</script> -->

<a href="javascript:;" onclick="addFiles()">Add Files</a>
<div id="container"></div>
<script>
    function addFiles() {
        ConcreteFileManager.launchDialog(function (data) {
            ConcreteFileManager.getFileDetails(data.fID, function(r) {

                r.files.forEach(function(e) {
                    let addFile = e.resultsThumbnailImg + '<br />';
                    addFile += 'File-Name: '+e.title+' ';

                    $('#container').append(addFile);
                });
            });
        }, { 'multipleSelection' : true });
        <?php $service = Core::make('helper/concrete/file_manager');?>
        <?php $service->file('photo', 'photoID', t('choose file')); ?>
    }
</script>
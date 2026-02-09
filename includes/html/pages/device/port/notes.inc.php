<?php
$pagetitle[] = 'Notes';
$port_id_notes = 'port_id_notes:' . $port['port_id'];
$device_id = $device['device_id'];
$data = get_dev_attrib($device, $port_id_notes) ?? '';
?>

<form class="form-horizontal" action="" method="post">
    <?php echo csrf_field() ?>
    <h3>Port Notes</h3>
    <hr>
    <div class="form-group">
        <div class="col-sm-10">
            <textarea class="form-control" rows="6" name="notes" id="port-notes"><?php
            echo htmlentities($data); ?></textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-10">
            <?php
            echo '
            <button type="submit" name="btn-update-notes" id="btn-update-notes" class="btn btn-primary">Submit</button>
            ';
            ?>
        </div>
    </div>
</form>
<script>
$("[name='btn-update-notes']").on('click', function(event) {
    event.preventDefault();
    var $this = $(this);
    var device_id = "<?php echo $device_id; ?>";
    var port_id_notes = "<?php echo $port_id_notes; ?>";
    var notes = $("#port-notes").val();
    $.ajax({
        type: 'POST',
        url: 'ajax_form.php',
        data: { type: "update-port-notes", notes: notes, port_id_notes: port_id_notes, device_id: device_id },
        dataType: "html",
        success: function(data){
            toastr.success('Saved');
        },
        error:function(){
            toastr.error('Error');
        }
    });
});
</script>

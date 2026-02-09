<?php
if (Auth::user()->hasGlobalAdmin()) {
    ?>

    <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h5 class="modal-title" id="Delete">Confirm Delete</h5>
                </div>
                <div class="modal-body">
                    <p>Please confirm that you would like to delete this poller.</p>
                </div>
                <div class="modal-footer">
                    <form role="form" class="remove_token_form">
                        <?php echo csrf_field() ?>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger danger" id="poller-removal"
                                data-target="poller-removal">Delete
                        </button>
                        <input type="hidden" name="id" id="id" value="">
                        <input type="hidden" name="pollertype" id="pollertype" value="">
                        <input type="hidden" name="confirm" id="confirm" value="yes">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#confirm-delete').on('show.bs.modal', function (e) {
            id = $(e.relatedTarget).data('id');
            pollertype = $(e.relatedTarget).data('pollertype');
            $("#id").val(id);
            $("#pollertype").val(pollertype);
        });

        $('#poller-removal').on("click", function (e) {
            e.preventDefault();
            var id = $("#id").val();
            var pollertype = $("#pollertype").val();
            $.ajax({
                type: 'POST',
                url: 'ajax_form.php',
                data: {type: pollertype, id: id},
                success: function (result) {
                    if (result.status == 0) {
                        toastr.success(result.message);
                        $("#row_" + id).remove();
                    }
                    else {
                        toastr.error(result.message);
                    }
                    $("#confirm-delete").modal('hide');
                },
                error: function () {
                    toastr.error('An error occurred deleting this poller.');
                    $("#confirm-delete").modal('hide');
                }
            });
        });
    </script>
    <?php
}
?>

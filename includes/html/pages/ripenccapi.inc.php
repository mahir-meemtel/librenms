<?php
$pagetitle[] = 'RIPE NCC - API Tools';
$no_refresh = true;

?>
<h3> RIPE NCC API Tools </h3>
<hr>
<form class="form-horizontal" action="" method="post">
    <?php echo csrf_field() ?>
    <div class="radio">
        <label><input type="radio" name="data_radio" value="whois" checked>Whois</label>
    </div>
    <div class="radio">
        <label><input type="radio" name="data_radio" value="abuse-contact-finder">Abuse Contact Finder</label>
    </div>
    <br />
    <div class="input-group">
        <input type="text" class="form-control" id="input-parameter" placeholder="IP, ASN etc.">
        <span class="input-group-btn">
        <button type="submit" name="btn-query" id="btn-query" class="btn btn-primary">Query</button>
        </span>
    </div>
</form>
<br />
<div class="alert alert-success" style="display: none;">
    <pre id="ripe-output"></pre>
</div>
<br />
<script>
    $("[name='btn-query']").on('click', function(event) {
        event.preventDefault();
        var data_param = $('input[name=data_radio]:checked').val();
        var query_param = $("#input-parameter").val();
        $.ajax({
            type: 'POST',
            url: 'ajax/ripe/raw',
            data: {
                data_param: data_param,
                query_param: query_param
            },
            dataType: "json",
            success: function(data) {
                var output = $('#ripe-output');
                output.empty();
                output.parent().show();

                if (data.output.data.records)
                    $.each(data.output.data.records[0], function(row, value) {
                        $('#ripe-output').append(value['key'] + ' = ' + value['value'] + '<br />');
                    });
                else if (data.output.data.abuse_contacts)
                    $.each(data.output.data.abuse_contacts, function(row, value) {
                        $('#ripe-output').append("email" + ' = ' + value + '<br />');
                    });
            },
            error: function(data) {
                if (data.status === 422) {
                    var json = data.responseJSON;
                    var errors = [];
                    for (var attrib in json) {
                        errors.push(json[attrib]);
                    }

                    toastr.error('Error: ' + errors.join("<br />"));
                } else {
                    toastr.error(data.responseJSON.message);
                }
            }
        });
    });
</script>

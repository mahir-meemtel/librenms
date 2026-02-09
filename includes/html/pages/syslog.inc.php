<?php
use Carbon\Carbon;
use App\Facades\ObzoraConfig;

$no_refresh = true;
$param = [];
$device_id = (int) $vars['device'];

if (isset($vars['action']) && $vars['action'] == 'expunge' && \Auth::user()->hasGlobalAdmin()) {
    \App\Models\Syslog::truncate();
    print_message('syslog truncated');
}

$pagetitle[] = 'Syslog';
?>
<div class="panel panel-default panel-condensed">
    <div class="panel-heading">
        <strong>Syslog</strong>
    </div>

    <?php
    require_once 'includes/html/common/syslog.inc.php';
    echo implode('', $common_output);
    ?>
</div>
<script>
    $('.actionBar').append(
        '<div class="pull-left">' +
        '<form method="post" action="" class="form-inline" role="form" id="result_form">' +
        '<?php echo csrf_field() ?>'+
        '<div class="form-group">' +
        <?php
        if (! isset($vars['fromdevice'])) {
            ?>
        '<select name="device" id="device" class="form-control">' +
        '<option value="">All Devices&nbsp;&nbsp;</option>' +
            <?php
            if ($device_id) {
                echo "'<option value=$device_id>" . str_replace(['"', '\''], '', htmlentities(format_hostname(device_by_id_cache($device_id)))) . "</option>' +";
            } ?>
        '</select>' +
            <?php
        } else {
            echo "'&nbsp;&nbsp;<input type=\"hidden\" name=\"device\" id=\"device\" value=\"" . $device_id . "\">' + ";
        }
        ?>
        '</div>' +
        '&nbsp;&nbsp;<div class="form-group">' +
        '<select name="program" id="program" class="form-control">' +
        '<option value="">All Programs&nbsp;&nbsp;</option>' +
        <?php
        if (! empty($vars['program'])) {
            $js_program = addcslashes(htmlentities($vars['program']), "'");
            echo "'<option value=\"$js_program\">$js_program</option>' +";
        }
        ?>
        '</select>' +
        '</div>' +
        '&nbsp;&nbsp;<div class="form-group">' +
        '<select name="priority" id="priority" class="form-control">' +
        '<option value="">All Priorities</option>' +
        <?php
        if (! empty($vars['priority'])) {
            $js_priority = addcslashes(htmlentities($vars['priority']), "'");
            echo "'<option value=\"$js_priority\">$js_priority</option>' +";
        }
        ?>
        '</select>' +
        '</div>' +
        '&nbsp;&nbsp;<div class="form-group">' +
        '<input name="from" type="text" class="form-control" id="dtpickerfrom" maxlength="16" value="<?php echo htmlspecialchars($vars['from'] ?? ''); ?>" placeholder="From" data-date-format="YYYY-MM-DD HH:mm">' +
        '</div>' +
        '<div class="form-group">' +
        '&nbsp;&nbsp;<input name="to" type="text" class="form-control" id="dtpickerto" maxlength="16" value="<?php echo htmlspecialchars($vars['to'] ?? ''); ?>" placeholder="To" data-date-format="YYYY-MM-DD HH:mm">' +
        '</div>' +
        '&nbsp;&nbsp;<button type="submit" class="btn btn-default">Filter</button>' +
        '</form>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>'
    );

    $(function () {
        var fromPicker = flatpickr("#dtpickerfrom", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            defaultDate: '<?php echo Carbon::now()->subDay()->format(ObzoraConfig::get('dateformat.byminute', 'Y-m-d H:i')); ?>',
            allowInput: true,
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates[0]) {
                    toPicker.set("minDate", selectedDates[0]);
                }
            }
        });
        
        var toPicker = flatpickr("#dtpickerto", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            allowInput: true,
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates[0]) {
                    fromPicker.set("maxDate", selectedDates[0]);
                }
            }
        });
        
        if ($("#dtpickerfrom").val() != "") {
            toPicker.set("minDate", $("#dtpickerfrom").val());
        }
        if ($("#dtpickerto").val() != "") {
            fromPicker.set("maxDate", $("#dtpickerto").val());
        } else {
            toPicker.set("maxDate", '<?php echo Carbon::now()->format(ObzoraConfig::get('dateformat.byminute', 'Y-m-d H:i')); ?>');
        }
    });

    <?php if (! isset($vars['fromdevice'])) { ?>
    $("#device").select2({
        theme: "bootstrap",
        dropdownAutoWidth : true,
        width: "auto",
        allowClear: true,
        placeholder: "All Devices",
        ajax: {
            url: '<?php echo url('/ajax/select/device'); ?>',
            delay: 200
        }
    })<?php echo $device_id ? ".val($device_id).trigger('change');" : ''; ?>;
    <?php } ?>

    $("#program").select2({
        theme: "bootstrap",
        dropdownAutoWidth : true,
        width: "auto",
        allowClear: true,
        placeholder: "All Programs",
        ajax: {
            url: '<?php echo url('/ajax/select/syslog'); ?>',
            delay: 200,
            data: function(params) {
                return {
                    field: "program",
                    device: $('#device').val(),
                    term: params.term,
                    page: params.page || 1
                }
            }
        }
    })<?php echo isset($vars['program']) ? ".val('" . htmlspecialchars($vars['program']) . "').trigger('change');" : ''; ?>;

    $("#priority").select2({
        theme: "bootstrap",
        dropdownAutoWidth : true,
        width: "auto",
        allowClear: true,
        placeholder: "All Priorities",
        ajax: {
            url: '<?php echo url('/ajax/select/syslog'); ?>',
            delay: 200,
            data: function(params) {
                return {
                    field: "priority",
                    device: $('#device').val(),
                    term: params.term,
                    page: params.page || 1
                }
            }
        }
    })<?php echo isset($vars['priority']) ? ".val('" . htmlspecialchars($vars['priority']) . "').trigger('change');" : ''; ?>;
</script>


<?php
use Carbon\Carbon;
use App\Facades\ObzoraConfig;

$no_refresh = true;
$param = [];
$device_id = (int) $vars['device'];

$pagetitle[] = 'Outages';
?>
<div class="panel panel-default panel-condensed">
    <div class="panel-heading">
        <strong>Outages</strong>
    </div>

    <?php
    require_once 'includes/html/common/outages.inc.php';
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
        '<input name="from" type="text" class="form-control" id="dtpickerfrom" maxlength="16" value="<?php echo htmlspecialchars($vars['from']); ?>" placeholder="From" data-date-format="YYYY-MM-DD HH:mm">' +
        '</div>' +
        '<div class="form-group">' +
        '&nbsp;&nbsp;<input name="to" type="text" class="form-control" id="dtpickerto" maxlength="16" value="<?php echo htmlspecialchars($vars['to']); ?>" placeholder="To" data-date-format="YYYY-MM-DD HH:mm">' +
        '</div>' +
        '&nbsp;&nbsp;<button type="submit" class="btn btn-default">Filter</button>' +
        '</form>' +
        '</div>'
    );

    $(function () {
        var fromPicker = flatpickr("#dtpickerfrom", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            defaultDate: '<?php echo Carbon::now(session('preferences.timezone'))->subMonth()->format(ObzoraConfig::get('dateformat.byminute', 'Y-m-d H:i')); ?>',
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
            defaultDate: '<?php echo Carbon::now(session('preferences.timezone'))->format(ObzoraConfig::get('dateformat.byminute', 'Y-m-d H:i')); ?>',
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
            toPicker.set("maxDate", '<?php echo Carbon::now(session('preferences.timezone'))->format(ObzoraConfig::get('dateformat.byminute', 'Y-m-d H:i')); ?>');
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
</script>


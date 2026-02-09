<div style="text-align: center;">
    <form class="form-inline" id="customrange">
        <input type="hidden" id="selfaction" name="selfaction" value="<?php echo Request::url(); ?>">
        
        <!-- Relative Time Slot Selector -->
        <div class="form-group">
            <label for="relativeTimeSlot">Quick Time</label>
            <select class="form-control" id="relativeTimeSlot" name="relativeTimeSlot" style="margin-right: 10px;">
                <option value="">Custom Range</option>
                <optgroup label="Time Slots">
                    <option value="5m">Last 5 minutes</option>
                    <option value="10m">Last 10 minutes</option>
                    <option value="15m">Last 15 minutes</option>
                    <option value="30m">Last 30 minutes</option>
                    <option value="1h">Last 1 hour</option>
                    <option value="2h">Last 2 hours</option>
                    <option value="4h">Last 4 hours</option>
                    <option value="6h">Last 6 hours</option>
                    <option value="12h">Last 12 hours</option>
                    <option value="24h">Last 24 hours</option>
                </optgroup>
                <optgroup label="Relative Dates">
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="thisweek">This Week</option>
                    <option value="lastweek">Last Week</option>
                    <option value="thismonth">This Month</option>
                    <option value="lastmonth">Last Month</option>
                </optgroup>
            </select>
        </div>
        
        <!-- Custom Date Range (hidden by default, shown when Custom Range is selected) -->
        <div class="form-group" id="customDateRange">
            <label for="dtpickerfrom">From</label>
            <input type="text"
                   class="form-control"
                   id="dtpickerfrom"
                   name="dtpickerfrom"
                   maxlength="16"
                   data-date-format="YYYY-MM-DD HH:mm"
                   style="margin-right: 10px;">
        </div>
        <div class="form-group" id="customDateRangeTo">
            <label for="dtpickerto">To</label>
            <input type="text"
                   class="form-control"
                   id="dtpickerto"
                   name="dtpickerto"
                   maxlength="16"
                   data-date-format="YYYY-MM-DD HH:mm"
                   style="margin-right: 10px;">
        </div>
        
        <input type="submit"
               class="btn btn-default"
               id="submit"
               value="Update"
               onclick="submitCustomRange(this.form);">
        <button type="button"
                class="btn btn-default"
                id="clearFilter"
                style="margin-left: 10px;"
                title="Clear all date filters">
            Clear
        </button>
    </form>
    <script src="<?php echo asset('js/RrdGraphJS/moment-timezone-with-data.js'); ?>"></script>
    <script type="text/javascript">
        // Prevent multiple initializations
        (function() {
            if (window.dateSelectorInitialized) {
                console.warn("Date selector already initialized, skipping...");
                return;
            }
            window.dateSelectorInitialized = true;
            
            $(function () {
                // Wait for Flatpickr to be available
                if (typeof flatpickr === 'undefined') {
                    console.error("Flatpickr is not loaded");
                    return;
                }
            
            var ds_datefrom = new Date(<?php echo \ObzoraNMS\Util\Time::parseAt($graph_array['from']); ?>*1000);
            var ds_dateto = new Date(<?php echo \ObzoraNMS\Util\Time::parseAt($graph_array['to']); ?>*1000);
            var ds_tz = '<?php echo session('preferences.timezone'); ?>';
            
            // Convert to moment with timezone handling
            var moment_from, moment_to;
            if (ds_tz) {
                moment_from = moment.tz(ds_datefrom, ds_tz);
                moment_to = moment.tz(ds_dateto, ds_tz);
            } else {
                moment_from = moment(ds_datefrom);
                moment_to = moment(ds_dateto);
            }
            
            // Format dates for Flatpickr (Y-m-d H:i format)
            var fromDateStr = moment_from.format("YYYY-MM-DD HH:mm");
            var toDateStr = moment_to.format("YYYY-MM-DD HH:mm");
            
            // Convert to Date objects for Flatpickr
            var fromDateObj = moment_from.toDate();
            var toDateObj = moment_to.toDate();
            
            // Set input values immediately (before Flatpickr initialization)
            $("#dtpickerfrom").val(fromDateStr);
            $("#dtpickerto").val(toDateStr);
            
            // Check if elements exist and aren't already initialized
            var $fromInput = $("#dtpickerfrom");
            var $toInput = $("#dtpickerto");
            
            if ($fromInput.length === 0 || $toInput.length === 0) {
                console.error("Date picker inputs not found");
                return;
            }
            
            // Destroy existing instances if any
            if ($fromInput[0]._flatpickr) {
                $fromInput[0]._flatpickr.destroy();
            }
            if ($toInput[0]._flatpickr) {
                $toInput[0]._flatpickr.destroy();
            }
            
            // Initialize Flatpickr datetime pickers
            var fromPicker = flatpickr("#dtpickerfrom", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
                defaultDate: fromDateObj,
                allowInput: true,
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates[0] && toPicker) {
                        try {
                            toPicker.set("minDate", selectedDates[0]);
                        } catch (e) {
                            console.warn("Error setting minDate:", e);
                        }
                    }
                }
            });
            
            var toPicker = flatpickr("#dtpickerto", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
                defaultDate: toDateObj,
                allowInput: true,
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates[0] && fromPicker) {
                        try {
                            fromPicker.set("maxDate", selectedDates[0]);
                        } catch (e) {
                            console.warn("Error setting maxDate:", e);
                        }
                    }
                }
            });
            
            // Set initial min/max dates after a short delay to ensure pickers are ready
            setTimeout(function() {
                try {
                    if (fromPicker && toPicker && fromDateObj && toDateObj) {
                        // Verify dates are set correctly
                        var currentFrom = fromPicker.selectedDates[0];
                        var currentTo = toPicker.selectedDates[0];
                        
                        // If dates aren't set or are incorrect, set them again
                        if (!currentFrom || Math.abs(currentFrom.getTime() - fromDateObj.getTime()) > 1000) {
                            fromPicker.setDate(fromDateObj, false);
                        }
                        if (!currentTo || Math.abs(currentTo.getTime() - toDateObj.getTime()) > 1000) {
                            toPicker.setDate(toDateObj, false);
                        }
                        
                        // Set min/max constraints
                        toPicker.set("minDate", fromDateObj);
                        fromPicker.set("maxDate", toDateObj);
                    }
                } catch (e) {
                    console.warn("Error setting initial min/max dates:", e);
                }
            }, 100);

            // Handle relative time slot selection
            // Unbind any existing handlers to prevent duplicates
            $("#relativeTimeSlot").off('change.dateSelector');
            $("#relativeTimeSlot").on('change.dateSelector', function() {
                var selectedValue = $(this).val();
                // Get current time in the specified timezone (not convert from local time)
                // moment.tz(timezone) gets current time in that timezone
                // moment.tz(moment(), timezone) converts local time to that timezone (can shift dates)
                var now = ds_tz ? moment.tz(ds_tz) : moment();
                var from, to;
                
                // Debug: Log the current date to verify it's correct
                console.log("Current date calculation:", {
                    timezone: ds_tz || 'local',
                    nowDate: now.format("YYYY-MM-DD"),
                    nowTime: now.format("HH:mm:ss"),
                    nowFull: now.format("YYYY-MM-DD HH:mm:ss")
                });

                if (!selectedValue) {
                    // Show custom date range
                    $("#customDateRange").show();
                    $("#customDateRangeTo").show();
                    return;
                }

                // Hide custom date range for quick selections
                $("#customDateRange").hide();
                $("#customDateRangeTo").hide();

                // Calculate relative times
                switch(selectedValue) {
                    case '5m':
                        from = now.clone().subtract(5, 'minutes');
                        to = now.clone();
                        break;
                    case '10m':
                        from = now.clone().subtract(10, 'minutes');
                        to = now.clone();
                        break;
                    case '15m':
                        from = now.clone().subtract(15, 'minutes');
                        to = now.clone();
                        break;
                    case '30m':
                        from = now.clone().subtract(30, 'minutes');
                        to = now.clone();
                        break;
                    case '1h':
                        from = now.clone().subtract(1, 'hour');
                        to = now.clone();
                        break;
                    case '2h':
                        from = now.clone().subtract(2, 'hours');
                        to = now.clone();
                        break;
                    case '4h':
                        from = now.clone().subtract(4, 'hours');
                        to = now.clone();
                        break;
                    case '6h':
                        from = now.clone().subtract(6, 'hours');
                        to = now.clone();
                        break;
                    case '12h':
                        from = now.clone().subtract(12, 'hours');
                        to = now.clone();
                        break;
                    case '24h':
                        from = now.clone().subtract(24, 'hours');
                        to = now.clone();
                        break;
                    case 'today':
                        from = now.clone().startOf('day');
                        to = now.clone().endOf('day');
                        break;
                    case 'yesterday':
                        var yesterday = now.clone().subtract(1, 'days');
                        from = yesterday.clone().startOf('day');
                        to = yesterday.clone().endOf('day');
                        break;
                    case 'thisweek':
                        from = now.clone().startOf('week');
                        to = now.clone().endOf('week');
                        break;
                    case 'lastweek':
                        var lastWeek = now.clone().subtract(1, 'weeks');
                        from = lastWeek.clone().startOf('week');
                        to = lastWeek.clone().endOf('week');
                        break;
                    case 'thismonth':
                        from = now.clone().startOf('month');
                        to = now.clone().endOf('month');
                        break;
                    case 'lastmonth':
                        var lastMonth = now.clone().subtract(1, 'months');
                        from = lastMonth.clone().startOf('month');
                        to = lastMonth.clone().endOf('month');
                        break;
                    default:
                        $("#customDateRange").show();
                        $("#customDateRangeTo").show();
                        return;
                }

                // Update Flatpickr with calculated values
                try {
                    // Ensure dates are valid
                    if (!from || !to || !from.isValid() || !to.isValid()) {
                        console.error("Invalid date calculated:", {from: from, to: to});
                        return;
                    }
                    
                    // Format dates for Flatpickr (Y-m-d H:i format to match Flatpickr's dateFormat)
                    var fromStr = from.format("YYYY-MM-DD HH:mm");
                    var toStr = to.format("YYYY-MM-DD HH:mm");
                    
                    // Debug: Log the actual dates to verify they're different
                    console.log("Date calculation:", {
                        selection: selectedValue,
                        fromDate: from.format("YYYY-MM-DD"),
                        toDate: to.format("YYYY-MM-DD"),
                        fromTime: from.format("HH:mm:ss"),
                        toTime: to.format("HH:mm:ss"),
                        fromStr: fromStr,
                        toStr: toStr
                    });
                    
                    // Convert moment to Date object for Flatpickr
                    var fromDate = from.toDate();
                    var toDate = to.toDate();
                    
                    // Verify the dates are actually different
                    if (selectedValue === 'yesterday' && from.format("YYYY-MM-DD") === now.format("YYYY-MM-DD")) {
                        console.error("ERROR: Yesterday date is same as today!");
                    }
                    if (selectedValue === 'lastweek' && from.format("YYYY-[W]WW") === now.format("YYYY-[W]WW")) {
                        console.error("ERROR: Last week is same as this week!");
                    }
                    if (selectedValue === 'lastmonth' && from.format("YYYY-MM") === now.format("YYYY-MM")) {
                        console.error("ERROR: Last month is same as this month!");
                    }
                    
                    // Get picker instances directly from DOM elements (more reliable)
                    var $fromEl = $("#dtpickerfrom");
                    var $toEl = $("#dtpickerto");
                    var fromPickerInstance = $fromEl[0] ? $fromEl[0]._flatpickr : fromPicker;
                    var toPickerInstance = $toEl[0] ? $toEl[0]._flatpickr : toPicker;
                    
                    // Update Flatpickr instances using string format
                    // Flatpickr's dateFormat is "Y-m-d H:i", so "YYYY-MM-DD HH:mm" should work
                    if (fromPickerInstance && typeof fromPickerInstance.setDate === 'function') {
                        try {
                            // Try with Date object first (most reliable)
                            fromPickerInstance.setDate(fromDate, false);
                            // Verify it was set
                            setTimeout(function() {
                                if (fromPickerInstance.selectedDates.length > 0) {
                                    var setDate = fromPickerInstance.selectedDates[0];
                                    var expectedDate = fromDate.getTime();
                                    var actualDate = setDate.getTime();
                                    if (Math.abs(expectedDate - actualDate) > 60000) { // More than 1 minute difference
                                        console.warn("Date not set correctly, trying string format");
                                        fromPickerInstance.setDate(fromStr, false);
                                    }
                                }
                            }, 10);
                        } catch (e) {
                            console.error("Error setting from date:", e);
                            // Fallback to string format
                            fromPickerInstance.setDate(fromStr, false);
                        }
                    }
                    // Always update input value as backup
                    $fromEl.val(fromStr);
                    
                    if (toPickerInstance && typeof toPickerInstance.setDate === 'function') {
                        try {
                            // Try with Date object first
                            toPickerInstance.setDate(toDate, false);
                            // Verify it was set
                            setTimeout(function() {
                                if (toPickerInstance.selectedDates.length > 0) {
                                    var setDate = toPickerInstance.selectedDates[0];
                                    var expectedDate = toDate.getTime();
                                    var actualDate = setDate.getTime();
                                    if (Math.abs(expectedDate - actualDate) > 60000) {
                                        console.warn("Date not set correctly, trying string format");
                                        toPickerInstance.setDate(toStr, false);
                                    }
                                }
                            }, 10);
                        } catch (e) {
                            console.error("Error setting to date:", e);
                            // Fallback to string format
                            toPickerInstance.setDate(toStr, false);
                        }
                    }
                    // Always update input value as backup
                    $toEl.val(toStr);
                    
                    // Update min/max constraints after a short delay
                    setTimeout(function() {
                        try {
                            if (toPicker && typeof toPicker.set === 'function') {
                                toPicker.set("minDate", fromDate);
                            }
                            if (fromPicker && typeof fromPicker.set === 'function') {
                                fromPicker.set("maxDate", toDate);
                            }
                        } catch (e) {
                            console.warn("Error setting min/max dates:", e);
                        }
                    }, 50);
                    
                    // Verify the dates were set correctly
                    setTimeout(function() {
                        var actualFrom = $("#dtpickerfrom").val();
                        var actualTo = $("#dtpickerto").val();
                        console.log("Actual dates after update:", {
                            expectedFrom: fromStr,
                            actualFrom: actualFrom,
                            expectedTo: toStr,
                            actualTo: actualTo,
                            match: (actualFrom === fromStr && actualTo === toStr)
                        });
                    }, 100);
                    
                } catch (e) {
                    console.error("Error updating date pickers:", e);
                    // Fallback: just set the input values
                    $("#dtpickerfrom").val(from.format("YYYY-MM-DD HH:mm"));
                    $("#dtpickerto").val(to.format("YYYY-MM-DD HH:mm"));
                }
            });

            // Initially show custom date range by default
            $("#customDateRange").show();
            $("#customDateRangeTo").show();
            
            // Try to match current selection to a quick option (optional - for better UX)
            // This helps pre-select the quick option if the current range matches
            var currentFrom = moment_from.unix();
            var currentTo = moment_to.unix();
            var now = ds_tz ? moment.tz(moment(), ds_tz) : moment();
            var nowUnix = now.unix();
            var diff = nowUnix - currentTo;

            // Try to match current selection to a quick option
            if (Math.abs(diff) < 60) { // "To" is within last minute (current time)
                var fromDiff = currentTo - currentFrom;
                if (Math.abs(fromDiff - 300) < 30) { // 5 minutes
                    $("#relativeTimeSlot").val('5m');
                    $("#customDateRange").hide();
                    $("#customDateRangeTo").hide();
                } else if (Math.abs(fromDiff - 600) < 30) { // 10 minutes
                    $("#relativeTimeSlot").val('10m');
                    $("#customDateRange").hide();
                    $("#customDateRangeTo").hide();
                } else if (Math.abs(fromDiff - 900) < 30) { // 15 minutes
                    $("#relativeTimeSlot").val('15m');
                    $("#customDateRange").hide();
                    $("#customDateRangeTo").hide();
                } else if (Math.abs(fromDiff - 1800) < 60) { // 30 minutes
                    $("#relativeTimeSlot").val('30m');
                    $("#customDateRange").hide();
                    $("#customDateRangeTo").hide();
                } else if (Math.abs(fromDiff - 3600) < 60) { // 1 hour
                    $("#relativeTimeSlot").val('1h');
                    $("#customDateRange").hide();
                    $("#customDateRangeTo").hide();
                } else if (Math.abs(fromDiff - 7200) < 120) { // 2 hours
                    $("#relativeTimeSlot").val('2h');
                    $("#customDateRange").hide();
                    $("#customDateRangeTo").hide();
                } else if (Math.abs(fromDiff - 14400) < 240) { // 4 hours
                    $("#relativeTimeSlot").val('4h');
                    $("#customDateRange").hide();
                    $("#customDateRangeTo").hide();
                } else if (Math.abs(fromDiff - 21600) < 360) { // 6 hours
                    $("#relativeTimeSlot").val('6h');
                    $("#customDateRange").hide();
                    $("#customDateRangeTo").hide();
                } else if (Math.abs(fromDiff - 43200) < 720) { // 12 hours
                    $("#relativeTimeSlot").val('12h');
                    $("#customDateRange").hide();
                    $("#customDateRangeTo").hide();
                } else if (Math.abs(fromDiff - 86400) < 1440) { // 24 hours
                    $("#relativeTimeSlot").val('24h');
                    $("#customDateRange").hide();
                    $("#customDateRangeTo").hide();
                }
            }
            
            // Handle clear filter button
            $("#clearFilter").on('click', function(e) {
                e.preventDefault();
                
                // Reset Quick Time dropdown to "Custom Range"
                $("#relativeTimeSlot").val('');
                
                // Show custom date range inputs
                $("#customDateRange").show();
                $("#customDateRangeTo").show();
                
                // Get picker instances
                var $fromEl = $("#dtpickerfrom");
                var $toEl = $("#dtpickerto");
                var fromPickerInstance = $fromEl[0] ? $fromEl[0]._flatpickr : fromPicker;
                var toPickerInstance = $toEl[0] ? $toEl[0]._flatpickr : toPicker;
                
                // Clear Flatpickr date pickers
                if (fromPickerInstance) {
                    if (typeof fromPickerInstance.clear === 'function') {
                        fromPickerInstance.clear();
                    } else if (typeof fromPickerInstance.setDate === 'function') {
                        fromPickerInstance.setDate(null, false);
                    }
                }
                $fromEl.val('');
                
                if (toPickerInstance) {
                    if (typeof toPickerInstance.clear === 'function') {
                        toPickerInstance.clear();
                    } else if (typeof toPickerInstance.setDate === 'function') {
                        toPickerInstance.setDate(null, false);
                    }
                }
                $toEl.val('');
                
                // Reset min/max date constraints
                if (toPickerInstance && typeof toPickerInstance.set === 'function') {
                    toPickerInstance.set("minDate", null);
                }
                if (fromPickerInstance && typeof fromPickerInstance.set === 'function') {
                    fromPickerInstance.set("maxDate", null);
                }
                
                console.log("Filter cleared");
            });
            
            }); // End of $(function())
        })(); // End of IIFE
    </script>
</div>

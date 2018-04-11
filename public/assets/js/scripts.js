/**
 * @param options
 * id: table id(s),
 * columns: [] is array of numbers or one number,
 * optionName: null define default name of option,
 * scrollX '',
 * paging true,
 * ordering true,
 * info true,
 * search true
 * @param callback
 */

var initColumnFiltersForDataTable = function(options, callback) {
    var settings = {
        id: options.id,
        columns: options.columns !== undefined ? options.columns : [],
        optionName: options.optionName !== undefined ? options.optionName : '',
        scrollX: options.scrollX !== undefined ? options.scrollX : true,
        paging: options.paging !== undefined ? options.paging : true,
        ordering: options.ordering !== undefined ? options.ordering : true,
        info: options.info !== undefined ? options.info : true,
        search: options.search !== undefined ? options.search : true,
        phoneNumberTarget: options.phoneNumberTarget !== undefined ? options.phoneNumberTarget : null
    };
    var $table = $('#' + settings.id).DataTable({
        drawCallback: callback !== undefined ? callback : function () {
        },
        order: [],
        columnDefs: [
            {
                type: 'phoneNumber', targets: settings.phoneNumberTarget
            },
            {
                targets: 'nosort',
                orderable: false
            }
        ],
        scrollX: settings.scrollX,
        paging: settings.paging,
        ordering: settings.ordering,
        info: settings.info,
        bFilter: settings.search,
        initComplete: function () {
            this.api().columns(settings.columns).every(function () {
                var column = this;
                var select = $('<br><select id="selectFilter"><option value="">' + settings.optionName + '</option></select>')
                    .appendTo($(column.header()))
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                    });
                column.data().unique().sort().each(function (d) {
                    var selectId = '#selectFilter';
                    if (d == "") {
                        return true;
                    }
                    if (d.match(/<span/gi) || d.match(/<i/gi)) {
                        var selectValues = ($(d).text()).replace(/\s+/g, ' ').split(/(?=[A-ZÀ-ß][a-zà-ÿ])/);
                        for (var i = 0; i < selectValues.length; i++) {
                            selectValues[i] = selectValues[i].trim();
                            if (!($(selectId).find("option[value='" + selectValues[i] + "']").length > 0)) {
                                select.append('<option value="' + selectValues[i] + '">' + selectValues[i] + '</option>');
                            }
                        }
                    } else {
                        var val = $("<div/>").html(d).text().replace(/\s+/g, " ").trim();
                        select.append('<option value="' + val + '">' + val + '</option>');
                    }
                    sortSelectFilter(selectId);
                });
            });

            $('th select').click(function (event) {
                event.stopPropagation();
            });
        }
    });

    return $table;
};

var initDateRange = function ($table, from, to, col) {
    $.fn.dataTableExt.afnFiltering.push(
        function( oSettings, aData, iDataIndex ) {
            var iFini = document.getElementById(from).value;
            var iFfin = document.getElementById(to).value;
            var iStartDateCol = col;
            var iEndDateCol = col;

            iFini=iFini.substring(6,10) + iFini.substring(3,5)+ iFini.substring(0,2);
            iFfin=iFfin.substring(6,10) + iFfin.substring(3,5)+ iFfin.substring(0,2);

            var datofini=aData[iStartDateCol].substring(6,10) + aData[iStartDateCol].substring(3,5)+ aData[iStartDateCol].substring(0,2);
            var datoffin=aData[iEndDateCol].substring(6,10) + aData[iEndDateCol].substring(3,5)+ aData[iEndDateCol].substring(0,2);

            if ( iFini === "" && iFfin === "" )
            {
                return true;
            }
            else if ( iFini <= datofini && iFfin === "")
            {
                return true;
            }
            else if ( iFfin >= datoffin && iFini === "")
            {
                return true;
            }
            else if (iFini <= datofini && iFfin >= datoffin)
            {
                return true;
            }
            return false;
        }
    );
    $('#' + from + ', #' + to).datepicker({
        autoclose: true,
        orientation: 'top',
        todayHighlight: true,
        format:'dd/mm/yyyy',
    }).on('input change', function () {
        $table.draw();
    });
};

var sortSelectFilter = function (selectId) {
    var firstOption = $(selectId + ' option:first');
    var sortOptions = $(selectId + ' option:not(:first)').sort(function(a, b) {
        return a.text == b.text ? 0 : a.text < b.text ? -1 : 1;
    });
    $(selectId).html(sortOptions).prepend(firstOption);
}

var resizeDataTables = function (element) {
    $(element).bind('click', function() {
        setTimeout(function() {
            $(document).resize();
        }, 400);
    });
}

$(document).ready(function() {
    resizeDataTables('.sidebar-toggle');
});

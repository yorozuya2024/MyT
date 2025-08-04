/*
 * Validation logic and sum total
 */
//$(".charge").change(function (event) {
$(document).on('change', '.charge', function() {

	var inStrVal = $(this).val().replace(",", ".");
	var inFloatVal = parseFloat(inStrVal);

    //Start Numeric only
    if (isNaN(inFloatVal))
    {
        $(this).val(null);
    }
	else
	{
		$(this).val(inFloatVal);
	}
    //End

    //Start "Total" logic
    if ($(this).val() > 24)
    {
        $(this).addClass('error');
        var id = $(this).attr('id');
    }
    else
    {
        $(this).removeClass('error');
    }

    var row_name = $(this).attr('name').split('[');
    var row_num = row_name[2].replace(']', '');
    var all_by_row = row_name[0] + '[' + row_name[1] + '[' + row_name[2];
    var total = 0;

    $("[name*='" + all_by_row + "']").each(function() {
        var val = $(this).val();

        if (val != "")
        {
            total += parseFloat(val);
        }
    });

    $(".total_" + row_num + "").text(total);
    //End

    // Start Per day total
    var day_total = 0;
    var day = row_name[3].replace(']', '');

    $("[name^='Charge[charge_data]'][name$='[" + row_name[3] + "']").each(function() {
        var val = $(this).val();

        if (val != "")
        {
            day_total += parseFloat(val);
        }
    });
    $("#day_total_" + day + "").text(day_total);

    if (day_total > 24)
    {
        $("#day_total_" + day + "").addClass('error');
    }
    else
    {
        $("#day_total_" + day + "").removeClass('error');
    }
    //End Per day total


    //Start Total Overall
    var total_overall = 0;
    $("[id^='day_total_']").each(function() {
        var val = $(this).html();

        if (val != "" && val != "&nbsp;")
        {
            total_overall += parseFloat(val);
        }
    });

    $("#total_overall").text(total_overall);
    //End Total Overall

    //Disable submit button
    if ($("*").hasClass('error'))
    {
        $("input[type='submit']").attr('disabled', 'disabled');
    }
    else
    {
        $("input[type='submit']").removeAttr('disabled');
    }
    //End disable button

});

/*
 * Hide "wbs" selector
 */
$(document).ready(function() {
    var oDate = new Date();

    MyTGridLoaded(oDate.getDate());
});

function MyTGridLoaded(day)
{
    $(".wbs-select").each(function() {
        if ($(this).val() == "")
        {
            $(this).hide();
        }
        else
        {
            $(this).click();
// FIX           $(this).change();
            $(this).focusout();
        }
    });

    $(".task-select").each(function() {
        if ($(this).val() == "")
        {
            $(this).hide();
        }
        else
        {
            $(this).click();
            $(this).change();
            $(this).focusout();
        }
    });
}

/*
 * Enable charge cell
 */

//$(".wbs-cell").click(function() {
$(document).on("click", ".wbs-cell", function() {
    var id = $(this).attr('id');
    var row_id = id.split('-')[0];
    var all_input_row = 'Charge[charge_data][' + row_id + ']';

    $(".selected").removeClass('selected');
    $(this).parent().addClass('selected');

    if (!$("#row-" + row_id + "-wbs").is(":visible"))
    {
        $("#row-" + row_id + "-wbs").show();

        $("#row-" + row_id + "-placeholder").text("");


        $("[name*='" + all_input_row + "']").each(function() {
            $(this).removeAttr('disabled');
        });

        //Dirty trick
        $("#row-" + row_id + "-wbs").focus();
    }
});

/*
 *  Enable charge only if wbs is selected
 */

//$(".wbs-select").change(function() {
$(document).on('click', '.wbs-select', function() {
    $(".wbs-select").change(function()
    {
        var id = $(this).attr('id');
        var row_id = id.split('-')[1];
        var all_input_row = 'Charge[charge_data][' + row_id + ']';
        var val = parseInt($(this).val());

        if (val <= 0 || isNaN(val))
        {
            $("[name*='" + all_input_row + "']").each(function() {
                $(this).val('');
                $(this).change();

                $(this).attr('disabled', 'disabled');
            });
        }
        else
        {
            $("[name*='" + all_input_row + "']").each(function() {
                $(this).removeAttr('disabled');
            });

            // Not used for the moment
            //$(this).data("prev",$(this).val());
        }

        //Show task select
        /*
         if (!$("#row-" + row_id + "-task").is(":visible"))
         {
         $("#row-" + row_id + "-taskplaceholder").text("");
         $("#row-" + row_id + "-task").show();
         }
         */
    });
});

//$(".wbs-select").focusout(function() {
$(document).on("focusout", ".wbs-select", function() {
    var id = $(this).attr('id');
    var row_id = id.split('-')[1];
    var all_input_row = 'Charge[charge_data][' + row_id + ']';
    var val = parseInt($(this).val());

    if (val <= 0 || isNaN(val))
    {
        $("[name*='" + all_input_row + "']").each(function() {
            $(this).val('');
            $(this).change();
            $(this).attr('disabled', 'disabled');
        });

        $("#row-" + row_id + "-task").hide(); //Hide task cell
        $(this).hide();

    }
    else
    {
        $("[name*='" + all_input_row + "']").each(function() {
            $(this).removeAttr('disabled');
        });

        $("#row-" + row_id + "-placeholder").text($(this).find(":selected").text());
        $(this).hide();

        /*
         if( $(this).data("prev") != "undefined" && $(this).data("prev") != null && $(this).data("prev") != "" )
         {
         alert($(this).data("prev"));
         $(".wbs-select option[value='"+$(this).data("prev")+"']").removeAttr('disabled');
         }

         $(".wbs-select option[value='"+$(this).find(":selected").val()+"']").attr('disabled', 'disabled');

         */
    }

});

$(document).on("focusout", ".task-select", function() {
    var id = $(this).attr('id');
    var row_id = id.split('-')[1];
    var val = parseInt($(this).val());

    $("#row-" + row_id + "-taskplaceholder").text($(this).find(":selected").text());
    $(this).hide();

});

$(document).on("click", ".task-cell", function() {
    var id = $(this).attr('id');
    var row_id = id.split('-')[0];

    /*
     $(".selected").removeClass('selected');
     $(this).parent().addClass('selected');
     */

    if (!$("#row-" + row_id + "-task").is(":visible") && $("#row-" + row_id + "-placeholder").text() != "")
    {
        $("#row-" + row_id + "-task").show();

        $("#row-" + row_id + "-taskplaceholder").text("");

        //Dirty trick
        $("#row-" + row_id + "-task").focus();
    }
});


function toggleHalf(first, second)
{
    var text = first;

    if (!$(".first_quindicina").is(":visible"))
    {
        $(".first_quindicina").show();
        $(".second_quindicina").hide();
        text = second;
    }
    else
    {
        $(".second_quindicina").show();
        $(".first_quindicina").hide();
    }

    $("#qtoggle").html(text + ' &gt;&gt;');

    return false;
}

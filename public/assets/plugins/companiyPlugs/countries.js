jQuery( document ).ready(function() {

    function formatState (state) {
        if (!state.id) { return state.text; }
        var jQuerystate = jQuery(
            '<span><img src="'+flagsPatch + state.element.value.toLowerCase()+'.png" class="img-flag" /> ' + state.text + '</span>'
        );
        return jQuerystate;
    };

    jQuery("#country_list").select2({
        placeholder: '',
        templateResult: formatState
    });

    jQuery("#country_list").on("select2:select", function (e) {


        var selectedValue = jQuery("#country_list").val();
        var datasending = { countryName : selectedValue};

        jQuery.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': jQuery('input[name="_token"]').attr('value')},
            url: Path+'employer/AjaxGetCities',
            data: JSON.stringify(datasending),
            contentType: "application/json; charset=utf-8",
            traditional: true,
            success: function (data) {
                jQuery(".city_list").select2().empty();
                jQuery(".city_list").select2({
                    // allowClear: true,
                    data: data,
                    // placeholder: ''
                });
                jQuery(".city_list").prop("disabled", false);
            }
        });

    });

    // jQuery(".city_list").select2({
        // allowClear: true,
        // placeholder: ''
    // });

    jQuery("#employees_count").select2({
        // allowClear: true,
        placeholder: ''
    });

});
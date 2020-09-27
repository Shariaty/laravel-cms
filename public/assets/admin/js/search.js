$( document ).ready(function() {

    var flagsPatch = relativePath+'employer/plugins/flags/';
    var ajaxPath = relativePath+'employer/AjaxGetStates';

    function formatState (state) {
        if (!state.id) { return state.text; }
        var $state = $(
            '<span><img src="'+flagsPatch + state.element.value.toLowerCase()+'.png" class="img-flag" /> ' + state.text + '</span>'
        );
        return $state;
    };

    jQuery(function($){
        $(document).ajaxStart(function() {
            $('<div class="overlay"><div/>').appendTo('body');
            $("body").css("cursor","wait");
            $('#ajaxLoader').show();
        });
        $(document).ajaxStop(function() {
            $(".overlay").hide();
            $("body").css("cursor","default");
            $('#ajaxLoader').hide();
        });
    });

    $("#country_list").select2({
        placeholder: 'Select country',
        templateResult: formatState
    });

    $("#country_list").on("select2:select", function (e) {

        $("#city_list").prop("disabled", true);
        $("#city_list").select2().empty();

        var selectedValue = $("#country_list").val();
        var datasending = { countryName : selectedValue};

        $.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')},
            url: ajaxPath ,
            data: JSON.stringify(datasending),
            contentType: "application/json; charset=utf-8",
            traditional: true,
            success: function (data) {
                $("#state_list").select2().empty()
                $("#state_list").select2({
                    data: data
                });
                $("#state_list").prop("disabled", false);
            }
        });
    });

    $("#state_list").select2({
        placeholder: 'Select state',
        templateResult: formatState
    }).prop("disabled", true);

    $("#state_list").on("select2:select", function (e) {

        var selectedValue = $("#state_list").val();
        var datasending = { stateName : selectedValue};

        $.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')},
            url: ajaxPath+'employer/AjaxGetCities',
            data: JSON.stringify(datasending),
            contentType: "application/json; charset=utf-8",
            traditional: true,
            success: function (data) {
                console.log(data);
                $("#city_list").prop("disabled", true);
                $("#city_list").select2().empty();
                $("#city_list").select2({
                    data: data
                });
                $("#city_list").prop("disabled", false);
            }
        });
    });

    $("#city_list").select2({
        placeholder: 'Select city',
        templateResult: formatState
    }).prop("disabled", true);

    var yearsList = [{id: '', text: ''},{id: '0', text: 'Not important'},{id: '1000', text: 'Trainee'}];
    for (var i = 1; i <= 50; i++) {
        yearsList.push({id: i, text: i});
    }

    $(".experience").select2({
        placeholder: 'Select years',
        data : yearsList
    });

    $("#job_categories").select2({
        placeholder: 'Select categories .. ',
        maximumSelectionLength: 3
    });


});

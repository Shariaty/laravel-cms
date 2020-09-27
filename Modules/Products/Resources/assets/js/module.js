var dataTableNewsList;
var M6Module =
        {
        global: function () {
            //Ajax Wrapper
            $(document).ajaxStart(function () {
                $(document).find('body').append('<div class="ajax-wrapper"></div>');
            });
            $(document).ajaxStop(function () {
                $(document).find('.ajax-wrapper').hide();
            });
            //Ajax Wrapper
        },
        getUrlVars: function () {
            var vars = [], hash;
            var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for (var i = 0; i < hashes.length; i++) {
                hash = hashes[i].split('=');
                vars.push(hash[0]);
                vars[hash[0]] = hash[1];
            }
            return vars;
        },
        tabInitialize: function () {
            $(document).on('click', '[data-event-tab]', function () {

                setTimeout(function(){
                    $('.select2').select2({
                        placeholder: ''
                    });
                }, 500);


                if (history.replaceState) {
                    var nameTab = $(this).data('tab-data');
                    history.replaceState('data', '', window.location.protocol + '//' + window.location.host + window.location.pathname + '?status=' + nameTab);
                }
            });
            function manageTabListPost() {
                var urlVars = M6Module.getUrlVars();
                if (typeof urlVars['status'] === 'undefined') {
                    history.replaceState('data', '', window.location.protocol + '//' + window.location.host + window.location.pathname + '?status=active');
                    $('a[href="#active"]').click();
                }
                else {
                    history.replaceState('data', '', window.location.protocol + '//' + window.location.host + window.location.pathname + '?status=' + urlVars['status']);
                    $('a[href="#' + urlVars['status'] + '"]').click();
                }
            }

            manageTabListPost();
        },
        initTinyMCE: function () {
            tinymce.init({
                selector: '.mce',
                theme: 'modern',
                height: 200,
                plugins: [
                    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                    'searchreplace wordcount visualblocks visualchars fullscreen',
                    'insertdatetime media nonbreaking save table contextmenu directionality',
                    'emoticons template paste textcolor colorpicker textpattern imagetools responsivefilemanager code directionality'
                ],
                toolbar1: "fontselect | fontsizeselect | undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
                toolbar2: "responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code | ltr rtl | removeformat ",
                image_advtab: true,
                relative_urls: false,
                remove_script_host: false,
                external_filemanager_path: PublicPath + "filemanager/",
                filemanager_title: "Responsive Filemanager",
                external_plugins: {"filemanager": PublicPath + "assets/plugins/tinymce/plugins/responsivefilemanager/plugin.min.js"}
            });
        },
        ajaxOrderingSystem: function (URL) {
            var fixHelper = function (e, ui) {
                ui.children().each(function () {
                    $(this).width($(this).width());
                });
                return ui;
            };
            var sortableLinks = $("#menu");
            $(sortableLinks).sortable({
                opacity: 0.6,
                cursor: 'move',
                helper: fixHelper,
                update: function (event, ui) {
                    var data = $("#menu").sortable('toArray', {attribute: "data-item"});

                    $.ajax({
                        async: true,
                        url: Path + URL,
                        headers: {'X-CSRF-TOKEN': _csrf_token},
                        type: 'POST',
                        dataType: 'json',
                        contentType: 'application/json',
                        data: JSON.stringify(data)
                    });
                }
            });
        },
        countryCities: function () {

            function formatState (state) {
                if (!state.id) { return state.text; }
                var jQuerystate = $(
                    '<span><img src="'+flagsPatch + state.element.value.toLowerCase()+'.png" class="img-flag" /> ' + state.text + '</span>'
                );
                return jQuerystate;
            }

            $("#country_list").select2({
                placeholder: 'Select ...',
                templateResult: formatState
            });

            $("#country_list").on("select2:select", function (e) {
                var selectedValue = jQuery("#country_list").val();
                var datasending = { countryName : selectedValue};

                $.ajax({
                    type: 'post',
                    headers: {'X-CSRF-TOKEN': jQuery('input[name="_token"]').attr('value')},
                    url: Path+'AjaxGetCities',
                    data: JSON.stringify(datasending),
                    contentType: "application/json; charset=utf-8",
                    traditional: true,
                    success: function (data) {
                        jQuery(".city_list").select2().empty();
                        jQuery(".city_list").select2({
                            data: data,
                            placeholder: 'Select ...'
                        });
                        jQuery(".city_list").prop("disabled", false);
                    }
                });

            });
            $(".city_list").select2({
                placeholder: 'Select ...'
            });
        } ,
        formValidation: function (form) {
            statusFormValid = true;
            $('.BoxError').html('');
            if(document.getElementById(form))
                $('#' + form).validate({
                    submitHandler: function (data) {
                        statusFormValid = true;
                        return true;
                    },
                    ignore: "",
                    errorElement: "div",
                    rules: {
                        title: {
                            required: true,
                            maxlength: 100
                        },
                        desc: {
                            required: false,
                            maxlength: 5000
                        },
                        category: {
                            required: true
                        } ,
                        manufacturer: {
                            required: true
                        },
                        type: {
                            required: true
                        },
                        mainUnit_id: {
                            required: true
                        },
                        subUnit: {
                            required: true
                        },
                        conversion_factor: {
                            required: function(){
                                return $("#subUnit_list").val() !== '0';
                            }
                        },
                        price: {
                            required: true
                        },
                        value: {
                            required: function(){
                                return $("#raw_material").val() === '2';
                            },
                            number: true,
                            min: 0
                        },
                        build_from: {
                            required: function(){
                                return $("#raw_material").val() === '2';
                            }
                        },
                        limitValue: {
                            required: function(){
                                return $("#on-off-switch").is(":checked");
                            } ,
                            number: true,
                            min: 0
                        }

                    },
                    messages: {
                        checkValidIsUrgent:{
                            'min' : 'You can only mark one job as urgent!',
                            'max' : 'You can only mark one job as urgent!'
                        }
                    },
                    errorPlacement: function (error, element) {
                        $(element).parents('.input-group:first').addClass('has-error');
                        $(element).parents('.form-group:first').addClass('has-error');

                        $(element).parents('.form-group:first').find('.validation-message-block:first').html(error[0].innerHTML);

                        var questionElementError = $(element).parents('.question:first').find('.questionShowError:first');
                        error.insertAfter(questionElementError);
                        statusFormValid = false;
                    },
                    success: function(error, element) {
                        $(element).parents('.input-group:first').removeClass('has-error');
                        $(element).parents('.form-group:first').removeClass('has-error');
                        statusFormValid = true;

                    },
                    error: function(error, element) {
                        $(element).parents('.input-group:first').addClass('has-error');
                        $(element).parents('.form-group:first').addClass('has-error');
                        statusFormValid = false;
                        console.log(4);
                    }
                });

            $('.product_selector').each(function () {
                $(this).rules('add', {
                    required: true
                });
            });

            $('.convert').each(function () {
                $(this).rules('add', {
                    required: true ,
                    min: 0 ,
                    minlength: 1,
                    maxlength: 10
                });
            });
        },
        ajaxStatusChange: function (URL) {
            $(document).on('click', '.status-change', function () {
                var element = $(this);
                var status = $(this).data('status');
                var userId = $(this).data('new');
                var datasending = {status: (status == 'Y' ? 'N' : 'Y'), user_id: userId};
                $.ajax({
                    type: 'post',
                    headers: {'X-CSRF-TOKEN': _csrf_token},
                    url: Path + URL,
                    data: JSON.stringify(datasending),
                    contentType: "application/json; charset=utf-8",
                    traditional: true,
                    success: function (data) {
                        if (data.status == 'error') {
                            console.log(data.message);
                        } else {
                            $(element).hide();
                            if (data.newStatus == 'Y') {
                                $(element).parent('td:first').append('<button class="btn btn-xs btn-default status-change" data-status="Y" data-new="' + userId + '">' +
                                    '<i class="fa fa-check fa-1x text-success"></i>' +
                                    '</button>');
                            } else {
                                $(element).parent('td:first').append('<button class="btn btn-xs btn-default status-change" data-status="N" data-new="' + userId + '">' +
                                    '<i class="fa fa-ban fa-1x text-danger"></i>' +
                                    '</button>');
                            }
                        }

                    }
                });
            });
        },
        ajaxDelete: function(identity , url) {
            $.ajax({
                url: Path+url+identity,
                type: "POST",
                data: {
                    _token: _csrf_token
                },
                success: function(response){
                    if(response.status == 'success'){
                        toastr.success(response.message);
                        dataTableNewsList.ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        },
        ajaxFileUpload: function () {
                var url = Path+'products/ajaxFileUpload';
                var id = $('#identifier').data('id');

                $('#FileUpload').fileupload({
                    url: url,
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')},
                    maxNumberOfFiles: 1 ,
                    formData: { id: id },
                    progressall: function (e, data) {
                        var progress = parseInt(data.loaded / data.total * 100, 10);
                        $('#progress .progress-bar').css(
                            'width', progress + '%'
                        );
                    },
                    success: function (data) {
                        if(data.success === true){
                            $("#progressBar").removeClass("progress-bar-danger");
                            $("#progressBar").addClass( "progress-bar-success");
                            $("#logo-thumb-container").html('').append('<i class="fa fa-check green" aria-hidden="true"></i>');
                            $("#upload-title").html('Change resume file');
                            $("#upload-box").pulsate("destroy");
                            $('#UPDL').hide();
                            $("#logo-error-reporting").html('');
                            $('#logo-thumb-container').html('<a href="'+Path+'magazines/fileView/'+data.slug+'" target="_blank" class="resume-upload-link btn btn-xs btn-info" style="margin-left: 3px;"><i class="fa fa-eye fa-1x"></i>&nbsp;View</a>' +
                                '<a href="'+Path+'magazines/fileRemove/'+id+'" style="margin-left:5px" class="resume-upload-link confirmation-remove btn btn-xs btn-danger"><i class="fa fa-times"></i></a>');
                        } else {
                            console.log(data);
                            $("#logo-error-reporting").html(data.message[0]);
                            $("#progressBar").removeClass( "progress-bar-success");
                            $("#progressBar").addClass( "progress-bar-danger");
                        }
                    }
                }).prop('disabled', !$.support.fileInput)
                    .parent().addClass($.support.fileInput ? undefined : 'disabled');
},
        productList: function () {
                var dataTableAjaxURL = Path+'products/dataTables';
                var dataTable = $('#products-table').DataTable({
                    processing: true,
                    serverSide: true,
                    order: [[6, 'desc']],
                    responsive: true,
                    ajax: {
                        type : 'post',
                        url  : dataTableAjaxURL,
                        data : {
                            _token : _csrf_token
                        }
                    },
                    initComplete : function () {
                        $('#products-table').show();
                    },
                    columnDefs: [
                        { name: 'is_published'},
                        { name: 'visible_sku'},
                        { name: 'title'},
                        { name: 'category'},
                        { name: 'subProducts'},
                        { name: 'type'},
                        { name: 'created_at'},
                        { name: 'action' }
                    ],
                    columns: [
                        { data: 'is_published', sortable: true , searchable: false},
                        { data: 'visible_sku' , sortable: false , searchable: true},
                        { data: 'title'      , sortable: false , searchable: true},
                        { data: 'category'   , sortable: false , searchable: false},
                        { data: 'subProducts', sortable: false , searchable: false},
                        { data: 'type'      , sortable: true , searchable: false},
                        { data: 'created_at' , sortable: true , searchable: false},
                        { data: 'action'    , sortable: false , searchable: false}
                    ]
                });

                $(document).on('click' , '.delete_btn' , function (e) {
                    var st_number = $(this).data('id');
                    swal({
                        title: "Remove this item !",
                        text : "You are about to remove an item from server , this action can not be undone ! Do you want to proceed ?",
                        type: "error",
                        showCancelButton: true,
                        confirmButtonColor: "#31c7b2",
                        cancelButtonColor: "#DD6B55",
                        confirmButtonText: "Remove",
                        cancelButtonText: "Cancel"
                    }).then(function(result) {
                        if (result.value) {
                            AjaxDelete(st_number);
                        }
                    });

                });
                function AjaxDelete(co) {
                    $.ajax({
                        url: Path+'products/delete/'+co,
                        type: "POST",
                        data: { _token: _csrf_token },
                        success: function(response){
                            if(response.status == 'success'){
                                toastr.success(response.message);
                                dataTable.ajax.reload();
                            } else {
                                toastr.error(response.message);
                            }
                        }
                    });
                }
            },
        productsCatList: function () {
                var dataTable = $('#staticDataTable').DataTable({
                    processing: true,
                    serverSide: false,
                    responsive: true,
                    order: false ,
                    initComplete : function () {
                        $('#staticDataTable').show();
                    },
                    columns: [
                        { target: 0 , searchable: false , sortable: false},
                        { target: 1 , searchable: true , sortable: false},
                        { target: 2 , searchable: false , sortable: false},
                        { target: 3 , searchable: false , sortable: false},
                        { target: 4 , searchable: false , sortable: false},
                        { target: 5 , searchable: false , sortable: false},
                        { target: 6 , searchable: false , sortable: false}
                    ]
                });
            } ,
        product: function () {
                var formId = 'FormStore';
                var form = '#' + formId;

                $("#cat_list").select2({
                    placeholder: '',
                    minimumResultsForSearch: 0
                });

                $("#attributes_list").select2({
                    minimumResultsForSearch: -1 ,
                    placeholder: 'Select Option'
                });

                $("#attributes_list").on("select2:select", function (e) {
                    var selectedValue = jQuery("#attributes_list").val();
                    var datasending = { variantName : selectedValue};

                    $.ajax({
                        type: 'post',
                        headers: {'X-CSRF-TOKEN': jQuery('input[name="_token"]').attr('value')},
                        url: Path+'products/ajaxGetValues',
                        data: JSON.stringify(datasending),
                        contentType: "application/json; charset=utf-8",
                        traditional: true,
                        success: function (data) {

                            console.log(data);

                            jQuery("#values_list").select2().empty();
                            jQuery("#values_list").select2({
                                data: data,
                                placeholder: 'Select ...',
                                minimumResultsForSearch: -1
                            });
                            jQuery("#values_list").prop("disabled", false);
                        }
                    });

                });

                $("#values_list").select2({
                    minimumResultsForSearch: -1 ,
                    placeholder: 'Select value ...' ,
                    disabled: true
                });

                $(document).on('click', '#btnCompanyCreate', function () {
                    var sliderValues = handlesSlider.noUiSlider.get();
                    M6Module.formValidation(formId);
                    var formValid = $(form).valid();
                    console.log(formValid);

                    if (formValid){
                        $('#ageRange').val(sliderValues);
                        $(form)[0].submit();
                    }
                });

                var converted = $.map(list, function (obj) {
                    return obj;
                });

                $("#options_list").select2({
                    data: converted ,
                    placeholder: 'Select Options',
                    multiple: true
                });

                var convertedManufactures = $.map(manulist, function (obj) {
                    return obj;
                });

                $("#manufacturer_list").select2({
                    data: convertedManufactures ,
                    placeholder: 'Select ...',
                    minimumResultsForSearch: 0
                }).prop("disabled", false);


                $("#cat_list").on("select2:select", function (e) {
                    var selectedValue = jQuery("#cat_list").val();
                    var dataSending = { categoryId : selectedValue};

                    $.ajax({
                        type: 'post',
                        headers: {'X-CSRF-TOKEN': jQuery('input[name="_token"]').attr('value')},
                        url: Path+'products/AjaxGetOptions',
                        data: JSON.stringify(dataSending),
                        contentType: "application/json; charset=utf-8",
                        success: function (data) {
                            if(!data.ageStatus) {
                                $('#slider-handles').hide();
                            } else {
                                $('#slider-handles').show();
                            }

                            var converted = $.map(data.options, function (obj) {
                                return obj;
                            });
                            var convertedManufactures = $.map(data.manufactures, function (obj) {
                                return obj;
                            });

                            $("#options_list").select2().empty();
                            $("#options_list").select2({
                                data: converted,
                                placeholder: 'Select ...'
                            }).prop("disabled", false);

                            $("#manufacturer_list").select2().empty();
                            $("#manufacturer_list").select2({
                                data: convertedManufactures,
                                placeholder: 'Select ...'
                            }).prop("disabled", false);
                        }
                    });

                });

                $("#raw_material").on("select2:select", function (e) {
                    var selectedValue = jQuery("#raw_material").val();
                    if(parseInt(selectedValue) === 2) {
                        $('.combinationClass').show();
                    } else {
                        $('.combinationClass').hide();
                    }
                });

                // NoUI Slider
                var handlesSlider = document.getElementById('slider-handles');
                var range_all_sliders = {
                    // 'min' : 0 ,
                    // '7.5%':   4 ,
                    // '15%':  7 ,
                    // '22.5%' :  10 ,
                    // '30%' :  13 ,
                    // '37.5%' :  16 ,
                    // '45%' :  19 ,
                    // '52.5%' :  22 ,
                    // '60%' :  25 ,
                    // '67.5%' :  28 ,
                    // '75%' :  31 ,
                    // '82.5%' :  34 ,
                    // '90%' :  37 ,
                    // 'max' :  40

                    'min'  : 1 ,
                    '2.5%' : 2 ,
                    '5%'   : 3 ,
                    '7.5%' : 4 ,
                    '10%'  : 5 ,
                    '12.5%': 6 ,
                    '15%'  : 7 ,
                    '17.5%': 8 ,
                    '20%'  : 9 ,
                    '22.5%': 10 ,
                    '25%'  : 11 ,
                    '27.5%': 12 ,
                    '30%'  : 13 ,
                    '32.5%': 14 ,
                    '35%'  : 15 ,
                    '37.5%': 16 ,
                    '40%'  : 17 ,
                    '42.5%': 18 ,
                    '45%'  : 19 ,
                    '47.5%': 20 ,
                    '50%'  : 21 ,
                    '52.5%': 22 ,
                    '55%'  : 23 ,
                    '57.5%': 24 ,
                    '60%'  : 25 ,
                    '62.5%': 26 ,
                    '65%'  : 27 ,
                    '67.5%': 28 ,
                    '70%'  : 29 ,
                    '72.5%': 30 ,
                    '75%'  : 31 ,
                    '77.5%': 32 ,
                    '80%'  : 33 ,
                    '82.5%': 34 ,
                    '85%'  : 35 ,
                    '87.5%': 36 ,
                    '90%'  : 37 ,
                    '92.5%': 38 ,
                    '95%'  : 39 ,
                    '97.5%': 40 ,
                    'max'  : 41
                };

                noUiSlider.create(handlesSlider, {
                    connect: true,
                    snap: true,
                    start: [ 1, 6 ],
                    range: range_all_sliders ,
                    tooltips: [ true , true ],
                    step: 1,
                    pips: {
                        mode: 'range',
                        density: 1.5
                    }
                });

                if( age ) {
                    handlesSlider.noUiSlider.set(age);
                }

                $('.noUi-value.noUi-value-horizontal.noUi-value-large').each(function(){
                    var val = $(this).html();
                    val = recountVal(parseInt(val));
                    $(this).html(val);
                });

                handlesSlider.noUiSlider.on('update', function(values, handle){
                    handleHandle(values , handle);
                });

                function handleHandle(values , handle) {
                    $('.noUi-handle[data-handle="'+handle+'"] .noUi-tooltip')
                        .text(recountFullVal(parseFloat(values[handle])));
                }

                function recountVal(val){
                    switch(val){
                        case 1 :return '1 week';break;
                        case 4 :return '4 week';break;
                        case 7 :return '7 week';break;
                        case 10:return '4 months';break;
                        case 13:return '7 months';break;
                        case 16:return '10 months';break;
                        case 19:return '13 months';break;
                        case 22:return '16 months';break;
                        case 25:return '19 months';break;
                        case 28:return '22 months';break;
                        case 31:return '3 Year';break;
                        case 34:return '6 Year';break;
                        case 37:return '9 Year';break;
                        case 40:return '12 Year';break;
                        default : return ''; break;
                    }
                }

                function recountFullVal(val){
                switch(val){
                    case 1 :return '1 week';break;
                    case 2 :return '2 week';break;
                    case 3 :return '3 week';break;
                    case 4 :return '4 week';break;
                    case 5 :return '5 week';break;
                    case 6 :return '6 week';break;
                    case 7 :return '7 week';break;
                    case 8 :return '2 months';break;
                    case 9 :return '3 months';break;
                    case 10:return '4 months';break;
                    case 11:return '5 months';break;
                    case 12:return '6 months';break;
                    case 13:return '7 months';break;
                    case 14:return '8 months';break;
                    case 15:return '9 months';break;
                    case 16:return '10 months';break;
                    case 17:return '11 months';break;
                    case 18:return '12 months';break;
                    case 19:return '13 months';break;
                    case 20:return '14 months';break;
                    case 21:return '15 months';break;
                    case 22:return '16 months';break;
                    case 23:return '17 months';break;
                    case 24:return '18 months';break;
                    case 25:return '19 months';break;
                    case 26:return '20 months';break;
                    case 27:return '21 months';break;
                    case 28:return '22 months';break;
                    case 29:return '23 months';break;
                    case 30:return '24 months';break;
                    case 31:return '3 Year';break;
                    case 32:return '4 Year';break;
                    case 33:return '5 Year';break;
                    case 34:return '6 Year';break;
                    case 35:return '7 Year';break;
                    case 36:return '8 Year';break;
                    case 37:return '9 Year';break;
                    case 38:return '10 Year';break;
                    case 39:return '11 Year';break;
                    case 40:return '12 Year';break;
                    case 41:return '12 Years old +';break;
                    default : return ''; break;
                }
            }

                if(!ageEnable){
                    $('#slider-handles').hide();
                }
                // NoUI Slider

                $("#subUnit_list").on("select2:select", function (e) {
                    var selectedValue = jQuery("#subUnit_list").val();
                    if (selectedValue === "0"){
                        $("#conversion_factor").prop("disabled", true);
                    } else {
                        $("#conversion_factor").prop("disabled", false);
                    }
                });

            },
        subProductsList: function () {
            var dataTable = $('#staticDataTable').DataTable({
                processing: true,
                serverSide: false,
                responsive: true,
                order: [[4, 'desc']],
                initComplete : function () {
                    $('#staticDataTable').show();
                },
                columns: [
                    { target: 0 , searchable: false , sortable: false},
                    { target: 1 , searchable: true , sortable: false},
                    { target: 2 , searchable: false , sortable: false},
                    { target: 3 , searchable: false , sortable: false},
                    { target: 5 , searchable: false , sortable: true},
                    { target: 6 , searchable: false , sortable: true}
                ]
            });

            $('.price').mask("000,000,000,000,000", {reverse: true});

        } ,
        subProduct: function(){
            var formId = 'FormStore';
            var form = '#' + formId;

            $(document).on('click', '#btnCreate', function () {
                M6Module.formValidation(formId);
                var formValid = $(form).valid();
                if (formValid){
                    $(form)[0].submit();
                }
            });

            $(".select2").select2({
                placeholder: '',
                minimumResultsForSearch: -1
            });

            $(document).on('click', '.add-bom', function () {
                M6Module.newRow();
            });

            $(document).on('click', '.remove-item-row', function () {
                M6Module.deleteRow(this);
            });

            $(document).on('select2:select', '.product_selector', function (e) {
                var el = $(this).parents('tr:first').find('.rawUnit:first');

                var selectedValue = $(this).val();
                var datasending = {id: selectedValue};
                $.ajax({
                    type: 'post',
                    headers: {'X-CSRF-TOKEN': jQuery('input[name="_token"]').attr('value')},
                    url: Path + 'products/ajaxGetBomUnit',
                    data: JSON.stringify(datasending),
                    contentType: "application/json; charset=utf-8",
                    traditional: true,
                    success: function (data) {
                        console.log(data);
                        if (data.status === 'success') {
                            $(el).html('<span class="text-success farsi-text">' + data.unit.title + '</span>');
                            $(el).fadeOut(200).fadeIn(200).fadeOut(200).fadeIn(200);
                        } else {
                            swal({
                                title: "Product info is missing",
                                text: data.message,
                                type: "error",
                                showCancelButton: true,
                                confirmButtonColor: "#31c7b2",
                                cancelButtonColor: "#DD6B55",
                                cancelButtonText: "Ok"
                            })
                        }
                    }
                });
            });

        } ,
        categoryAddEdit : function () {

                var formId = 'ProductCategoryForm';
                var form = '#' + formId;

                $("#parent_list").select2({
                    placeholder: 'Select parent category'
                });

                $("#options_list").select2({
                    placeholder: 'Select Option types',
                    minimumResultsForSearch: 0
                });

                $("#manufactures_list").select2({
                    placeholder: 'Select Manufactures'
                });

                function _generateSortableList() {
                    var order = [];
                    $("#sortable").children().each(function (i) {
                        var li = $(this);
                        order.push(parseInt(li.attr('id')));
                    });
                    return order;
                }

                $("#sortable").sortable({
                    update: function () {
                        var order = [];
                        $("#sortable").children().each(function (i) {
                            var li = $(this);
                            order.push( li.attr('id') );
                        });
                    }
                });

                $(document).on("click" , '#btn_post' , function (e) {
                    e.preventDefault();

                    var order = _generateSortableList();

                    $('#variants-values').val(order);

                    M6Module.formValidation(formId);
                    var formValid = $(form).valid();
                    if (formValid){
                        $(form)[0].submit();
                    }
                });

                $(document).on("click" , '.remove-item' , function (e) {
                    e.preventDefault();
                    $(this).remove();
                });

                $('#AddVariants').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget);
                    var id = button.data('category-id');
                    var title = button.data('category-title');

                    var result = '';
                    result += '<ul>';
                    for (var item in attributes) {
                        result += '<li><a data-identifier="'+item+'" data-title="'+attributes[item]+'" class="identifier">'+attributes[item]+'</a></li>';
                    }
                    result += '</ul>';

                    var modal = $(this);
                    modal.find('.modal-body').html(result);
                });

                $(document).on('click' , '.identifier' , function (e) {
                    e.preventDefault();
                    var identifier = $(this).data('identifier');
                    var title = $(this).data('title');

                    var order = _generateSortableList();

                    if(order.length >= limit) {
                        alert('Maximum exceed');
                    } else if($.inArray(parseInt(identifier), order) !== -1){
                        alert('Error');
                    } else  {
                        $('#sortable').append('<li id="'+identifier+'" class="remove-item farsi-text">'+title+'</li>');
                    }

                });

            },
        newRow: function () {
                $(".item-row:last").after(
                    '<tr class="item-row">' +
                    '<td class="item-name">' +
                    '<div class="form-group">' +
                    '<div class="delete-btn">' +
                    '<select class="form-control item product_selector" name="rawProduct[]" style="width: 100%;"></select>' +
                    '<span class="validation-message-block"></span>' +
                    '<a class="remove-item-row delete" href="javascript:;" title="Remove row">X</a>' +
                    '</div>' +
                    '</div>' +
                    '</td>' +
                    '<td>' +
                    '<div class="form-group">' +
                    '<input class="form-control convert" name="convert[]['+Math.random()+']" placeholder="conversion" type="number">' +
                    '<span class="validation-message-block"></span>' +
                    '</div>' +
                    '</td>' +
                    '<td>' +
                    '<div>' +
                    '<span class="rawUnit"></span>' +
                    '</div>' +
                    '</td>' +
                    '</tr>'
                );

                $('.item').select2({
                    placeholder: '',
                    data: rawProductsListForJs
                });

                return 1;
            },
        deleteRow: function (elem) {
            $(elem).parents().find('.item-row:last').remove();

            // if (jQuery($.opt.delete).length < 2) {
            //     jQuery($.opt.delete).hide();
            // }

            return 1;
        }
        };


M6Module.global();

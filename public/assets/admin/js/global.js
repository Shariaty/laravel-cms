var M6 =
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

            //Toaster Options
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-full-width",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "200",
                "hideDuration": "500",
                "timeOut": "500000000000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            //Toaster Options

            //Remove Sweet Alert
            $(document).on('click' , '.confirmation-remove' , function (e) {
                var href = jQuery(this).attr('href');
                var items = $(this).data('items');

                if(parseInt(items) && parseInt(items) > 0){
                    swal({
                        title: "Category contain some items!",
                        text : "This category contains items in it, so you can not remove it , first get rid of the items in it and after that you can remove this category.",
                        type: "warning",
                        confirmButtonColor: "#31c7b2",
                        confirmButtonText: "OK"
                    });
                } else {
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
                            window.location.href = href;
                        }
                    });
                }
                return false;
            });
            //Remove Sweet Alert

            //Remove Sweet Alert
            $(document).on('click' , '.confirmation-mass-remove' , function (e) {
                e.preventDefault();
                var href = jQuery(this).attr('href');
                swal({
                    title: "Remove All of these items!",
                    text : "You are about to remove all items from this list, this action can not be undone ! Do you want to proceed ?",
                    type: "error",
                    showCancelButton: true,
                    confirmButtonColor: "#31c7b2",
                    cancelButtonColor: "#DD6B55",
                    confirmButtonText: "Remove",
                    cancelButtonText: "Cancel"
                }).then(function(result) {
                    if (result.value) {
                        window.location.href = href;
                    }
                });

            });
            //Remove Sweet Alert

            //Logout Sweet Alert
            $(document).on('click' , '#confirmation-logout' , function (e) {
                var href = jQuery(this).attr('href');

                swal({
                    title: "You are about to log out of you account!",
                    showCancelButton: true,
                    confirmButtonColor: "#31c7b2",
                    cancelButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    cancelButtonText: "Cancel"
                }).then(function(result) {
                    if (result.value) {
                        window.location.href = href;
                    }
                });

                return false;
            });
            //Logout Sweet Alert

            //Lock Sweet Alert
            $(document).on('click' ,'#confirmation-lock' , function (e) {
                var href = jQuery(this).attr('href');
                swal({
                    title: "Lock your session ?",
                    text : "You are about to lock your session , you can unlock it through unlock screen later , Do you want to proceed ?",
                    showCancelButton: true,
                    confirmButtonColor: "#31c7b2",
                    cancelButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    cancelButtonText: "Cancel"
                }).then(function(result) {
                    if (result.value) {
                        window.location.href = href;
                    }
                });
                return false;
            });
            //Lock Sweet Alert

            //ClearAll Message Sweet Alert
            $(document).on('click' ,'#btnDeleteMessage' , function (e) {
                var href = jQuery(this).attr('href');
                swal({
                    title: "Delete All",
                    text: "Are you sure you want to delete all these items?",
                    showCancelButton: true,
                    confirmButtonColor: "#31c7b2",
                    cancelButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    cancelButtonText: "Cancel"
                }).then(function(result) {
                    if (result.value) {
                        window.location.href = href;
                    }
                });
                return false;
            });
            //ClearAll Message Sweet Alert

            // Pulsate Initialize
            $(".pulsate").pulsate({ color: '#f80400' });

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
        tabInitialize: function () {
            $(document).on('click', '[data-event-tab]', function () {
                if (history.replaceState) {
                    var nameTab = $(this).data('tab-data');
                    history.replaceState('data', '', window.location.protocol + '//' + window.location.host + window.location.pathname + '?status=' + nameTab);
                }
            });
            function manageTabListPost() {
                var urlVars = M6.getUrlVars();
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
        company: function () {
            var formId = 'FormCompanyEmployer';
            var form = '#' + formId;

            $( '.select2' ).select2({
                placeholder: 'Select ...',
            });
            $('#city_list').select2({
                placeholder: ''
            });
            $('#city_list').select2({
                placeholder: ''
            });

            $('#company_fields').select2({
                minimumSelectionLength: 1,
                minimumResultsForSearch: -1
            });

            $('#subActivityFields').select2({
                placeholder: '',
                minimumResultsForSearch: -1
            });

            $('#company_activity').select2({
                placeholder: '',
                minimumResultsForSearch: -1
            }).on('change', function (e) {
                var element = $(this);
                var idActivity = parseInt($(element).val());
                if (!isNaN(idActivity)) {
                    $.ajax({
                        'type': 'POST',
                        'dataType': 'json',
                        'url': urlGetSubActivities,
                        'data': {_token: _csrf_token, idActivity: idActivity},
                        success: function (data) {
                            console.log(data);
                            var status = data.status;
                            $('#subActivityFields').empty();
                            if (status && data.activity.length > 0) {
                                var activity = data.activity;
                                $('#subActivityFields').select2({
                                    placeholder: '',
                                    minimumResultsForSearch: -1,
                                    data: activity
                                }).enable(true);
                            } else {
                                var activity = null;
                                $('#subActivityFields').select2({
                                    placeholder: 'Has No sub activities',
                                    minimumResultsForSearch: -1,
                                    data: activity
                                }).enable(false);
                            }
                        }
                    });
                }
            });

            $(document).on('click', '#btnCompanySubmit', function () {
                M6.postFormValid(formId);
                var formValid = $(form).valid();
                if (formValid) {
                    swal({
                        title: "Company Similarity check",
                        html: "if you think it has, you can paste <strong>co_number</strong> in the box below to replace old one with new approved company." +
                        "<br> <a target='_blank' href='/companies/list' class='btn btn-xs btn-default margin-top-10'><i class='fa fa-search'></i> Search in companies list</a>",
                        input: 'text',
                        inputPlaceholder: 'paste co_number here ...',
                        showCancelButton: true,
                        confirmButtonColor: "#31c7b2",
                        cancelButtonColor: "#7d9f00",
                        confirmButtonText: "Approve & Replace",
                        cancelButtonText: "Approve Only",
                        showCloseButton: true,
                        reverseButtons: true ,
                        inputValidator: function (value) {
                            var regex = new RegExp('^(co-)[0-9]{7}$');
                            if(!value || !regex.test(value)){
                                return true && 'You need to write something and shout be in the correct format!';
                            }
                        }
                    }).then(function (result) {
                        if (result.value) {
                            $(form).append("<input type='hidden' name='change_co_number' id='change_co_number' value='"+ result.value +"'/>");
                            $(form)[0].submit();
                        } else if (result.dismiss === 'cancel') {
                            $('#change_co_number:first').remove();
                            $(form)[0].submit();
                        }
                    });

                }
            });

            $(document).on('click', '#btnCompanyCreate', function () {
                M6.postFormValid(formId);
                var formValid = $(form).valid();
                if (formValid){
                    $(form)[0].submit();
                }
            });


            $(document).on('click', '#btnCompanyReject', function () {
                var ajaxCompanyProfileRejectionUrl = Path+'approval/companyProfileReject';
                var identifier = $('#co_number').data('id');

                M6.postFormValid(formId);
                swal.setDefaults({
                    confirmButtonText: 'Next &rarr;',
                    showCancelButton: true,
                    progressSteps: ['1', '2']
                });

                var steps = [
                    {
                        title: 'What is your reason for rejection ?',
                        input: 'select',
                        confirmButtonColor: "#31c7b2",
                        cancelButtonColor: "#dd4c61",
                        confirmButtonText: "Continue",
                        cancelButtonText: "Cancel",
                        reverseButtons: true,
                        inputOptions:rejectMessages,
                        preConfirm: function (dataFromPreviousStep) {
                            if(dataFromPreviousStep === 'OTHER'){
                                return new Promise(function (resolve) {
                                    swal.insertQueueStep({
                                        title: 'Describe why',
                                        input: 'text',
                                        confirmButtonColor: "#31c7b2",
                                        cancelButtonColor: "#dd4c61",
                                        confirmButtonText: "Reject",
                                        cancelButtonText: "Cancel",
                                        reverseButtons: true,
                                        inputValidator: function (value) {
                                            if(!value || value.length < 4){
                                                return true && 'You need to write something more than 4 characters!'
                                            }
                                        }
                                    });
                                    resolve();
                                });
                            } else {
                                return new Promise(function (resolve) {
                                    swal.insertQueueStep({
                                        title: '',
                                        text: 'you choose '+dataFromPreviousStep+' , are you sure you want to reject?',
                                        confirmButtonColor: "#31c7b2",
                                        cancelButtonColor: "#dd4c61",
                                        confirmButtonText: "Yes , Reject",
                                        cancelButtonText: "Cancel",
                                        reverseButtons: true
                                    });
                                    resolve();
                                });
                            }
                        }
                    }
                ];

                swal.queue(steps).then(function (result) {
                    swal.resetDefaults();
                    if (result.value) {
                        var finalData = null;
                        if(result.value[0] === 'OTHER'){
                            finalData = result.value[1]
                        } else {
                            finalData = result.value[0]
                        }

                        $.ajax({
                            'type': 'POST',
                            'dataType': 'json',
                            'url': ajaxCompanyProfileRejectionUrl,
                            'data': {_token: _csrf_token, identifier: identifier , defMessage: finalData},
                            success: function (data) {
                                if(data.status === 'success'){
                                    toastr.success(data.message);
                                } else if (data.status === 'warning') {
                                    toastr.warning(data.message);
                                    window.location.href = data.url;
                                } else {
                                    toastr.error('Something went wrong, contact you administrator');
                                }
                            }
                        });
                    }
                });
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
        postFormValid: function (form) {
            statusFormValid = true;
            $('.BoxError').html('');
            if(document.getElementById(form))
                $('#' + form).validate({
                    submitHandler: function (data) {
                        statusFormValid = true;
                        return true;
                    },
                    ignore: '',
                    errorElement: "div",
                    rules: {
                        country: {
                            required: true
                        },
                        city: {
                            required: true
                        },
                        desc: {
                            required: true,
                            minlength: 30,
                            maxlength: 3000
                        },
                        p_desc: {
                            required: true,
                            minlength: 30,
                            maxlength: 3000
                        },
                        title: {
                            required: true,
                        },
                        p_title: {
                            required: true,
                        },
                        national_id: {
                            required: true,
                            maxlength: 11,
                            number: true
                        },
                        address: {
                            required: true,
                            maxlength: 500
                        },
                        p_address: {
                            required: true,
                            maxlength: 500
                        },
                        employees_count: {
                            required: true,
                        },
                        'company_fields[]': {
                            required: true,
                        },
                        company_type: {
                            required: true,
                        },
                        company_activity: {
                            required: true,
                        },
                        phoneCompany: {
                            required: true,
                            number: true
                        },
                        name: {
                            required: true,
                        },
                        phone: {
                            required: true,
                            number: true
                        },
                        telephone: {
                            number: true
                        },
                        email: {
                            required: true,
                            email: true
                        },

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

            $('.select2').on('change', function () {
                $('#' + form).valid();
            });
        },
        approvalList: function () {
            var dataTable = $('#approval-table').DataTable({
                processing: true,
                serverSide: false,
                responsive: true,
                initComplete : function () {
                    $('#approval-table').show();
                },
                columns: [
                    { target: 0 , searchable: false , sortable: true},
                    { target: 1 , searchable: true , sortable: false},
                    { target: 2 , searchable: false , sortable: false},
                    { target: 3 , searchable: false , sortable: false}
                ]
            });

            function doTheAjax( url , identifier , type){
                $.ajax({
                    'type': 'POST',
                    'dataType': 'json',
                    'url': url,
                    'data': {_token: _csrf_token, identifier: identifier},
                    success: function (data) {
                        if(data.status === 'success'){
                            toastr.success(data.message);
                            if(type = 1){
                                $('#TR-'+identifier).remove();
                            }
                        } else if (data.status === 'warning') {
                            toastr.warning(data.message);
                            if(type = 1){
                                $('#TR-'+identifier).remove();
                            }
                        } else {
                            toastr.error('Something went wrong, contact you administrator');
                        }
                    }
                });
            }

            $(document).on('click' , '.approve-profile-image' , function (e) {
                var ajaxProfileImageApproveUrl = Path+'approval/profileImageApprove';
                var ajaxProfileImageRejectUrl = Path+'approval/profileImageReject';
                var identifier = $(this).data('id');
                swal({
                    title: "What is you decision ?",
                    showCancelButton: true,
                    confirmButtonColor: "#31c7b2",
                    cancelButtonColor: "#dd4c61",
                    confirmButtonText: "Approve",
                    cancelButtonText: "Reject",
                    showCloseButton: true,
                    reverseButtons: true ,
                }).then(function (result) {
                    if (result.value) {
                        doTheAjax(ajaxProfileImageApproveUrl , identifier , 1);
                    } else if (result.dismiss === 'cancel') {
                        doTheAjax(ajaxProfileImageRejectUrl , identifier , 1);
                    }
                });

            });

            $(document).on('click' , '.approve-company-logo' , function (e) {
                var ajaxProfileImageApproveUrl = Path+'approval/companyLogoApprove';
                var ajaxProfileImageRejectUrl = Path+'approval/companyLogoReject';
                var identifier = $(this).data('id');
                swal({
                    title: "What is you decision ?",
                    showCancelButton: true,
                    confirmButtonColor: "#31c7b2",
                    cancelButtonColor: "#dd4c61",
                    confirmButtonText: "Approve",
                    cancelButtonText: "Reject",
                    showCloseButton: true,
                    reverseButtons: true ,
                }).then(function (result) {
                    if (result.value) {
                        doTheAjax(ajaxProfileImageApproveUrl , identifier , 1);
                    } else if (result.dismiss === 'cancel') {
                        doTheAjax(ajaxProfileImageRejectUrl , identifier , 1);
                    }
                });

            });

            $(document).on('click' , '.approve-company-cover' , function (e) {
                var ajaxProfileImageApproveUrl = Path+'approval/companyCoverApprove';
                var ajaxProfileImageRejectUrl = Path+'approval/companyCoverReject';
                var identifier = $(this).data('id');
                swal({
                    title: "What is you decision ?",
                    showCancelButton: true,
                    confirmButtonColor: "#31c7b2",
                    cancelButtonColor: "#dd4c61",
                    confirmButtonText: "Approve",
                    cancelButtonText: "Reject",
                    showCloseButton: true,
                    reverseButtons: true ,
                }).then(function (result) {
                    if (result.value) {
                        doTheAjax(ajaxProfileImageApproveUrl , identifier , 1);
                    } else if (result.dismiss === 'cancel') {
                        doTheAjax(ajaxProfileImageRejectUrl , identifier , 1);
                    }
                });

            });

        }
    };

M6.global();
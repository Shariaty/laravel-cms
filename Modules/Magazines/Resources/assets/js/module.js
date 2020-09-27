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
        base64MimeType : function (encoded) {
            var result = null;
            if (typeof encoded !== 'string') {
                return result;
            }
            var mime = encoded.match(/data:([a-zA-Z0-9]+\/[a-zA-Z0-9-.+]+).*,.*/);
            if (mime && mime.length) {
                result = mime[1];
            }

            return result;
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
                height: 210,
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
                    ignore: '',
                    errorElement: "div",
                    rules: {
                        title: {
                            required: true
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
        itemList: function () {
                var dataTableAjaxURL = Path+'magazines/dataTables';
                var dataTable = $('#items-table').DataTable({
                    processing: true,
                    serverSide: true,
                    order: [[4, 'desc']],
                    responsive: true,
                    ajax: {
                        type : 'post',
                        url  : dataTableAjaxURL,
                        data : {
                            _token : _csrf_token
                        }
                    },
                    initComplete : function () {
                        $('#items-table').show();
                    },
                    columnDefs: [
                        { name: 'is_published'},
                        { name: 'title'},
                        { name: 'categories'},
                        { name: 'downloads'},
                        { name: 'created_at'},
                        { name: 'action' }
                    ],
                    columns: [
                        { data: 'is_published'    , sortable: true , searchable: false},
                        { data: 'title'     , sortable: false , searchable: true},
                        { data: 'categories' , sortable: false  , searchable: false},
                        { data: 'downloads'     , sortable: false  , searchable: false},
                        { data: 'created_at', sortable: true , searchable: false},
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
                        url: Path+'magazines/delete/'+co,
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
        catList: function () {
                var dataTable = $('#staticDataTable').DataTable({
                    processing: true,
                    serverSide: false,
                    responsive: true,
                    order: [[3, 'desc']],
                    initComplete : function () {
                        $('#staticDataTable').show();
                    },
                    columns: [
                        { target: 0 , searchable: false , sortable: false},
                        { target: 1 , searchable: false , sortable: false},
                        { target: 2 , searchable: false , sortable: false},
                        { target: 3 , searchable: false , sortable: false},
                        { target: 4 , searchable: false , sortable: false},
                        { target: 5 , searchable: false , sortable: false}
                    ]
                });
            },
        magazine: function () {

            $("#cat_list").select2({
                placeholder: 'Select Categories',
                maximumSelectionLength: 2,
                minimumResultsForSearch: -1
            });

            // Modal image crop & upload
            var formId = 'MagazineForm';
            var form = '#' + formId;

            var ajaxUploadUrl = Path+'magazines/ajaxImageUpload';
            var ajaxImageDelete = Path+'magazines/ajaxImageRemove';
            var image = $('.crop-box-container').data('image');
            var id    = $('.crop-box-container').data('id');

            if (!image){
                VImage = PublicPath+'assets/admin/images/placeholder.jpg';
            } else {
                VImage = PublicPath+'uploads/admins/magazine-pictures/'+image;
            }
            var options = {
                thumbBox: '.thumbBox',
                spinner: '.spinner',
                imgSrc: VImage
            };
            var cropper = $('.imageBox').cropbox(options);

            $('#file').on('change', function(){
                var acceptedTypes = ['image/jpeg' , 'image/png'];
                var reader = new FileReader();
                reader.onload = function(e) {
                    var type = M6Module.base64MimeType(e.target.result);
                    if(!$.inArray(type, acceptedTypes)) {
                        options.imgSrc = e.target.result;
                        cropper = $('.imageBox').cropbox(options);
                    } else {
                        toastr.error('It is not an acceptable file type!');
                    }
                };
                reader.readAsDataURL(this.files[0]);
                // this.files = [];
            });
            $('#btnZoomIn').on('click', function(){
                cropper.zoomIn();
            });
            $('#btnZoomOut').on('click', function(){
                cropper.zoomOut();
            });
            $('#remove-image').on('click', function(){

                var $btn = $(this).button('loading');

                var sendingData = {
                    _token: _csrf_token,
                    id : id
                };
                console.log(id);

                $.ajax({
                    type: 'post',
                    url: ajaxImageDelete,
                    data: sendingData,
                    dataType: 'json',
                    traditional: true,
                    success: function (data) {
                        if(data.status == 'success'){
                            window.location.reload();
                            toastr.success('', data.message);
                        } else {
                            toastr.error('', data.message);
                            $btn.button('reset')
                        }
                    }
                });
            });

            $('#save-update-btn').on('click', function(e){
                e.preventDefault();
                var img = cropper.getDataURL();
                $('#finalFile').val(img);

                 M6Module.formValidation(formId);
                 var formValid = $(form).valid();

                 if (formValid){
                      $(form)[0].submit();
                 }
            })
        },
        ajaxFileUpload: function () {
            var url = Path+'magazines/ajaxFileUpload';
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
        }

    };

M6Module.global();

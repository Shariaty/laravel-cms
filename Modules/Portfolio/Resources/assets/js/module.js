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
                        price: {
                            required: false
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
                var url = Path+'portfolio/ajaxFileUpload';
                var id = $('#identifier').data('id');

                $('#catalog #FileUpload').fileupload({
                    url: url,
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')},
                    maxNumberOfFiles: 1 ,
                    formData: { id: id , type: "catalog" },
                    progressall: function (e, data) {
                        var progress = parseInt(data.loaded / data.total * 100, 10);
                        $('#catalog #progress .progress-bar').css(
                            'width', progress + '%'
                        );
                    },
                    success: function (data) {
                        if(data.success === true){
                            $("#catalog #progressBar").removeClass("progress-bar-danger");
                            $("#catalog #progressBar").addClass( "progress-bar-success");
                            $("#catalog #logo-thumb-container").html('').append('<i class="fa fa-check green" aria-hidden="true"></i>');
                            $("#catalog #upload-title").html('Change file');
                            $("#catalog #upload-box").pulsate("destroy");
                            $('#catalog #UPDL').hide();
                            $("#catalog #logo-error-reporting").html('');
                            $('#catalog #logo-thumb-container').html('<a href="'+Path+'portfolio/fileView/'+id+'/catalog" target="_blank" class="resume-upload-link btn btn-xs btn-info" style="margin-left: 3px;"><i class="fa fa-eye fa-1x"></i>&nbsp;View</a>' +
                                '<a href="'+Path+'portfolio/fileRemove/'+id+'/catalog" style="margin-left:5px" class="resume-upload-link confirmation-remove btn btn-xs btn-danger"><i class="fa fa-times"></i></a>');
                        } else {
                            $("#catalog #logo-error-reporting").html(data.message[0]);
                            $("#catalog #progressBar").removeClass( "progress-bar-success");
                            $("#catalog #progressBar").addClass( "progress-bar-danger");
                        }
                    }
                }).prop('disabled', !$.support.fileInput)
                    .parent().addClass($.support.fileInput ? undefined : 'disabled');


            $('#sheet #FileUpload').fileupload({
                url: url,
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')},
                maxNumberOfFiles: 1 ,
                formData: { id: id , type: "sheet" },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#sheet #progress .progress-bar').css(
                        'width', progress + '%'
                    );
                },
                success: function (data) {
                    if(data.success === true){
                        $("#sheet #progressBar").removeClass("progress-bar-danger");
                        $("#sheet #progressBar").addClass( "progress-bar-success");
                        $("#sheet #logo-thumb-container").html('').append('<i class="fa fa-check green" aria-hidden="true"></i>');
                        $("#sheet #upload-title").html('Change file');
                        $("#sheet #upload-box").pulsate("destroy");
                        $('#sheet #UPDL').hide();
                        $("#sheet #logo-error-reporting").html('');
                        $('#sheet #logo-thumb-container').html('<a href="'+Path+'portfolio/fileView/'+id+'/sheet" target="_blank" class="resume-upload-link btn btn-xs btn-info" style="margin-left: 3px;"><i class="fa fa-eye fa-1x"></i>&nbsp;View</a>' +
                            '<a href="'+Path+'portfolio/fileRemove/'+id+'/sheet" style="margin-left:5px" class="resume-upload-link confirmation-remove btn btn-xs btn-danger"><i class="fa fa-times"></i></a>');
                    } else {
                        $("#sheet #logo-error-reporting").html(data.message[0]);
                        $("#sheet #progressBar").removeClass( "progress-bar-success");
                        $("#sheet #progressBar").addClass( "progress-bar-danger");
                    }
                }
            }).prop('disabled', !$.support.fileInput)
                .parent().addClass($.support.fileInput ? undefined : 'disabled');
        },
        productList: function () {
                var dataTableAjaxURL = Path+'portfolio/dataTables';
                var dataTable = $('#products-table').DataTable({
                    processing: true,
                    serverSide: true,
                    order: [[3, 'desc']],
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
                        { name: 'title'},
                        { name: 'category'},
                        { name: 'created_at'},
                        { name: 'action' }
                    ],
                    columns: [
                        { data: 'is_published', sortable: true , searchable: false},
                        { data: 'title'      , sortable: false , searchable: true},
                        { data: 'category'   , sortable: false , searchable: false},
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
                        url: Path+'portfolio/delete/'+co,
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
                        { target: 4 , searchable: false , sortable: false}
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

                $("#designer_list").select2({
                    placeholder: '',
                    minimumResultsForSearch: 0
                });

                $(document).on('click', '#btnCompanyCreate', function () {
                    M6Module.formValidation(formId);
                    var formValid = $(form).valid();

                    if (formValid){
                        $(form)[0].submit();
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
        designersList: function () {
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
                    { target: 4 , searchable: false , sortable: false}
                ]
            });
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
                    url: Path + 'portfolio/ajaxGetBomUnit',
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
            return 1;
            },
        designer: function () {

            $("#cat_list").select2({
                placeholder: 'Select Categories',
                maximumSelectionLength: 2,
                minimumResultsForSearch: -1
            });

            // Modal image crop & upload
            var formId = 'MagazineForm';
            var form = '#' + formId;

            var ajaxUploadUrl = Path+'portfolio/designers/ajaxImageUpload';
            var ajaxImageDelete = Path+'portfolio/designers/ajaxImageRemove';
            var image = $('.crop-box-container').data('image');
            var id    = $('.crop-box-container').data('id');

            if (!image){
                VImage = PublicPath+'assets/admin/images/placeholder.jpg';
            } else {
                VImage = PublicPath+'uploads/admins/designer-pictures/'+image;
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
        }
        };

M6Module.global();

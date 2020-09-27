var dataTableValidityList;
var M6Module =
    {
        global: function () {
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
        ajaxFileUpload: function () {
            var url = Path+'slide-show/ajaxFileUpload';
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
                        $('#catalog #logo-thumb-container').html('<a href="'+Path+'slide-show/fileView/'+data.slug+'/catalog" target="_blank" class="resume-upload-link btn btn-xs btn-info" style="margin-left: 3px;"><i class="fa fa-eye fa-1x"></i>&nbsp;View</a>' +
                            '<a href="'+Path+'slide-show/fileRemove/'+id+'/catalog" style="margin-left:5px" class="resume-upload-link confirmation-remove btn btn-xs btn-danger"><i class="fa fa-times"></i></a>');
                    } else {
                        $("#catalog #logo-error-reporting").html(data.message[0]);
                        $("#catalog #progressBar").removeClass( "progress-bar-success");
                        $("#catalog #progressBar").addClass( "progress-bar-danger");
                    }
                }
            }).prop('disabled', !$.support.fileInput)
                .parent().addClass($.support.fileInput ? undefined : 'disabled');
        },
    };

M6Module.global();

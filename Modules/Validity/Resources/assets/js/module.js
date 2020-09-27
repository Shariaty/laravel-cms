var dataTableValidityList;
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
                var dataTableAjaxURL = Path+'validity/dataTables';
                dataTableValidityList = $('#items-table').DataTable({
                    processing: true,
                    serverSide: true,
                    order: [[2, 'desc']],
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
                        { name: 'identification'},
                        { name: 'created_at'},
                        { name: 'action' }
                    ],
                    columns: [
                        { data: 'is_published' , sortable: false , searchable: false},
                        { data: 'title' , sortable: false , searchable: true},
                        { data: 'identification' , sortable: false , searchable: true},
                        { data: 'created_at', sortable: true , searchable: false},
                        { data: 'action' , sortable: false , searchable: false}
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
                        url: Path+'validity/delete/'+co,
                        type: "POST",
                        data: { _token: _csrf_token },
                        success: function(response){
                            if(response.status == 'success'){
                                toastr.success(response.message);
                                dataTableValidityList.ajax.reload();
                            } else {
                                toastr.error(response.message);
                            }
                        }
                    });
                }
        },
        ajaxFileUpload: function () {
            var url = Path+'validity/ajaxFileUpload';

            $('#FileUpload').fileupload({
                url: url,
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')},
                maxNumberOfFiles: 1 ,
                formData: { id: null },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress .progress-bar').css(
                        'width', progress + '%'
                    );
                },
                success: function (data) {
                    if(data.success === true){
                        dataTableValidityList.ajax.reload();
                        $('#counter-view').html(`( Total : ${data.message} )`);
                        $("#progressBar").removeClass("progress-bar-danger");
                        $("#progressBar").addClass( "progress-bar-success");
                        $("#logo-thumb-container").html('').append('<i class="fa fa-check green checkbox-icon" aria-hidden="true"></i>');
                        $("#upload-title").html('Excel file has been imported successfully (Import another)');
                        $("#upload-box").pulsate("destroy");
                        $('#UPDL').hide();
                        $("#logo-error-reporting").html('');
                    } else {
                        $("#logo-error-reporting").html(data.message[0]);
                        $("#progressBar").removeClass( "progress-bar-success");
                        $("#progressBar").addClass( "progress-bar-danger");
                    }
                }
            }).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');
        }
    };

M6Module.global();

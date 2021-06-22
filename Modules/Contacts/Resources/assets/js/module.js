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
        ajaxStatusChange: function (URL , identifier) {
            var Id = identifier;
            var datasending = {is_read: 'Y', identifier: Id};
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
                        $('#icon-'+identifier).html('<i class="fa fa-eye fa-1x text-success"></i>');
                        var counter =  parseInt($('#message-counter').data('counter'));
                        if(counter && counter > 0){
                            var newCounter =  counter-1 ;
                            $('#message-counter').data('counter' , newCounter);
                            if(newCounter > 0){
                                $('#message-counter').html(newCounter);
                            } else if (newCounter === 0){
                                $('#message-counter').hide();
                            }
                        }
                    }
                }
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
                var dataTableAjaxURL = Path+'contacts/dataTables';
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
                        { name: 'is_read'},
                        { name: 'sender'},
                        { name: 'subject'},
                        { name: 'message'},
                        { name: 'created_at'},
                        { name: 'action' }
                    ],
                    columns: [
                        { data: 'is_read'   , sortable: true , searchable: false},
                        { data: 'subject'   , sortable: false , searchable: true},
                        { data: 'sender'   , sortable: false , searchable: true},
                        { data: 'message'   , sortable: false  , searchable: false},
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
                        url: Path+'contacts/delete/'+co,
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

                $('#exampleModal').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget);
                    var id = button.data('id');
                    var sender = button.data('sender');
                    var subject = button.data('subject');
                    var message = button.data('message');
                    var time = button.data('time');
                    var seen = button.data('seen');
                    if(seen != 'Y'){
                        M6Module.ajaxStatusChange('contacts/AjaxStatusUpdate' , id);
                        button.data('seen' , 'Y')
                    }
                    var modal = $(this);
                    modal.find('.modal-title').text(subject);
                    modal.find('.modal-body input[name=identifier]').val(id);
                    modal.find('.modal-sender').html(
                        '<a href="mailto:'+sender+'">'+sender+'</a>'
                    );
                    modal.find('.modal-sendTime').text(time);
                    modal.find('.messageContainer').text(message);
                });

        }
    };

M6Module.global();

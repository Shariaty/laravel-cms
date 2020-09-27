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
        formValidation: function (form) {
            statusFormValid = true;
            $('.BoxError').html('');
            if(document.getElementById(form))
            {
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
                        },
                        user: {
                            required: true
                        }
                    },
                    errorPlacement: function (error, element) {
                        $(element).parents('.input-group:first').addClass('has-error');
                        $(element).parents('.form-group:first').addClass('has-error');

                        $(element).parents('.form-group:first').find('.validation-message-block:first').html(error[0].innerHTML);

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
                    }
                });
            }
            $('.product_selector').each(function () {
                $(this).rules('add', {
                    required: true
                });
            });

            $('.price_value').each(function () {
                $(this).rules('add', {
                    required: true ,
                    min: 0 ,
                    minlength: 1,
                    maxlength: 10
                });
            });

            $('.qty_value').each(function () {
                $(this).rules('add', {
                    required: true ,
                    min: 1 ,
                    minlength: 1,
                    maxlength: 10
                });
            });

            $('.unit_list').each(function () {
                $(this).rules('add', {
                    required: true
                });
            });

            $('.select2').on('change', function () {
                $('#' + form).valid();
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
        invoiceList: function () {
                var dataTableAjaxURL = Path+'sale/dataTables';
                var dataTable = $('#list-table').DataTable({
                    processing: true,
                    serverSide: true,
                    order: [[5, 'desc']],
                    responsive: true,
                    ajax: {
                        type : 'post',
                        url  : dataTableAjaxURL,
                        data : {
                            _token : _csrf_token
                        }
                    },
                    initComplete : function () {
                        $('#list-table').show();
                    },
                    columnDefs: [
                        { name: 'invoice_number'},
                        { name: 'title'},
                        { name: 'customer'},
                        { name: 'total_price'},
                        { name: 'total_qty'},
                        { name: 'created_at'},
                        { name: 'action' }
                    ],
                    columns: [
                        { data: 'invoice_number' , sortable: false , searchable: true},
                        { data: 'title'      , sortable: false , searchable: true},
                        { data: 'customer'      , sortable: false , searchable: true},
                        { data: 'total_price' , sortable: true , searchable: false},
                        { data: 'total_qty' , sortable: true , searchable: false},
                        { data: 'created_at' , sortable: true , searchable: false},
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
                $('.price').mask("000,000,000,000,000", {reverse: true});

                function AjaxDelete(co) {
                    $.ajax({
                        url: Path+'sale/delete/'+co,
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
        createEditInvoice: function(){
            var formId = 'FormInvoice';
            var form = '#' + formId;
            M6Module.formValidation(formId);

            $(document).on('click', '#btnCreate', function () {
                M6Module.formValidation(formId);
                var formValid = $(form).valid();

                if (formValid){
                    var countOfGoods = $('.item-row').length;
                    if(countOfGoods <= 1) {
                        swal({
                            title: "Invoice Error",
                            text : "You can not save an invoice with no goods in it.",
                            type: "warning",
                            showCancelButton: false,
                            confirmButtonColor: "#31c7b2",
                            cancelButtonColor: "#DD6B55"
                        });

                    } else {

                        var total_qty = parseFloat($('#totalQty').html());
                        var total_price = parseFloat($('#grandTotal').html());

                        $('#total_price').val(total_price);
                        $('#total_qty').val(total_qty);
                        $(form)[0].submit();
                    }
                }
            });
        },
        orderList: function () {
                var dataTableAjaxURL = Path+'orders/dataTables';
                var dataTable = $('#list-table').DataTable({
                    processing: true,
                    serverSide: true,
                    order: [[5, 'desc']],
                    responsive: true,
                    ajax: {
                        type : 'post',
                        url  : dataTableAjaxURL,
                        data : {
                            _token : _csrf_token
                        }
                    },
                    initComplete : function () {
                        $('#list-table').show();
                    },
                    columnDefs: [
                        { name: 'invoice_number'},
                        { name: 'is_paid'},
                        { name: 'status'},
                        { name: 'title'},
                        { name: 'total_price'},
                        { name: 'created_at'},
                        { name: 'action' }
                    ],
                    columns: [
                        { data: 'invoice_number' , sortable: false , searchable: true},
                        { data: 'is_paid'      , sortable: false , searchable: false},
                        { data: 'status'      , sortable: true , searchable: true},
                        { data: 'title'      , sortable: false , searchable: false},
                        { data: 'total_price' , sortable: false , searchable: false},
                        { data: 'created_at' , sortable: true , searchable: false},
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
                $('.price').mask("000,000,000,000,000", {reverse: true});

                function AjaxDelete(co) {
                    $.ajax({
                        url: Path+'sale/delete/'+co,
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
        paymentList: function () {
            var dataTableAjaxURL = Path+'payments/dataTables';
            var dataTable = $('#list-table').DataTable({
                processing: true,
                serverSide: true,
                order: [[5, 'desc']],
                responsive: true,
                ajax: {
                    type : 'post',
                    url  : dataTableAjaxURL,
                    data : {
                        _token : _csrf_token
                    }
                },
                initComplete : function () {
                    $('#list-table').show();
                },
                columnDefs: [
                    { name: 'status'},
                    { name: 'port'},
                    { name: 'user'},
                    { name: 'cell'},
                    { name: 'invoice_number'},
                    { name: 'price'},
                    { name: 'payment_date' }
                ],
                columns: [
                    { data: 'status' , sortable: true , searchable: false},
                    { data: 'port'   , sortable: true , searchable: false},
                    { data: 'user'   , sortable: false , searchable: true},
                    { data: 'cell'  , sortable: false , searchable: true},
                    { data: 'invoice_number' , sortable: true , searchable: true},
                    { data: 'price'  , sortable: true , searchable: false},
                    { data: 'payment_date' , sortable: true , searchable: false}
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
            $('.price').mask("000,000,000,000,000", {reverse: true});
            },
        priceList: function () {
                var dataTableAjaxURL = Path+'priceList/dataTables';
                var dataTable = $('#list-table').DataTable({
                    processing: true,
                    serverSide: true,
                    order: [[1, 'desc']],
                    responsive: true,
                    ajax: {
                        type : 'post',
                        url  : dataTableAjaxURL,
                        data : {
                            _token : _csrf_token
                        }
                    },
                    initComplete : function () {
                        $('#list-table').show();
                    },
                    columnDefs: [
                        { name: 'code'},
                        { name: 'created_at'},
                        { name: 'updated_at'},
                        { name: 'action' }
                    ],
                    columns: [
                        { data: 'code' , sortable: false , searchable: true},
                        { data: 'created_at' , sortable: true , searchable: false},
                        { data: 'updated_at' , sortable: true , searchable: false},
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
                            AjaxDelete(st_number , 'priceList/');
                        }
                    });

                });
                $('.price').mask("000,000,000,000,000", {reverse: true});

                function AjaxDelete(co) {
                    $.ajax({
                        url: Path+'priceList/delete/'+co,
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
        createEditPriceList: function(){
            var formId = 'FormPriceList';
            var form = '#' + formId;
            M6Module.formValidation(formId);

            $(document).on('click', '#btnCreate', function () {
                M6Module.formValidation(formId);
                var formValid = $(form).valid();
                if (formValid){
                    $(form)[0].submit();
                }
            });
        }
        };

M6Module.global();

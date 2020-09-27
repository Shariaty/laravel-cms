/**
 Custom module for you to write your own javascript functions
 **/
var Custom = function () {

    // private functions & variables

    var myFunc = function(text) {
        alert(text);
    };

    // public functions
    return {

        //main function
        init: function () {
            //initialize here something.
        },

        //some helper function
        doSomeStuff: function () {
            myFunc();
        }

    };

}();

jQuery(document).ready(function() {

    Custom.init();

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
        "timeOut": "6000",
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
                    title: "Item contain some other items",
                    text : "This category contains some items, by removing it you will lost categories that set into items, are you sure you want to remove it?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#31c7b2",
                    cancelButtonColor: "#DD6B55",
                    // confirmButtonText: "Remove",
                    cancelButtonText: "Candsadasdcel",
                    // closeOnConfirm: true,
                    closeOnCancel: true
                });
                // .then(function(result) {
                //     if (result.value) {
                //         window.location.href = href;
                //     }
                // });
        } else {
            swal({
                    title: "Remove this item !",
                    text : "You are about to remove an item from server , this action can not be undone ! Do you want to proceed ?",
                    type: "error",
                    showCancelButton: true,
                    confirmButtonColor: "#31c7b2",
                    cancelButtonColor: "#DD6B55",
                    confirmButtonText: "Remove",
                    cancelButtonText: "Cancel",
                    closeOnConfirm: true,
                    closeOnCancel: true
                }).then(function(result) {
                if (result.value) {
                    window.location.href = href;
                }
            });
        }
        return false;
    });
    //Remove Sweet Alert

    //Logout Sweet Alert
    jQuery('#confirmation-logout').click(function (e) {
        var href = jQuery(this).attr('href');

        swal({
                title: "You are about to log out of you account!",
                showCancelButton: true,
                confirmButtonColor: "#31c7b2",
                cancelButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel",
                closeOnConfirm: true,
                closeOnCancel: true
            }).then(function(result) {
            if (result.value) {
                window.location.href = href;
                }
            });

        return false;
    });
    //Logout Sweet Alert

    //Lock Sweet Alert
    jQuery('#confirmation-lock').click(function (e) {
        var href = jQuery(this).attr('href');
        swal({
                title: "Lock your session ?",
                text : "You are about to lock your session , you can unlock it through unlock screen later , Do you want to proceed ?",
                showCancelButton: true,
                confirmButtonColor: "#31c7b2",
                cancelButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel",
                closeOnConfirm: true,
                closeOnCancel: true
            }).then(function(result) {
                if (result.value) {
                    window.location.href = href;
                }
            });
        return false;
    });
    //Lock Sweet Alert

    //ClearAll Message Sweet Alert
    jQuery('#btnDeleteMessage').click(function (e) {
        var href = jQuery(this).attr('href');
        swal({
                title: "Delete All",
                text: "Are you sure you want to delete all these items?",
                showCancelButton: true,
                confirmButtonColor: "#31c7b2",
                cancelButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel",
                closeOnConfirm: true,
                closeOnCancel: true
            }).then(function(result) {
                if (result.value) {
                    window.location.href = href;
                }
            });
        return false;
    });
    //ClearAll Message Sweet Alert
});

/***
 Usage
 ***/
//Custom.doSomeStuff();

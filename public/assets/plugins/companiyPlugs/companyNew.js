$(document).ready(function() {

    var text_max = 500;
    var url = Path+'companies/ajaxImageUpload';
    var path = Path+'uploads/company-logos-temp/' ;
    var FormCompanyEmployer_orginal = $('#FormCompanyEmployer').serialize();

    $('#logoUpload').fileupload({
        url: url,
        dataType: 'json',
        headers: {'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')},
        maxNumberOfFiles: 1 ,
        done: function (e, data) {
            $.each(data.result.files , function (index, file) {
                $('<p/>').text(file.name).appendTo('#files');
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        },
        success: function (data) {
            if(data.success === true){
                $("#progressBar").removeClass("progress-bar-danger");
                $("#progressBar").addClass( "progress-bar-success");
                $("#logo-thumb-image").attr("src", path+data.message);
                $(".image-company-profile").attr("src", path+data.message);
                $("#upload-box").pulsate("destroy");
                $("#status_First_logo_company").attr("value", "1");
                // $("#logo-error-reporting").html('');

            } else {
                toastr.error(data.message, 'Upload error', {timeOut: 5000});
                // $("#logo-error-reporting").html(data.message[0]);
                $("#progressBar").removeClass( "progress-bar-success");
                $("#progressBar").addClass( "progress-bar-danger");
            }
            console.log(data.message);
        }
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');

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

    $(document).ajaxStart(function() {
        $( "#progress .progress-bar" ).css('width', 1 + '%');
    });

    $('#textarea_feedback').html(text_max + ' characters remaining');

    $('#tm_textArea').keyup(function() {
        var text_length = $('#tm_textArea').val().length;
        var text_remaining = text_max - text_length;

        $('#textarea_feedback').html(text_remaining + ' characters remaining');
    });

    $('.pulsate-regular').pulsate({
        color:"#ff0000",                         // set the color of the pulse
        reach: 5,                               // how far the pulse goes in px
        speed: 500,                            // how long one pulse takes in ms
        pause: 0,                               // how long the pause between pulses is in ms
        glow: true,                             // if the glow should be shown too
        repeat: true,                           // will repeat forever if true, if given a number will repeat for that many times
        onHover: false                          // if true only pulsate if user hovers over the element
    });

    // $(document).on('click', 'a:not(.dropdown-toggle)', function (e) {
    //     var FormCompanyEmployer_new = $('#FormCompanyEmployer').serialize();
    //     var href = $(this).attr('href');
    //     if(FormCompanyEmployer_orginal != FormCompanyEmployer_new)
    //     {
    //         e.preventDefault();
    //         swal({
    //             title: "Do you want to save Company Profile?",
    //             width: 600,
    //             padding: 35,
    //             html: "<button class='btn btn-info build-cv-swal swal_continue'>Yes</button>&nbsp;"+
    //             "<button class='btn btn-danger swal_close' data-href='"+href+"'>No</button>" ,
    //             showCloseButton: true,
    //             showConfirmButton: false
    //         }).catch(swal.noop);
    //         return true;
    //     }
    //
    // });

    $(document).on('click', '.swal_close', function () {
        var href = $(this).data('href');
        swal.close();
        window.location.href = href;
    });

    $(document).on('click', '.swal_continue', function () {
        swal.close();
        $('#FormCompanyEmployer').submit();
    });

    $(document).on(function () {
        $('form').valid();
    });

    var defaultcolor = $('#colorPicker').val();

    $('#colorSelector div').css('backgroundColor', defaultcolor);

    $('#colorSelector').ColorPicker({

        color: defaultcolor,
        onShow: function (colpkr) {
            $(colpkr).fadeIn(500);
            return false;
        },
        onHide: function (colpkr) {
            $(colpkr).fadeOut(500);
            return false;
        },
        onChange: function (hsb, hex, rgb) {
            $('#colorSelector div').css('backgroundColor', '#' + hex);
            $('#colorPicker').val('#'+hex);
        }
    });

});



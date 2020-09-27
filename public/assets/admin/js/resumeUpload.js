'use strict';
var url = Path+'profile/ajaxImageUpload';
var imagePath = PublicPath+'uploads/admins/profile-pictures/';
$('#ResumeUpload').fileupload({
    url: url,
    dataType: 'json',
    headers: {'X-CSRF-TOKEN': _csrf_token },
    maxNumberOfFiles: 1 ,
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
            $("#logo-thumb-container").html('').append('<i class="fa fa-check green" aria-hidden="true"></i>');
            $("#profile-image , #header-company-logo").attr('src', imagePath+data.message);
            // $("").attr('src', imagePath+data.message);
            $("#upload-title").html('change file');
            $("#upload-box").pulsate("destroy");
            $("#logo-error-reporting").html('');
            $('#logo-thumb-container').html('<a href="'+Path+'profile/ImageRemove?redirect='+window.location.href+'" style="margin-left: 5px" class="resume-upload-link confirmation-remove"><i class="fa fa-times text-muted"></i></a>');
        } else {
            $("#logo-error-reporting").html(data.message[0]);
            $("#progressBar").removeClass( "progress-bar-success");
            $("#progressBar").addClass( "progress-bar-danger");
            $("#logo-thumb-container").html('').append('<i class="fa fa-exclamation red" aria-hidden="true"></i>');
        }
        console.log(data);
    }
}).prop('disabled', !$.support.fileInput)
    .parent().addClass($.support.fileInput ? undefined : 'disabled');

$(function(jQuery){
    $(document).ajaxStart(function() {
        $( "#progress .progress-bar" ).css('width', 0 + '%');
        $('<div class="overlay"><div/>').appendTo('body');
        $("body").css("cursor","wait");
        $('#ajaxLoader').show();
    });
    $(document).ajaxStop(function() {
        $(".overlay").hide().fadeOut(500);
        $("body").css("cursor","default");
        $('#ajaxLoader').hide();
    });
});

$('.pulsate-regular').pulsate({
    color:"#ff0000",
    reach: 5,
    speed: 500,
    pause: 0,
    glow: true,
    repeat: true,
    onHover: false
});

// //Ajax Status change
// $("#cv_permission").change(function() {
//     var selector = $('input[name=cv_permission]:checked').val();
//     var dataSending = {cv_permission: selector};
//     $.ajax({
//         type: 'patch',
//         headers: {'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')},
//         url: Path+'candidate/cv/permissionStatusUpdate',
//         data: JSON.stringify(dataSending),
//         contentType: "application/json; charset=utf-8",
//         traditional: true,
//         success: function (data) {
//             console.log(data);
//         }
//     });
// });
// //Ajax Status change
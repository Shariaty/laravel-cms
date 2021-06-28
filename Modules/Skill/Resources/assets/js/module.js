var M6Module =
    {
        global: function () {
        },
        ajaxFileUpload: function () {
            var url = Path+'skills/ajaxFileUpload';
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
                        $('#catalog #logo-thumb-container').html('<a href="'+Path+'skills/fileView/'+data.slug+'/catalog" target="_blank" class="resume-upload-link btn btn-xs btn-info" style="margin-left: 3px;"><i class="fa fa-eye fa-1x"></i>&nbsp;View</a>' +
                            '<a href="'+Path+'skills/fileRemove/'+id+'/catalog" style="margin-left:5px" class="resume-upload-link confirmation-remove btn btn-xs btn-danger"><i class="fa fa-times"></i></a>');
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

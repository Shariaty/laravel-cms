//Ajax Status change


$j(document).on('click' , '.finish' , function (e) {
    $j.ajax({
        type: 'patch',
        headers: {'X-CSRF-TOKEN': $j('input[name="_token"]').attr('value')},
        url: AppPath+'candidate/cv/wizardFinish',
        data: JSON.stringify([]),
        contentType: "application/json; charset=utf-8",
        traditional: true,
        success: function (data) {

            // window.location.href = AppPath+'candidate/cv/cvSuccessPage';
            console.log(data);
        }
    });
});

//Ajax Status change
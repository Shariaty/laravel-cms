$( document ).ready(function() {


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

    if(typeof tinymce !== "undefined")
    {
        tinymce.init({
            selector: '.tmc-textarea',
            menubar:false,
            statusbar: false,
            height : 200 ,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table contextmenu paste code'
            ],
            toolbar: 'styleselect | bold | alignleft aligncenter alignright alignjustify | bullist numlist | link | removeformat',
        });
    }

    // range slide -----------------------------------------------------------------------------------------------------
    var slider = document.getElementById('age_range');

    var inputNumberLower = document.getElementById('input-number-lower');
    var inputNumberUpper = document.getElementById('input-number-upper');

    if(inputNumberLower && inputNumberUpper)
    {
        noUiSlider.create(slider, {
            start: [ document.getElementById('input-number-lower').value , document.getElementById('input-number-upper').value ] ,
            snap: true,
            connect: true,
            range: {
                'min': 18,
                '10%': 25,
                '20%': 30,
                '30%': 35,
                '40%': 40,
                '50%': 50,
                '60%': 60,
                '70%': 70,
                'max': 100
            }
            ,
            pips: {
                mode: 'positions',
                values: [0,10 ,20 , 30 , 40 , 50 , 60 , 70 , 100],
                density: 5
            }
        });

        inputNumberLower.addEventListener('change', function(){
            slider.noUiSlider.set([ inputNumberLower.value , this.secondValue]);
        });

        inputNumberUpper.addEventListener('change', function(){
            slider.noUiSlider.set([this.firstValue , inputNumberUpper.value]);
        });

        slider.noUiSlider.on('update', function( values, handle ) {

            var firstValue = values[0];
            var secondValue = values[1];

            if ( handle ) {
                inputNumberLower.value = firstValue;
                inputNumberUpper.value = secondValue;
            } else {
                inputNumberLower.value = Math.round(firstValue);
                inputNumberUpper.value = Math.round(secondValue);
            }
        });
    }

    // range slide end--------------------------------------------------------------------------------------------------
});

@if(!empty($success)  && $success != Null || Session::has('success'))

    <input type="hidden" value="{{Session::get('success') ? Session::get('success') : $success}}" id='S_U'>
    <script>
//          var $j = jQuery.noConflict();
          var text = $('#S_U').val();
          $(document).ready(function() {
              toastr["success"](text);
          });
    </script>

@elseif(!empty($error)  && $error != Null || Session::has('error') )

    <input type="hidden" value="{{Session::get('error') ? Session::get('error') : $error}}" id='E_R'>
    <script>
//        var $j = jQuery.noConflict();
        var text = $('#E_R').val();
        $(document).ready(function() {
            toastr["error"](text);
        });
    </script>

@elseif(!empty($warning)  && $warning != Null || Session::has('warning'))

    <input type="hidden" value="{{Session::get('warning') ? Session::get('warning') : $warning}}" id='W_R'>
    <script>
//        var $j = jQuery.noConflict();
        var text = $('#W_R').val();
        $(document).ready(function() {
            toastr["warning"](text);
        });
    </script>

@elseif(!empty($info)  && $info != Null || Session::has('info'))

    <input type="hidden" value="{{Session::get('info') ? Session::get('info') : $info}}" id='I_N'>
    <script>
//        var $j = jQuery.noConflict();
        var text = $('#I_N').val();
        $(document).ready(function() {
            toastr["info"](text);
        });
    </script>

@endif



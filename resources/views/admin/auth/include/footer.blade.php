
<!--[if lt IE 9]>
<!--<script src="{{asset('admin/auth/plugins/respond.min.js')}}"></script>-->
<!--<script src="{{asset('admin/auth/plugins/excanvas.min.js')}}"></script>-->
<!--<script src="{{asset('admin/auth/plugins/ie8.fix.min.js')}}"></script>-->
<![endif]-->
<!-- BEGIN CORE PLUGINS -->
<script src="{{asset('assets/plugins/jquery/jquery.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/js.cookie.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/jquery.blockui.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="{{asset('assets/plugins/jquery-validation/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/jquery-validation/js/additional-methods.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/select2/js/select2.full.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/backstretch/jquery.backstretch.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/bootstrap-toastr/toastr.min.js')}}" type="text/javascript"></script>
<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="{{asset('assets/admin/js/app.min.js')}}" type="text/javascript"></script>
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{asset('assets/admin/auth/js/login-4.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/admin/js/captcha.js')}}" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->

@include('admin.forms._errorreporting')
</body>

</html>
</div>
</div>
<!-- END CONTENT -->
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
{{--<div class="page-footer">--}}
    {{--<div class="page-footer-inner"> 2016 - {{\Carbon\Carbon::now()->format("Y")}} &copy; {{trans('admin.WEB_SITE_NAME')}} &nbsp;|&nbsp; All rights reserved</div>--}}
    {{--<div class="scroll-to-top">--}}
        {{--<i class="icon-arrow-up"></i>--}}
    {{--</div>--}}
{{--</div>--}}
<!-- END FOOTER -->
</div>
</div>

<!-- BEGIN CORE PLUGINS -->
<script src="{{asset('assets/plugins/jquery/jquery.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/js.cookie.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/jquery.blockui.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/bootstrap-select.min.js')}}" type="text/javascript"></script>
<!-- END CORE PLUGINS -->

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="{{asset('assets/plugins/morris/morris.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/counterup/jquery.waypoints.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/counterup/jquery.counterup.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/sweetalert2/dist/sweetalert2.all.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/bootstrap-toastr/toastr.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/jquery-pulsate/jquery.pulsate.min.js')}}" type="text/javascript"></script>
<!-- jQuery form validation -->
<script src="{{asset('assets/plugins/form/jquery.form.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/form/jquery.validate.min.js')}}" type="text/javascript"></script>

<script src="{{asset('assets/admin/js/global.js')}}?v={{rand(10000000, 99999999)}}"></script>

@yield('footer')
@yield('moduleFooter')

<!-- END PAGE LEVEL PLUGINS -->

<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="{{asset('assets/admin/js/app.min.js')}}" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->

<!-- BEGIN THEME LAYOUT SCRIPTS -->
<script src="{{asset('assets/admin/js/layout.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/admin/js/demo.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/admin/js/quick-sidebar.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/admin/js/quick-nav.min.js')}}" type="text/javascript"></script>
<!-- END THEME LAYOUT SCRIPTS -->
@include('admin.forms._errorreporting')
</body>


</html>
<!-- Resume Upload -->
<div id="upload-box" class="custom-file-input ot-center-text {{ Session::has('ResumeError') ? 'pulsate-regular' : '' }}">
<span class="fileinput-button">
    <i class="fa fa-cloud-upload icon-upload-custom green"></i>
    {{ csrf_field() }}
    <span id="upload-title">
        @if($adminUser->img)
            change file
        @else
            upload file
        @endif
    </span>
    <!-- The file input field used as target for the file upload widget -->
    {!! Form::file( 'file' , ['id' => 'ResumeUpload' , 'class' => 'disabled' , 'accept' => '.jpg,.jpeg,.png' ]) !!}
</span>
</div>
<!-- The global progress bar -->
<div  id="progress" class="progress progress-striped progress-custom active">
    <div id="progressBar" class="progress-bar progress-bar-success" role="progressbar">
    </div>
</div>
<!-- The container for the uploaded files -->
<div id="logo-thumb-container" style="margin-left: 10px; padding-top: 2px;">
    @if($adminUser->img)
        <a href="{{route('admin.profile.image.remove' ,  ['redirect' => url()->current()])}}" class="resume-upload-link confirmation-remove">
            <i class="fa fa-times text-muted" aria-hidden="true"></i>
        </a>
    @endif
</div>
<div id="logo-error-reporting"></div>
<!-- Resume Upload -->
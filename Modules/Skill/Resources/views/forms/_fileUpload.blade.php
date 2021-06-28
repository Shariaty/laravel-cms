<!-- File Upload -->
<span style="font-size: 12px;  color: #a1a1a1;  margin-left: 10px;"><i class="icon-paper-clip icons"></i>&nbsp;
    {{ $type === "sheet" ? 'Technical Sheet' : 'Skill Media' }}
</span>
<div id="upload-box" class="custom-file-input ot-center-text {{ Session::has('ResumeError') ? 'pulsate-regular' : '' }}">
<span class="fileinput-button btn btn-xs btn-success pull-left" style="margin-left: 10px; margin-top: 8px; color: white;">
        <i class="fa fa-cloud-upload icon-upload-custom"></i>
        {{ csrf_field() }}
        <span id="upload-title">
            @if($type === "sheet" ? $magazine->sheet : $magazine->file)
                Change file
            @else
                Upload file
            @endif
        </span>
        <!-- The file input field used as target for the file upload widget -->
        {!! Form::file( 'file' , ['id' => 'FileUpload' , 'class' => 'disabled' , 'accept' => '.jpeg,.jpg,.png' ]) !!}
</span>
</div>
<!-- The global progress bar -->
<div  id="progress" class="progress progress-striped progress-custom active">
    <div id="progressBar" class="progress-bar progress-bar-success" role="progressbar">
    </div>
</div>
<!-- The container for the uploaded files -->
<div id="logo-thumb-container">
    @if($type === "sheet")
        @if($magazine->sheet)
            <a href="{{route('admin.skills.fileView' , [$magazine , 'type' => "sheet"] )}}" class="resume-upload-link btn btn-xs btn-info" style="margin-left: 3px;" target="_blank">
                <i class="fa fa-eye fa-1x"></i>&nbsp;View
            </a>
            <a href="{{route('admin.skills.removeFile' , ['id' =>  $magazine->id , 'type' => "sheet"])}}" class="resume-upload-link confirmation-remove btn btn-xs btn-danger">
                <i class="fa fa-times"></i>
            </a>
        @endif
    @else
        @if($magazine->file)
            <a href="{{route('admin.skills.fileView' , [$magazine , 'type' => "catalog"])}}" class="resume-upload-link btn btn-xs btn-info" style="margin-left: 3px;" target="_blank">
                <i class="fa fa-eye fa-1x"></i>&nbsp;View
            </a>
            <a href="{{route('admin.skills.removeFile' ,  [$magazine->id ,"catalog"])}}" class="resume-upload-link confirmation-remove btn btn-xs btn-danger">
                <i class="fa fa-times"></i>
            </a>
        @endif
    @endif


</div>
<div id="logo-error-reporting"></div>
<!-- File Upload -->

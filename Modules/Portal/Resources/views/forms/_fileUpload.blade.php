
<!-- File Upload -->
<span style="font-size: 12px;  color: #a1a1a1;  margin-left: 10px;"><i class="fa fa-file-excel-o"></i>&nbsp;
    Excel file to be imported
</span>
<div id="upload-box" class="custom-file-input ot-center-text {{ Session::has('ResumeError') ? 'pulsate-regular' : '' }}">
<span class="fileinput-button btn btn-xs btn-success pull-left" style="margin-left: 10px; margin-top: 8px; color: white;">
        <i class="fa fa-cloud-upload icon-upload-custom"></i>
        {{ csrf_field() }}
        <span id="upload-title">
               Upload Excel file
        </span>
        <!-- The file input field used as target for the file upload widget -->
        {!! Form::file( 'file' , ['id' => 'FileUpload' , 'class' => 'disabled' , 'accept' => '.xlsx' ]) !!}
</span>
</div>
<!-- The global progress bar -->
<div  id="progress" class="progress progress-striped progress-custom active">
    <div id="progressBar" class="progress-bar progress-bar-success" role="progressbar">
    </div>
</div>
<!-- The container for the uploaded files -->
<div id="logo-thumb-container"></div>
<div id="logo-error-reporting"></div>
<!-- File Upload -->

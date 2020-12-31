@extends('dashboard.master.base')
@section('title',$title)
@section('style')
    <link rel="stylesheet" href="{{asset('panel/dropify/dist/css/dropify.min.css')}}">
@endsection
@section('content')
    <div class="content-i">
        <div class="content-box">
            <div class="row">
                <div class="col-sm-12">
                    <div class="element-wrapper">
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade in" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </div>
                        @endif
                        <div class="element-box">
                            <form class="formValidate" method="POST" action="{{ route($action) }}"
                                  enctype="multipart/form-data">
                                @csrf
                            <div class="element-info">
                                <div class="element-info-with-icon">
                                    <div class="element-info-icon">
                                        <div class="os-icon os-icon-wallet-loaded"></div>
                                    </div>
                                    <div class="element-info-text">
                                        <h5 class="element-inner-header">
                                            إضافة
                                        </h5>
                                        @if(isset($create_alert))
                                            <div class="element-inner-desc">
                                                {{$create_alert}}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if(isset($create_fields))
                                <fieldset class="form-group">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group" id="name_ar">
                                                <label> الإسم باللغة العربية </label>
                                                <span style="color: red">*</span>
                                                <input type="text" name="name_ar" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group" id="name_en">
                                                <label> الإسم باللغة الانجليزية </label>
                                                <span style="color: red">*</span>
                                                <input type="text" name="name_en" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group" id="note_ar">
                                                <label> الوصف باللغة العربية </label>
                                                <span style="color: red">*</span>
                                                <textarea name="note_ar" class="form-control" cols="80" rows="10"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group" id="note_en">
                                                <label> الوصف باللغة الانجليزية </label>
                                                <span style="color: red">*</span>
                                                <textarea name="note_en" class="form-control" cols="80" rows="10" ></textarea>
                                            </div>
                                        </div>
                                        @foreach($create_fields as $key=>$value)
                                            <div class="col-sm-12">
                                                <div class="form-group" id="{{$value}}">
                                                    <label for=""> {{$key}}</label>
                                                    <input name="{{$value}}" class="form-control" type="number" value="0" min="0">
                                                    <div class="help-block form-text with-errors form-control-feedback"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="col-sm-12">
                                            <div class="white-box">
                                                <label for="input-file-now-custom-1">الصورة</label>
                                                <input name="image" type="file" id="input-file-now-custom-1 image" class="dropify" data-default-file="{{asset('media/images/logo.jpeg')}}"/>
                                            </div>
                                        </div>
                                        <br/>
                                        <div class="form-group" id="image_preview"></div>
                                    </div>
                                </fieldset>
                            @endif
                            <div class="form-buttons-w">
                                <button class="btn btn-primary create-submit" type="submit"> إرسال</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{asset('panel/dropify/dist/js/dropify.min.js')}}"></script>
    <script>
        $(document).ready(function() {
            // Basic
            $('.dropify').dropify();
            // Translated
            $('.dropify-fr').dropify({
                messages: {
                    default: 'Glissez-déposez un fichier ici ou cliquez',
                    replace: 'Glissez-déposez un fichier ou cliquez pour remplacer',
                    remove: 'Supprimer',
                    error: 'Désolé, le fichier trop volumineux'
                }
            });
            // Used events
            var drEvent = $('#input-file-events').dropify();
            drEvent.on('dropify.beforeClear', function(event, element) {
                return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
            });
            drEvent.on('dropify.afterClear', function(event, element) {
                alert('File deleted');
            });
            drEvent.on('dropify.errors', function(event, element) {
                console.log('Has Errors');
            });
            var drDestroy = $('#input-file-to-destroy').dropify();
            drDestroy = drDestroy.data('dropify')
            $('#toggleDropify').on('click', function(e) {
                e.preventDefault();
                if (drDestroy.isDropified()) {
                    drDestroy.destroy();
                } else {
                    drDestroy.init();
                }
            })
        });
    </script>

    @if($errors->any())
        <div style="visibility: hidden" id="errors" data-content="{{$errors}}"></div>
        <script type="text/javascript">
            $(document).ready(function () {
                var errors=$('#errors').attr('data-content');
                $.each(JSON.parse(errors), function( index, value ) {
                    // $('input[name="note"]').notify(
                    $('#'+index).notify(
                        value,
                        'error',
                        { position:"top" }
                    );
                });
            })
        </script>
    @endif
@stop

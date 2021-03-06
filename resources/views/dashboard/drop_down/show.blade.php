@extends('dashboard.master.base')
@section('title',$title)
@section('style')
    <link rel="stylesheet" href="{{asset('panel/dropify/dist/css/dropify.min.css')}}">
@endsection
@section('content')
    <div class="content-i">
        <div class="content-box">
            <div class="row">
{{--                first box--}}
                <div class="col-sm-5">
                    <div class="user-profile compact">
                        @if($image==true)
                            <div class="up-head-w" style="background-image:url({{$row->image}})">
                        @else
                            <div class="up-head-w" style="background-image:url({{asset('media/images/logo.png')}})">
                        @endif
                            <div class="up-main-info">
                                <h2 class="up-header">
                                    {{$row->name['ar']}}
                                </h2>
                            </div>
                            <svg class="decor" width="842px" height="219px" viewBox="0 0 842 219" preserveAspectRatio="xMaxYMax meet" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <g transform="translate(-381.000000, -362.000000)" fill="#FFFFFF">
                                    <path class="decor-path" d="M1223,362 L1223,581 L381,581 C868.912802,575.666667 1149.57947,502.666667 1223,362 Z">
                                    </path>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
{{--                end first box--}}
{{--                show fields--}}
        <div class="col-sm-7">
                        <div class="element-wrapper">
                            <div class="element-box">
                                @if($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </div>
                                @endif
                                @if(isset($edit_fields))
                                    {!! Form::model($row, ['method'=>'POST','name'=>'update', 'files'=>true, 'route'=>[$action, $row->id], 'id' => 'formValidate']) !!}
                                    {!! Form::hidden('id', $row->id) !!}
                                    {!! Form::hidden('edit_by', \Illuminate\Support\Facades\Auth::user()->id) !!}
                                    <div class="element-info">
                                        <div class="element-info-with-icon">
                                            <div class="element-info-icon">
                                                <div class="os-icon os-icon-wallet-loaded"></div>
                                            </div>
                                            <div class="element-info-text">
                                                <h5 class="element-inner-header">
                                                    تعديل البيانات
                                                </h5>
                                                @if(isset($edit_alert))
                                                    <div class="element-inner-desc">
                                                        {{$edit_alert}}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <fieldset class="form-group">
                                        <div class="row">
                                            @foreach($edit_fields as $key=>$value)
                                                @if($value=='parent_id')
                                                    <div class="col-sm-12" id="roles">
                                                        <div class="form-group">
                                                            <label for=""> الماركة </label>
                                                            <select id="parent_id" name="parent_id" class="form-control">
                                                                @foreach(\App\DropDown::whereClass('Mark')->get() as $mark)
                                                                    <option @if($row->parent_id==$mark->id) selected @endif value="{{$mark->id}}">
                                                                        {{$mark->name['ar']}}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="col-sm-12">
                                                        <div class="form-group" id="name_ar">
                                                            <label for=""> الاسم باللغة العربية</label><input name="name_ar" class="form-control" value="{{$row->name['ar']}}" type="text">
                                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="form-group" id="name_en">
                                                            <label for=""> الاسم باللغة الانجليزية</label><input name="name_en" class="form-control" value="{{$row->name['en']}}" type="text">
                                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                            @if($image==true)
                                                <div class="col-sm-6">
                                                    <div class="white-box">
                                                        <label for="input-file-now-custom-1">الصورة</label>
                                                        <input name="image" type="file" id="input-file-now-custom-1" class="dropify" data-default-file="{{$row->image}}"/>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </fieldset>
                                    {{--                @can('edit-'.$type.'s')--}}
                                    <div class="form-buttons-w">
                                        <button class="btn btn-primary edit-submit" type="submit"> تعديل</button>
                                    </div>
                                    {{--                @endcan--}}
                                    {!! Form::close() !!}
                                @endif
                            </div>
                        </div>
            </div>
{{--                end show fields--}}
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
@endsection

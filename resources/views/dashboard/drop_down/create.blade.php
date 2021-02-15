@extends('dashboard.master.base')
@section('title',$title)
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
                            {!! Form::open(['method'=>'post', 'files'=>true, 'enctype' => 'multipart/form-data', 'route'=>[$action], 'class' => 'formValidate']) !!}
                            {!! Form::hidden('add_by', \Illuminate\Support\Facades\Auth::user()->id) !!}
                            {!! Form::hidden('admin_notify_type', $admin_notify_type) !!}
                            {!! Form::hidden('type', $type) !!}
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
                                        @foreach($create_fields as $key=>$value)
                                            <div class="col-sm-12">
                                                <div class="form-group" id="name_ar">
                                                    <label for=""> الاسم باللغة العربية</label>
                                                    <input name="name_ar" class="form-control" type="text">
                                                    <div class="help-block form-text with-errors form-control-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group" id="name_en">
                                                    <label for=""> الاسم باللغة الانجليزية</label>
                                                    <input name="name_en" class="form-control" type="text">
                                                    <div class="help-block form-text with-errors form-control-feedback"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                            @if(isset($image))
                                                <div class="col-sm-12">
                                                    <div class="white-box">
                                                        <label for="input-file-now-custom-1">الصورة</label>
                                                        <input name="image" type="file" id="input-file-now-custom-1 image" class="dropify" data-default-file="{{asset('media/images/logo.png')}}"/>
                                                    </div>
                                                </div>
                                            @endif
                                            @if(isset($selects))
                                                @foreach($selects as $select)
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label for=""> {{$select['title']}} </label>
                                                            <select id="{{$select['input_name']}}" name="{{$select['input_name']}}" class="form-control">
                                                                @foreach($select['rows'] as $row)
                                                                    <option value="{{$row->id}}">
                                                                        {{$row->nameForSelect()}}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                    </div>
                                </fieldset>
                            @endif
                            <div class="form-buttons-w">
                                <button class="btn btn-primary create-submit" type="submit"> إرسال</button>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
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

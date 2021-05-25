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
                                    </div>
                                </div>
                            </div>
                                <fieldset class="form-group">
                                    <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group" id="ask_ar">
                                                    <label for=""> السؤال باللغة العربية</label>
                                                    <input name="ask_ar" class="form-control" type="text">
                                                    <div class="help-block form-text with-errors form-control-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group" id="ask_en">
                                                    <label for=""> السؤال باللغة الانجليزية</label>
                                                    <input name="ask_en" class="form-control" type="text">
                                                    <div class="help-block form-text with-errors form-control-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-sm-12">
                                                <div class="form-group" id="answer_ar">
                                                    <label for=""> الإجابة باللغة العربية</label>
                                                    <input name="answer_ar" class="form-control" type="text">
                                                    <div class="help-block form-text with-errors form-control-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group" id="answer_en">
                                                    <label for=""> الإجابة باللغة الانجليزية</label>
                                                    <input name="answer_en" class="form-control" type="text">
                                                    <div class="help-block form-text with-errors form-control-feedback"></div>
                                                </div>
                                            </div>

                                    </div>
                                </fieldset>
                            <div class="form-buttons-w">
                                <button class="btn btn-primary create-submit" type="submit"> إضافة</button>
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

@extends('dashboard.master.base')
@section('title',$title)
@section('style')
    <style>
        #sortable1, #sortable2 {
            border: 1px solid #eee;
            min-height: 100px;
            margin: 0;
            padding: 5px 0 0 0;
            /*float: left;*/
            /*margin-right: 10px;*/
        }
        #sortable1 li, #sortable2 li {
            margin: 0 5px 5px 5px;
            padding: 5px;
            font-size: 1.2em;
            /*width: 120px;*/
        }
    </style>
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
                            <form name="create-form" class="formValidate" method="POST" action="{{ route($action) }}"
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
                                    </div>
                                </div>
                                </div>
                                @if(isset($create_fields))
                                    <fieldset class="form-group">
                                        <div class="row">
                                            @foreach($create_fields as $key=>$value)
                                                @if($value=='duration')
                                                    <div class="col-sm-12">
                                                        <div class="form-group" id="{{$value}}">
                                                            <label for=""> {{$key}}</label>
                                                            <input name="{{$value}}" class="form-control" type="number" min="1">
                                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                                        </div>
                                                    </div>
                                                @elseif($value=='start_date' || $value=='end_date')
                                                    <div class="col-sm-12" id="{{$value}}">
                                                        <div class="form-group row">
                                                            <label for="{{$value}}" class="col-2 col-form-label">{{$key}}</label>
                                                            <input name="{{$value}}" class="form-control" type="datetime-local" id="{{$value}}">
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="col-sm-12">
                                                        <div class="form-group" id="{{$value}}">
                                                            <label for=""> {{$key}}</label>
                                                            <input  id="{{$value}}" name="{{$value}}" class="form-control" type="text">
                                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                            @if(isset($multi_select))
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="sortable1"> {{$multi_select['title']}}</label>
                                                        <ul id="sortable1" class="connectedSortable col-sm-6">
                                                            @foreach($multi_select['rows'] as $multi_select_row)
                                                                <li data-id="{{$multi_select_row->id}}" class="ui-state-default">{{$multi_select_row->nameForSelect()}}</li>
                                                            @endforeach
                                                        </ul>
                                                        <label for="sortable2"> السلع المضمنة بالمزاد</label>
                                                        <ul id="sortable2" class="connectedSortable col-sm-6">
                                                        </ul>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </fieldset>
                                @endif
                                <div class="form-buttons-w">
                                    <button id="submit" class="btn btn-primary create-submit" type="submit"> إنشاء</button>
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
    <script>
        $('#submit').click(function (){
            let sortable_li=$('#sortable2 li');
            if (sortable_li.length<1){
                alert('تأكد من اضافة سلعة واحدة على الأقل للمزاد');
                window.reload();
            }
            sortable_li.each(function() {
                $("form[name='create-form']").append('<input name="items[]" type="hidden" value="'+parseInt($(this).data('id'))+'" />');
            });
            $("form[name='create-form']").submit();
        });
    </script>
{{--    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>--}}
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $( "#sortable1, #sortable2" ).sortable({
            connectWith: ".connectedSortable",
        }).disableSelection();
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

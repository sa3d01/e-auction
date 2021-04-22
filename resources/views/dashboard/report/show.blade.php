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
                            <form class="formValidate" method="POST" action="{{ route($action) }}"
                                  enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" value="{{$item_id}}" name="item_id">
                                <fieldset class="form-group">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group" id="title_ar">
                                                <label> العنوان باللغة العربية </label>
                                                <span style="color: red">*</span>
                                                <input type="text" value="{{$row->title['ar']}}" name="title_ar" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group" id="title_en">
                                                <label> العنوان باللغة الانجليزية </label>
                                                <span style="color: red">*</span>
                                                <input type="text" value="{{$row->title['en']}}" name="title_en" class="form-control" >
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
                                        {{--                                        <div class="col-sm-12">--}}
                                        {{--                                            <div class="form-group" id="price">--}}
                                        {{--                                                <label for=""> السعر </label>--}}
                                        {{--                                                <input name="price" class="form-control" value="0" type="number" min="0">--}}
                                        {{--                                                <div class="help-block form-text with-errors form-control-feedback"></div>--}}
                                        {{--                                            </div>--}}
                                        {{--                                        </div>--}}
                                        <div class="col-sm-12" id="images">
                                            <div class="form-group row">
                                                <label for="images" class="col-form-label">الصور</label>
                                                <span style="color: red">*</span>
                                                <input required class="upload form-control" id="uploadFile" type="file" accept="image/*" name="images[]" multiple />
                                            </div>
                                        </div>
                                        <br/>
                                        <div class="form-group" id="image_preview"></div>
                                    </div>
                                </fieldset>
                                <div class="form-buttons-w">
                                    <button class="btn btn-primary create-submit" type="submit"> إضافة</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
{{--                end show fields--}}
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $("#uploadFile").change(function(){
            $('#image_preview').html("");
            var total_file=document.getElementById("uploadFile").files.length;
            for(var i=0;i<total_file;i++)
            {
                $('#image_preview').append("<img style='pointer-events: none;max-height: 100px;max-width: 100px;height: 100px;border-radius: 10px;margin: 5px;' src='"+URL.createObjectURL(event.target.files[i])+"'>");
            }
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
@endsection

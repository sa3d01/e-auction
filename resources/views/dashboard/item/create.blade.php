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
                            {!! Form::open(['method'=>'post', 'files'=>true, 'enctype' => 'multipart/form-data', 'route'=>[$action], 'class' => 'formValidate','onsubmit'=>'return checkSize(4194304)']) !!}
                            {!! Form::hidden('add_by', \Illuminate\Support\Facades\Auth::user()->id) !!}
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
                                            @if($value=='images')
                                                <div class="col-sm-12" id="{{$value}}">
                                                    <div class="form-group row">
                                                        <label for="{{$value}}" class="col-form-label">{{$key}}</label>
                                                        <span style="color: red">*</span>
                                                        <input required class="upload form-control" id="uploadFile" type="file" accept="image/*" name="images[]" multiple />
                                                    </div>
                                                </div>
                                                <br/>
                                                <div class="form-group" id="image_preview"></div>
                                            @elseif($value=='sunder_count' || $value=='kms_count')
                                                <div class="col-sm-12">
                                                    <div class="form-group" id="{{$value}}">
                                                        <label for=""> {{$key}}</label>
                                                        <span style="color: red">*</span>
                                                        <input name="{{$value}}" class="form-control" type="number" min="1">
                                                        <div class="help-block form-text with-errors form-control-feedback"></div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                        <div class="col-sm-12">
                                            <div class="white-box">
                                                <label for="input-file-now-custom-1">صورة الاستمارة</label>
                                                <span style="color: red">*</span>
                                                <input name="paper_image" type="file" id="input-file-now-custom-1 image" class="dropify" data-default-file="{{asset('media/images/user/default.jpeg')}}"/>
                                            </div>
                                        </div>

                                            <div class="col-sm-12" id="marks">
                                                <div class="form-group">
                                                    <label for=""> الماركة </label>
                                                    <span style="color: red">*</span>
                                                    <select id="mark_id" name="mark_id" class="form-control">
                                                        @foreach(\App\DropDown::active()->where('class','Mark')->whereHas('childs')->get() as $mark)
                                                            <option value="{{$mark->id}}">
                                                                {{$mark->name['ar']}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-sm-12" id="models" hidden>
                                                <div class="form-group">
                                                    <label for=""> الموديل </label>
                                                    <span style="color: red">*</span>
                                                    <select required id="model_id" name="model_id" class="form-control">
                                                        @foreach(\App\DropDown::active()->where('class','Mark')->whereHas('childs')->first()->childs() as $model)
                                                            <option value="{{$model->id}}">
                                                                {{$model->name['ar']}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div id="year" class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="">سنة الصنع</label>
                                                    <span style="color: red">*</span>
                                                    <select id="year" name="year" class="form-control">
                                                        @for($year=1980;$year<=2040;$year++)
                                                            <option value="{{$year}}">
                                                                {{$year}}
                                                            </option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>

                                            <div id="color" class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="">اللون</label>
                                                    <span style="color: red">*</span>
                                                    <select id="color" name="color_id" class="form-control">
                                                        @foreach(\App\DropDown::active()->where('class','Color')->get() as $color)
                                                            <option value="{{$color->id}}">
                                                                {{$color->name['ar']}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div id="fetes_id" class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="">نوع ناقل الحركة</label>
                                                    <span style="color: red">*</span>
                                                    <select id="fetes_id" name="fetes_id" class="form-control">
                                                        @foreach(\App\DropDown::active()->where('class','Fetes')->get() as $fetes)
                                                            <option value="{{$fetes->id}}">
                                                                {{$fetes->name['ar']}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div id="item_status_id" class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="">حالة المركبة</label>
                                                    <span style="color: red">*</span>
                                                    <select id="item_status_id" name="item_status_id" class="form-control">
                                                        @foreach(\App\DropDown::active()->where('class','ItemStatus')->get() as $item_status)
                                                            <option value="{{$item_status->id}}">
                                                                {{$item_status->name['ar']}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div id="scan_status_id" class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="">حالة الفحص</label>
                                                    <span style="color: red">*</span>
                                                    <select id="scan_status_id" name="scan_status_id" class="form-control">
                                                        @foreach(\App\DropDown::active()->where('class','ScanStatus')->get() as $scan_status)
                                                            <option value="{{$scan_status->id}}">
                                                                {{$scan_status->name['ar']}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div id="paper_status_id" class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="">حالة الاستمارة</label>
                                                    <span style="color: red">*</span>
                                                    <select id="paper_status_id" name="paper_status_id" class="form-control">
                                                        @foreach(\App\DropDown::active()->where('class','PaperStatus')->get() as $paper_status)
                                                            <option value="{{$paper_status->id}}">
                                                                {{$paper_status->name['ar']}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div id="city_id" class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="">المدينة </label>
                                                    <span style="color: red">*</span>
                                                    <select id="city_id" name="city_id" class="form-control">
                                                        @foreach(\App\DropDown::active()->where('class','City')->get() as $city)
                                                            <option value="{{$city->id}}">
                                                                {{$city->name['ar']}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div id="auction_type" class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="">نوع المزاد</label>
                                                    <span style="color: red">*</span>
                                                    <select id="auction_type_id" name="auction_type_id" class="form-control">
                                                        @foreach(\App\AuctionType::all() as $auction_type)
                                                            <option value="{{$auction_type->id}}">
                                                                {{$auction_type->name['ar']}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-sm-12" id="user_price" hidden>
                                                <div class="form-group">
                                                    <label for="">سعر المركبة</label>
                                                    <span style="color: red">*</span>
                                                    <input name="price" class="form-control" type="number" min="1">
                                                    <div class="help-block form-text with-errors form-control-feedback"></div>
                                                </div>
                                            </div>

                                    </div>
                                </fieldset>
                            @endif
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
    <script type="text/javascript">
        $("#uploadFile").change(function(){
            $('#image_preview').html("");
            var total_file=document.getElementById("uploadFile").files.length;
            for(var i=0;i<total_file;i++)
            {
                $('#image_preview').append("<img style='pointer-events: none;max-height: 100px;max-width: 100px;height: 100px;border-radius: 10px;margin: 5px;' src='"+URL.createObjectURL(event.target.files[i])+"'>");
            }
        });
        window.onload = function (){
            $('#mark_id').change(function (){
                var mark_id = $('#mark_id').val();
                $.ajax({
                    type: "GET",
                    url:'/e-auction/public/admin/get_models/'+mark_id,
                    dataType: 'json',
                    success: function( data ) {
                        console.log(data)
                        $('#models').empty();
                        var res = '<div class="form-group"><label for=""> الموديل </label><select id="model_id" name="model_id" class="form-control">';
                        $.each (data, function (key, value)
                        {
                            res +=
                                '<option value="'+value.id+'">'+value.name+'</option>';
                        });
                        res +='</select></div></div>';
                        $('#models').html(res);
                        $('#models').removeAttr('hidden');
                    }
                });
            });
            $('#auction_type_id').change(function (){
                var auction_type_id = $('#auction_type_id').val();
                console.log(auction_type_id)
                if(auction_type_id==3 || auction_type_id==4){
                    $('#user_price').removeAttr('hidden');
                }else {
                    $('#user_price').attr('hidden','hidden');
                }
            });
        };

    </script>
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
    <script type="text/javascript">
        function checkSize(max_img_size)
        {
            var input = document.getElementById("uploadFile");
            console.log('checkSize')
            // check for browser support (may need to be modified)
            if(input.files)
            {
                console.log(input.files)
                for (var i=0;i<input.files.length;i++){
                    if (input.files[i].size > max_img_size)
                    {
                        alert("The file must be less than " + (max_img_size/1024/1024) + "MB");
                        return false;
                    }
                }
                return true;
            }
            return true;
        }
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

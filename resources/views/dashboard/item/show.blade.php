@extends('dashboard.master.base')
@section('title',$title)
@section('style')
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDAXioTdC6khsa8ECLMd5qxWBjlb1OqtO0&&callback=initMap" type="text/javascript">
</script>
<style>
    .map
    {
        position: absolute !important;
        height: 100% !important;
        width: 100% !important;
    }
</style>
<link rel="stylesheet" href="{{asset('panel/dropify/dist/css/dropify.min.css')}}">

@endsection
@section('content')
    <div class="content-i">
        <div class="content-box">
            <div class="row">
{{--                first box--}}
                <div class="col-sm-5">
                    <div class="user-profile compact">
                        @php
                        if (is_array($row->imagesArray())){
                            if (count($row->imagesArray())>0){
                              $single_image=$row->images[0];
                            }else{
                                $single_image='';
                            }
                        }else{
                            $single_image='';
                        }
                        @endphp
                        <div class="up-head-w" style="background-image:url({{$single_image}})">
                            <div class="up-main-info">
                                <h2 class="up-header">
                                    {{$row->name}}
                                </h2>
                            </div>
                            <svg class="decor" width="842px" height="219px" viewBox="0 0 842 219" preserveAspectRatio="xMaxYMax meet" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <g transform="translate(-381.000000, -362.000000)" fill="#FFFFFF">
                                    <path class="decor-path" d="M1223,362 L1223,581 L381,581 C868.912802,575.666667 1149.57947,502.666667 1223,362 Z">
                                    </path>
                                </g>
                            </svg>
                        </div>
                        <div class="up-controls">
                        @if($row->status=='pending')
                            <div class="row">
                                <div class="col-md-6">
                                    <a class='reject btn btn-danger btn-sm' data-href='{{route('admin.item.reject',$row->id)}}' href=''><i class='os-icon os-icon-cancel-circle'></i><span>رفض السلعة</span></a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <a class='accept btn btn-success btn-sm' data-href='{{route('admin.item.accept',$row->id)}}' href=''><i class='os-icon os-icon-shopping-cart'></i><span>قبول السلعة</span></a>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="value-pair">
                                        <div class="label" style="font-size: large">
                                            الحالة
                                        </div>
                                        <div class="icon-action-redo">
                                            {!!$row->itemStatusIcon()!!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        </div>
                    </div>

                    <div class="element-wrapper">
                        <div class="row">
                                @if(\App\AuctionItem::where('item_id',$row->id)->first())
                                    <div class="col-sm-6">
                                        <a class="element-box el-tablo centered trend-in-corner padded bold-label">
                                            <div class="value">
                                                @php
                                                    $auction_id=\App\AuctionItem::where('item_id',$row->id)->latest()->value('auction_id');
                                                @endphp
                                                {{$auction_id}}
                                            </div>
                                            <div class="label">
                                                الرقم التسلسلى للمزاد
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-sm-6">
                                        <a class="element-box el-tablo centered trend-in-corner padded bold-label">
                                            <div class="value">
{{--                                                {{\App\AuctionUser::where(['item_id'=>$row->id,'auction_id'=>$auction_id])->sum('charge_price')}}--}}
                                                {{\App\AuctionItem::where(['item_id'=>$row->id,'auction_id'=>$auction_id])->orderBy('id','DESC')->value('price')}}
                                            </div>
                                            <div class="label">
                                                السعر الأخير
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-sm-6">
                                        <a class="element-box el-tablo centered trend-in-corner padded bold-label">
                                            <div class="value">
                                                {{\App\AuctionUser::where(['item_id'=>$row->id,'auction_id'=>$auction_id])->count()}}
                                            </div>
                                            <div class="label">
                                                عدد المزايدات
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            @if(isset($auction_id))
                                @if(\App\AuctionUser::where(['item_id'=>$row->id,'auction_id'=>$auction_id])->count() > 0)
                                    <div class="col-sm-6">
                                        <a class="element-box el-tablo centered trend-in-corner padded bold-label" href="{{route('admin.user.show',[\App\AuctionUser::where(['item_id'=>$row->id,'auction_id'=>$auction_id])->orderBy('id','DESC')->value('user_id')])}}">
                                            <div class="value">
                                                {{\App\User::whereId(\App\AuctionUser::where(['item_id'=>$row->id,'auction_id'=>$auction_id])->orderBy('id','DESC')->value('user_id'))->value('name')}}
                                            </div>
                                            <div class="label">
                                                الأعلى مزايدة
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="element-wrapper">
                        <div class="element-box">
                            <h6 class="element-header">
                                اخر النشاطات
                            </h6>
                            <div class="timed-activities compact" style="overflow:scroll;max-height: 500px">
                                @if(isset($auction_id) && \App\AuctionUser::where(['item_id'=>$row->id,'auction_id'=>$auction_id])->latest()->count() > 0)
                                    @foreach(\App\AuctionUser::where(['item_id'=>$row->id,'auction_id'=>$auction_id])->orderBy('id','DESC')->get() as $auction_user)
                                        @php
                                            $user_route=route('admin.user.show',$auction_user->user_id);
                                            $user_name=\App\User::whereId($auction_user->user_id)->value('name');
                                            $item_route=route('admin.item.show',$auction_user->item_id);
                                            $item_name=$auction_user->item->mark->name['ar'].' '.$auction_user->item->model->name['ar'];
                                            $user_href="<a href='".$user_route."'>".$user_name."</a>";
                                            $item_href="<a href='".$item_route."'>".$item_name."</a>";
                                        @endphp
                                        <div class="timed-activity">
                                            <div class="ta-date">
                                                <span>{{$auction_user->ArabicTimeDate($auction_user->created_at)}}</span>
                                            </div>
                                            <div class="ta-record-w">
                                                <div class="ta-record">
                                                    <div class="ta-timestamp">
                                                        <strong>{{\Carbon\Carbon::parse($auction_user->created_at)->format('H:i:s A')}}</strong>
                                                    </div>
                                                    <div class="ta-activity">
                                                        {!! $item_href. 'قام بمزايدة بمبلغ ' .$auction_user->charge_price. ' ريال على سلعة ' . $user_href !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="ta-activity font-italic">
                                        ﻻ يوجد اى نشاطات بعد
                                    </div>
                                @endif
                            </div>
                        </div>




                    </div>

                </div>
{{--                end first box--}}
{{--                show fields--}}
                <div class="col-sm-7">
                    <div class="element-wrapper">
                        <div class="element-box">
                        <form method="POST" action="{{route('admin.item.update',$row->id)}}" enctype="multipart/form-data" data-parsley-validate novalidate>
                            @csrf
                            @method('PUT')
                            <div class="element-info">
                                    <div class="element-info-with-icon">
                                        <div class="element-info-icon">
                                            <div class="os-icon os-icon-wallet-loaded"></div>
                                        </div>
                                        <div class="element-info-text">
                                            <h5 class="element-inner-header">
                                                البيانات المضافة من قبل المستخدم
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <fieldset class="form-group">
                                    <div class="row">
                                        @foreach($show_fields as $key=>$value)
                                            @if($value== 'created_at')
                                                <div class="col-sm-12">
                                                    <div class="form-group" id="{{$value}}">
                                                        <label for=""> {{$key}}</label>
                                                        <input disabled name="{{$value}}" value="{!! $row->published_at() !!}" class="form-control" type="text">
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-sm-12">
                                                    <div class="form-group" id="{{$value}}">
                                                        <label for=""> {{$key}}</label><input disabled name="{{$value}}" class="form-control" value="{{$row->$value}}" type="text">
                                                        <div class="help-block form-text with-errors form-control-feedback"></div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                            <div class="col-sm-12">
                                                <div class="form-group" id="shipping_by" data-value="{{$row->shipping_by}}">
                                                    <label for=""> نوع الشحن</label>
                                                    <input disabled name="shipping_by" class="form-control" value="{{$row->shipping_by=='app'?'الشحن عن طريق التطبيق':'الشحن عن طريق المستخدم'}}" type="text">
                                                    <div class="help-block form-text with-errors form-control-feedback"></div>
                                                </div>
                                            </div>
                                            @if(isset($selects))
                                                @foreach($selects as $select)
                                                    @php($related_model=$select['name'])
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label for=""> {{$select['title']}} </label>
                                                            @if(array_key_exists("route",$select))
                                                                <a href="{{$select['route']}}">
                                                                    <input disabled value="{!!$row->$related_model?$row->$related_model->nameForSelect():""!!}" class="form-control" type="text">
                                                                </a>
                                                            @else
                                                                <input disabled value="{!!$row->$related_model?$row->$related_model->nameForSelect():""!!}" class="form-control" type="text">
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                            @if(isset($location) && $row->location!=null)
                                                <div class="col-sm-12">
                                                    <div class="card-img" style="height: 400px">
                                                        <label for="map">الموقع</label>
                                                        <div id="map" data-lat="{{$row->location['lat']}}" data-lng="{{$row->location['lng']}}" class="map"></div>
                                                    </div>
                                                </div>
                                                <br>
                                            @endif
                                            <br>
                                            <br>
                                            <br>
                                            <div class="col-sm-12" id="images">
                                                <div class="form-group row">
                                                    <label for="images" class="col-form-label">صور المركبة</label>
                                                    <div id="itemImages" data-images="{{$row->imagesArray()}}">
                                                    </div>
                                                    <input  required class="upload form-control" id="uploadFile"
                                                           type="file" accept="image/*" name="images[]" multiple/>
                                                </div>
                                            </div>
                                            <br/>
                                            <div class="form-group" id="image_preview"></div>
                                            <div class="col-sm-12">
                                                <div class="white-box">
                                                    <label for="input-file-now-custom-1">صورة الاستمارة</label>
                                                    <span style="color: red">*</span>
                                                    <input  name="paper_image" type="file" id="input-file-now-custom-1 image"
                                                           class="dropify"
                                                           data-default-file="{{$row->paper_image}}"/>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group" id="sunder_count">
                                                    <label for=""> حجم الماكينة</label>
                                                    <input  required value="{{$row->sunder_count}}" name="sunder_count" class="form-control" type="number"
                                                           min="1">
                                                    <div
                                                        class="help-block form-text with-errors form-control-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group" id="kms_count">
                                                    <label for="">عدد الكيلومترات</label>
                                                    <input  required value="{{$row->kms_count}}" name="kms_count" class="form-control" type="number"
                                                           min="1">
                                                    <div
                                                        class="help-block form-text with-errors form-control-feedback"></div>
                                                </div>
                                            </div>


                                            <div class="col-sm-12" id="marks">
                                                <div class="form-group">
                                                    <label for=""> الماركة </label>
                                                    <span style="color: red">*</span>
                                                    <select  id="mark_id" name="mark_id" class="form-control">
                                                        <option value="{{$row->mark_id}}">
                                                            {{$row->mark->name['ar']}}
                                                        </option>
                                                        @foreach(\App\DropDown::active()->where('class','Mark')->whereHas('childs')->get() as $mark)
                                                            <option value="{{$mark->id}}">
                                                                {{$mark->name['ar']}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-sm-12" id="models">
                                                <div class="form-group">
                                                    <label for=""> الموديل </label>
                                                    <span style="color: red">*</span>
                                                    <select  required id="model_id" name="model_id" class="form-control">
                                                        <option value="{{$row->model->id}}">
                                                            {{$row->model->name['ar']}}
                                                        </option>

                                                    </select>
                                                </div>
                                            </div>

                                            <div id="year" class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="">سنة الصنع</label>
                                                    <span style="color: red">*</span>
                                                    <select  id="year" name="year" class="form-control">
                                                        <option value="{{$row->year}}">
                                                            {{$row->year}}
                                                        </option>
                                                        @for($year=1900;$year<=2040;$year++)
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
                                                    <select  id="color" name="color_id" class="form-control">
                                                        <option value="{{$row->color->id}}">
                                                            {{$row->color->name['ar']}}
                                                        </option>
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
                                                    <select  id="fetes_id" name="fetes_id" class="form-control">
                                                        <option value="{{$row->fetes->id}}">
                                                            {{$row->fetes->name['ar']}}
                                                        </option>
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
                                                    <select  id="item_status_id" name="item_status_id" class="form-control">
                                                        <option value="{{$row->item_status->id}}">
                                                            {{$row->item_status->name['ar']}}
                                                        </option>
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
                                                    <select  id="scan_status_id" name="scan_status_id" class="form-control">
                                                        <option value="{{$row->scan_status->id}}">
                                                            {{$row->scan_status->name['ar']}}
                                                        </option>
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
                                                    <select  id="paper_status_id" name="paper_status_id"
                                                            class="form-control">
                                                        <option value="{{$row->paper_status->id}}">
                                                            {{$row->paper_status->name['ar']}}
                                                        </option>
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
                                                    <select  id="city_id" name="city_id" class="form-control">
                                                        <option value="{{$row->city->id}}">
                                                            {{$row->city->name['ar']}}
                                                        </option>
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
                                                    <select  id="auction_type_id" name="auction_type_id"
                                                            class="form-control">
                                                        <option value="{{$row->auction_type->id}}">
                                                            {{$row->auction_type->name['ar']}}
                                                        </option>
                                                        @foreach(\App\AuctionType::all() as $auction_type)
                                                            <option value="{{$auction_type->id}}">
                                                                {{$auction_type->name['ar']}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-sm-12" id="user_price" @if($row->auction_type_id!=3 && $row->auction_type_id!=4) hidden @endif>
                                                <div class="form-group">
                                                    <label for="">سعر المركبة</label>
                                                    <span style="color: red">*</span>
                                                    <input  name="price" value="{{$row->price}}" class="form-control" type="number" min="1">
                                                    <div
                                                        class="help-block form-text with-errors form-control-feedback"></div>
                                                </div>
                                            </div>

                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <div class="form-check">
                                                        <span style="color: red">*</span>
                                                        <input name="tax" class="form-check-input" type="checkbox"
                                                               value="{{$row->tax}}" id="tax">
                                                        <label class="form-check-label" for="tax">لدي سجل ضريبي</label>
                                                    </div>
                                                </div>
                                            </div>








                                    </div>
                                </fieldset>
                                <div class="form-buttons-w">
                                    <button class="btn btn-primary create-submit" type="submit"> تعديل </button>
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
    <script src="{{asset('panel/dropify/dist/js/dropify.min.js')}}"></script>
    <script>
        $(document).ready(function () {
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
            drEvent.on('dropify.beforeClear', function (event, element) {
                return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
            });
            drEvent.on('dropify.afterClear', function (event, element) {
                alert('File deleted');
            });
            drEvent.on('dropify.errors', function (event, element) {
                console.log('Has Errors');
            });
            var drDestroy = $('#input-file-to-destroy').dropify();
            drDestroy = drDestroy.data('dropify')
            $('#toggleDropify').on('click', function (e) {
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
        $(document).ready(function () {
            $("#uploadFile").change(function () {
                $('#image_preview').html("");
                var total_file = document.getElementById("uploadFile").files.length;
                for (var i = 0; i < total_file; i++) {
                    $('#image_preview').append("<img style='pointer-events: none;max-height: 100px;max-width: 100px;height: 100px;border-radius: 10px;margin: 5px;' src='" + URL.createObjectURL(event.target.files[i]) + "'>");
                }
            });
            let files=JSON.parse($("#itemImages").attr('data-images'));
            for(var i=0;i<files.length;i++)
            {
                $('#image_preview').append("<img style='pointer-events: none;max-height: 100px;max-width: 100px;margin-right: 5px;margin-left: 5px;border-radius: 10px;' src='"+files[i]+"'>");
            }
        });
    </script>
    <script type="text/javascript">
        let map;
        let marker;
        function initMap() {
            // show map
            let lat_str = document.getElementById('map').getAttribute("data-lat");
            let long_str = document.getElementById('map').getAttribute("data-lng");
            let uluru = {lat:parseFloat(lat_str), lng: parseFloat(long_str)};
            let centerOfOldMap = new google.maps.LatLng(uluru);
            let oldMapOptions = {
                center: centerOfOldMap,
                zoom: 6
            };
            map = new google.maps.Map(document.getElementById('map'), oldMapOptions);
            marker = new google.maps.Marker({position: centerOfOldMap,animation:google.maps.Animation.BOUNCE});
            marker.setMap(map);
        }
        google.maps.event.addDomListener(window, 'load', initMap);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script>
        $(document).on('click', '.reject', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'من فضلك اذكر سبب الرفض',
                input: 'text',
                showCancelButton: true,
                confirmButtonText: 'رفض',
                cancelButtonText: 'الغاء',
                showLoaderOnConfirm: true,
                preConfirm: (reject_reason) => {
                    $.ajax({
                        url: $(this).data('href'),
                        type:'GET',
                        data: {reject_reason}
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then(() => {
                location.href = "https://sanedapps.com/e-auction/public/admin/item/status/rejected";
            })
        });
        $(document).on('click', '.accept', function (e) {
            e.preventDefault();
            let shipping_by = document.getElementById('shipping_by').getAttribute("data-value");
            console.log(shipping_by)
            if (shipping_by==='user'){
                Swal.fire({
                    title: "هل انت متأكد من القبول ؟",
                    text: "تأكد من اجابتك قبل التأكيد!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: 'btn-info',
                    confirmButtonText: 'نعم , قم بالقبول!',
                    cancelButtonText: 'ﻻ , الغى عملية القبول!',
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    preConfirm: () => {
                        $.ajax({
                            url: $(this).data('href'),
                            type:'GET',
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then(() => {
                    location.href = "https://sanedapps.com/e-auction/public/admin/item/status/accepted";
                })
            }else {
                Swal.fire({
                    title: "من فضلك اذكر سعر الشحن!",
                    input: 'number',
                    showCancelButton: true,
                    confirmButtonText: 'نعم , قم بالقبول!',
                    cancelButtonText: 'ﻻ , الغى عملية القبول!',
                    showLoaderOnConfirm: true,
                    preConfirm: (shipping_price) => {
                        $.ajax({
                            url: $(this).data('href'),
                            type:'GET',
                            data: {shipping_price}
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then(() => {
                    location.href = "https://sanedapps.com/e-auction/public/admin/item/status/accepted";
                })
            }
        });
    </script>

    <script type="text/javascript">
        window.onload = function () {
            $('#mark_id').change(function () {
                var mark_id = $('#mark_id').val();
                $.ajax({
                    type: "GET",
                    url: 'https://sanedapps.com/e-auction/public/admin/get_models/' + mark_id,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data)
                        $('#models').empty();
                        var res = '<div class="form-group"><label for=""> الموديل </label><select id="model_id" name="model_id" class="form-control">';
                        $.each(data, function (key, value) {
                            res +=
                                '<option value="' + value.id + '">' + value.name + '</option>';
                        });
                        res += '</select></div></div>';
                        $('#models').html(res);
                        $('#models').removeAttr('hidden');
                    }
                });
            });
            $('#auction_type_id').change(function () {
                var auction_type_id = $('#auction_type_id').val();
                if (auction_type_id == 3 || auction_type_id == 4) {
                    $('#user_price').removeAttr('hidden');
                } else {
                    $('#user_price').attr('hidden', 'hidden');
                }
            });
        };

    </script>
@endsection

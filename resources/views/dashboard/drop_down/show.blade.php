@extends('dashboard.master.base')
@section('title',$title)
@section('content')
    <div class="content-i">
        <div class="content-box">
            <div class="row">
{{--                first box--}}
                <div class="col-sm-5">
                    <div class="user-profile compact">
                        <div class="up-head-w" style="background-image:url({{$row->images[0]}})">
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
                            <div class="row">
                                <div class="col-md-6">
                                    <a class='reject btn btn-danger btn-sm' data-href='{{route('admin.item.reject',$row->id)}}' href=''><i class='os-icon os-icon-cancel-circle'></i><span>رفض السلعة</span></a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <a class='accept btn btn-success btn-sm' data-href='{{route('admin.item.accept',$row->id)}}' href=''><i class='os-icon os-icon-shopping-cart'></i><span>قبول السلعة</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
{{--                end first box--}}
{{--                show fields--}}
                <div class="col-sm-7">
                    <div class="element-wrapper">
                        <div class="element-box">
                                <div class="element-info">
                                    <div class="element-info-with-icon">
                                        <div class="element-info-icon">
                                            <div class="os-icon os-icon-wallet-loaded"></div>
                                        </div>
                                        <div class="element-info-text">
                                            <h5 class="element-inner-header">
                                                البيانات
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <fieldset class="form-group">
                                    <div class="row">
                                        @foreach($show_fields as $key=>$value)
                                            @if($value=='images')
                                                <div class="col-sm-12" id="{{$value}}">
                                                    <div class="form-group row hidden">
                                                        <label for="{{$value}}" class="col-2 col-form-label">{{$key}}</label>
                                                        <input hidden disabled class="upload form-control" id="itemImages" type="file" data-images="{{$row->imagesArray()}}" accept="image/*" name="images[]" multiple />
                                                    </div>
                                                </div>
                                                <br/>
                                                <div class="form-group" id="image_preview"></div>
                                            @elseif($value== 'paper_image')
                                                <div class="col-sm-12">
                                                    <div class="form-group" id="{{$value}}">
                                                        <label for=""> {{$key}}</label>
                                                        <img src="{{$row->$value}}">
                                                    </div>
                                                </div>
                                            @elseif($value== 'created_at')
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
                                            @if(isset($selects))
                                                @foreach($selects as $select)
                                                    @php($related_model=$select['name'])
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label for=""> {{$select['title']}} </label>
                                                            @if(array_key_exists("route",$select))
                                                                <a href="{{$select['route']}}">
                                                                    <input disabled value="{!!$row->$related_model->nameForSelect()!!}" class="form-control" type="text">
                                                                </a>
                                                            @else
                                                                <input disabled value="{!!$row->$related_model->nameForSelect()!!}" class="form-control" type="text">
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
                                    </div>
                                </fieldset>
                        </div>
                    </div>
                </div>
{{--                end show fields--}}
            </div>

        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        $(document).ready(function () {
            let files=JSON.parse($("#itemImages").attr('data-images'));
            for(var i=0;i<files.length;i++)
            {
                console.log(files[i])
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
                location.href = "/admin/item/status/rejected";
            })
        });
        $(document).on('click', '.accept', function (e) {
            e.preventDefault();
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
                location.href = "/admin/item/status/accepted";
            })
        });
    </script>
@endsection

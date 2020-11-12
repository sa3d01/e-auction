@extends('dashboard.master.base')
@section('title',$title)
@section('style')
    <link rel="stylesheet" href="{{asset('panel/dropify/dist/css/dropify.min.css')}}">
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBKhmEeCCFWkzxpDjA7QKjDu4zdLLoqYVw&&callback=initMap" type="text/javascript">
    </script>
    <style>
        .map
        {
            position: absolute !important;
            height: 100% !important;
            width: 100% !important;
        }
    </style>
@endsection
@section('content')
    <div class="content-i">
        <div class="content-box">
            <div class="row">
{{--                first box--}}
                <div class="col-sm-5">
                    <div class="user-profile compact">
                        <div class="up-head-w" style="background-image:url({{$row->image}})">
                            <div class="up-main-info">
                                <h2 class="up-header">
                                    {{$row->name}}
                                </h2>
                                <h6 class="up-sub-header">
                                    {!!$row->getRateIcon()!!}
                                </h6>
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
                                <div class="col-sm-6">
                                    <div class="value-pair">
                                        <div class="label" style="font-size: large">
                                            الحالة
                                        </div>
                                        <div class="icon-action-redo">
                                            {!!$row->getStatusIcon()!!}
                                        </div>
                                    </div>
                                </div>
                                @can('edit-wallets')
                                <div class="col-sm-6 text-right">
                                    {!! $row->walletDecrement() !!}
                                </div>
                                @endcan
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
                                            @if($value=='note')
                                                <div class="col-sm-12">
                                                    <div class="form-group" id="{{$value}}">
                                                        <label> {{$key}} </label>
                                                        <textarea disabled name="{{$value}}" class="form-control" cols="80" rows="10">
                                                        {{$row->$value}}
                                                    </textarea>
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
                                        @if(isset($image))
                                            <div class="col-sm-12">
                                                <div class="white-box">
                                                    <label for="input-file-now-custom-1">الصورة</label>
                                                    <input disabled name="image" type="file" id="input-file-now-custom-1" class="dropify" data-default-file="{{$row->image}}"/>
                                                </div>
                                            </div>
                                        @endif
                                        @if(isset($address) && $row->location!=null)
                                            <div class="col-sm-12">
                                                <div class="card-img" style="height: 400px">
                                                    <label for="map">الموقع</label>
                                                    <div id="map" data-lat="{{$row->location['lat']}}" data-lng="{{$row->location['lng']}}" class="map"></div>
                                                </div>
                                            </div>
                                            <br>
                                        @endif
                                            <div class="col-sm-12">
                                                <label for="attachments">الملفات المرفقة</label>
                                                @if(array_key_exists("attachments",(array)$row->more_details))
                                                    @foreach($row->more_details['attachments'] as $attachment)
                                                        <br>
                                                        <div>
                                                            <label for="map">{{$attachment['file_name']}}</label>
                                                            <iframe id="iframe" src="{{asset('media/files/attachment/'.$attachment['attachment'])}}" style="width:100%; height:300px;"></iframe>
                                                        </div>
                                                        <br>
                                                    @endforeach
                                                @else
                                                    <div>
                                                        <input disabled value="ﻻ يوجد ملفات مرفقة" class="form-control" type="text">
                                                    </div>
                                                @endif
                                            </div>
                                    </div>
                                </fieldset>
                        </div>
                    </div>
                </div>
{{--                end show fields--}}
            </div>
            <div  class="table-responsive">
                <table id="datatable" width="100%" class="table table-striped table-lightfont">
                    <thead>
                    <tr>
                        <th hidden></th>
                        <th>الرقم التسلسلى</th>
                        <th>التكلفة الاجمالية للطلب</th>
                        <th>نسبة التطبيق</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th hidden></th>
                        <th>الرقم التسلسلى</th>
                        <th>التكلفة الاجمالية للطلب</th>
                        <th>نسبة التطبيق</th>
                    </tr>
                    </tfoot>
                    <tbody>
                    @foreach($rows as $row)
                        <tr>
                            <td hidden>{{$row->id}}</td>
                            <td><a href="{{route('admin.order.show',$row->order_id)}}"># {{$row->order_id}}</a></td>
                            <td>{{$row->order->price}}</td>
                            <td>{{$row->app_ratio}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script>
        $(document).on('click', '#wallet_decrement', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'من فضلك أدخل القيمة التى تم سدادها',
                input: 'number',
                showCancelButton: true,
                confirmButtonText: 'تسديد',
                cancelButtonText: 'الغاء',
                showLoaderOnConfirm: true,
                preConfirm: (wallet_decrement_value) => {
                    $.ajax({
                        url: $(this).data('href'),
                        type:'GET',
                        data: {wallet_decrement_value}
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then(() => {
                location.reload(true);
            })
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
                zoom: 9
            };
            map = new google.maps.Map(document.getElementById('map'), oldMapOptions);
            marker = new google.maps.Marker({position: centerOfOldMap,animation:google.maps.Animation.BOUNCE});
            marker.setMap(map);
        }
        google.maps.event.addDomListener(window, 'load', initMap);
    </script>
@endsection

@extends('dashboard.master.base')
@section('title','الاعدادات العامة')
@section('style')
    <link rel="stylesheet" href="{{asset('panel/dropify/dist/css/dropify.min.css')}}">
    <style>
        .map
        {
            position: absolute !important;
            height: 100% !important;
            width: 100% !important;
        }
        .file-upload input[type='file']{
            height:200px;
            width:200px;
            position:absolute;
            top:0;
            left:0;
            opacity:0;
            cursor:pointer;
        }
    </style>

    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBZ7PLBfMF15gMexw6vZrbfSqWBcNeyKlM&language=ar&&callback=initMap&libraries=places" type="text/javascript">
    </script>
@endsection
@section('content')
    <div class="content-i">
        <div class="content-box">
            <div class="row">
                <div class="col-sm-12">
                    <div class="element-wrapper">
                        <div class="element-box">
                            {!! Form::open(['method'=>'post', 'files'=>true, 'enctype' => 'multipart/form-data', 'route'=>'admin.setting.update', 'class' => 'formValidate']) !!}
                            {!! Form::hidden('updated_by', \Illuminate\Support\Facades\Auth::user()->id) !!}
                            <div class="element-info">
                                <div class="element-info-with-icon">
                                    <div class="element-info-icon">
                                        <div class="os-icon os-icon-wallet-loaded"></div>
                                    </div>
                                    <div class="element-info-text">
                                        <h5 class="element-inner-header">
                                            الإعدادات العامة
                                        </h5>
                                        <div class="element-inner-desc">
                                            يرجى تحرى الحظر خلال عمليات التعديل فى هذه التعديلات
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <fieldset class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>
                                                <i class="os-icon os-icon-email-2-at"></i>البريد الإلكترونى
                                            </label>
                                            <input name="email" value="{{$row->contacts['email']}}" class="form-control" type="email">
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <i class="os-icon os-icon-phone"></i>رقم الجوال
                                            </label>
                                            <input name="mobile" value="{{$row->contacts['mobile']}}" class="form-control" type="text">
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <i class="os-icon os-icon-map-pin"></i>العنوان
                                            </label>
                                            <input name="address" value="{{$row->contacts['address']}}" class="form-control" type="text">
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="card-img" style="height: 400px">
                                                <label for="map">الموقع</label>
                                                <div id="map" data-lat="{{$row->address['lat']}}" data-lng="{{$row->address['lng']}}" class="map"></div>
                                                <input name="lat" type="hidden" id="lat">
                                                <input name="lng" type="hidden" id="lng">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label>
                                                <i class="os-icon os-icon-dollar-sign"></i>مقدار المزايدة على السلع الأقل سعرها من عشرة الاف
                                            </label>
                                            <input name="less_tenThousand" value="{{$row->more_details['less_tenThousand']}}" class="form-control" type="number" min="0">
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <i class="os-icon os-icon-dollar-sign"></i>مقدار المزايدة على السلع الأقل سعرها من مئة ألف
                                            </label>
                                            <input name="less_hundredThousand" value="{{$row->more_details['less_hundredThousand']}}" class="form-control" type="number" min="0">
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <i class="os-icon os-icon-dollar-sign"></i>مقدار المزايدة على السلع الأعلى من مئة ألف
                                            </label>
                                            <input name="more_hundredThousand" value="{{$row->more_details['more_hundredThousand']}}" class="form-control" type="number" min="0">
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <i class="os-icon os-icon-percent"></i>نسبة التطبيق على المزايد
                                            </label>
                                            <input name="app_ratio" value="{{$row->app_ratio}}" class="form-control" type="number" min="0">
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <i class="os-icon os-icon-percent"></i>نسبة الضريبة المضافة
                                            </label>
                                            <input name="owner_tax_ratio" value="{{$row->owner_tax_ratio}}" class="form-control" type="number" min="0">
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <i class="os-icon os-icon-dollar-sign"></i> رسوم نقل الملكية
                                            </label>
                                            <input name="finish_papers" value="{{$row->finish_papers}}" class="form-control" type="number" min="0">
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <i class="os-icon os-icon-dollar-sign"></i> الرسوم الإدارية
                                            </label>
                                            <input name="tax_ratio" value="{{$row->tax_ratio}}" class="form-control" type="number" min="0">
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <i class="os-icon os-icon-dollar-sign"></i>رسوم اضافة منتج
                                            </label>
                                            <input name="add_item_tax" value="{{$row->add_item_tax}}" class="form-control" type="number" min="0">
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>


                                        <div class="form-group">
                                            <label>
                                                <i class="os-icon os-icon-calendar-time"></i>مدة المفاوضه على السلعة بعد انتهاء المزاد المباشر
                                            </label>
                                            <input name="negotiation_period" value="{{$row->negotiation_period}}" class="form-control" type="number" min="0">
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>

                                        <div class="form-group">
                                            <label>
                                                <i class="os-icon os-icon-calendar-time"></i>مدة توقف المزايدات قبل المزاد المباشر
                                            </label>
                                            <input name="bid_pause_period" value="{{$row->bid_pause_period}}" class="form-control" type="number" min="0">
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>

                                        <div class="form-group">
                                            <label>
                                                <i class="os-icon os-icon-percent"></i>نسبة القوة الشرائية
                                            </label>
                                            <input name="purchasing_power_ratio" value="{{$row->purchasing_power_ratio}}" class="form-control" type="number" min="0">
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>

                                        <div class="form-group">
                                            <i class="os-icon os-icon-file-text"></i>
                                            <label>عن التطبيق باللغة العربية </label>
                                            <textarea name="about_ar" class="form-control" cols="80" rows="5">{{$row->about['ar']}}</textarea>
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                        <div class="form-group">
                                            <i class="os-icon os-icon-file-text"></i>
                                            <label>عن التطبيق باللغة الانجليزية </label>
                                            <textarea name="about_en" class="form-control" cols="80" rows="5">{{$row->about['en']}}</textarea>
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>

                                        <div class="form-group">
                                            <i class="os-icon os-icon-file-text"></i>
                                            <label>شروط الاستخدام باللغة العربية </label>
                                            <input id="licence_ar" name="licence_ar" type="file" accept="application/pdf"/>
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                        <br/>
                                        <div class="form-group" id="licence_ar_preview">
                                            @if($row->licence['ar']!=null)
                                                <iframe id="iframe" src="https://e-auction1.com/media/files/{{$row->licence['ar']}}" style="width:100%; height:500px;" frameborder="0"></iframe>
                                            @endif
                                        </div>
                                        <br>

                                        <div class="form-group">
                                            <i class="os-icon os-icon-file-text"></i>
                                            <label>شروط الاستخدام باللغة الانجليزية </label>
                                            <input id="licence_en" name="licence_en" type="file" accept="application/pdf"/>
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                        <br/>
                                        <div class="form-group" id="licence_en_preview">
                                            @if($row->licence['en']!=null)
                                                <iframe id="iframe" src="https://e-auction1.com/media/files/{{$row->licence['en']}}" style="width:100%; height:500px;" frameborder="0"></iframe>
                                            @endif
                                        </div>
                                        <br>

                                        <div class="form-group">
                                            <i class="os-icon os-icon-file-text"></i>
                                            <label>سياسة الخصوصية باللغة العربية </label>
                                            <textarea name="privacy_ar" class="form-control" cols="80" rows="5">{{$row->privacy['ar']}}</textarea>
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                        <div class="form-group">
                                            <i class="os-icon os-icon-file-text"></i>
                                            <label>سياسة الخصوصية باللغة الانجليزية </label>
                                            <textarea name="privacy_en" class="form-control" cols="80" rows="5">{{$row->privacy['en']}}</textarea>
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>

                                        <div class="form-group">
                                            <i class="os-icon os-icon-file-text"></i>
                                            <label>النص التعريفى للقوة الشرائية باللغة العربية </label>
                                            <textarea name="purchasing_power_text_ar" class="form-control" cols="80" rows="5">{{$row->purchasing_power_text['ar']}}</textarea>
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                        <div class="form-group">
                                            <i class="os-icon os-icon-file-text"></i>
                                            <label>النص التعريفى للقوة الشرائية باللغة الانجليزية </label>
                                            <textarea name="purchasing_power_text_en" class="form-control" cols="80" rows="5">{{$row->purchasing_power_text['en']}}</textarea>
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>

                                        <div class="form-group">
                                            <label>
                                                <i class="os-icon os-icon-twitter2"></i> رابط تويتر
                                            </label>
                                            <input name="twitter" value="{{$row->socials['twitter']??''}}" class="form-control" type="url">
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <i class="os-icon os-icon-facebook2"></i> رابط فيسبوك
                                            </label>
                                            <input name="facebook" value="{{$row->socials['facebook']??''}}" class="form-control" type="url">
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <img style="height: 20px;width: 20px" src="https://image.flaticon.com/icons/svg/169/169090.svg">
                                                رابط سناب شات
                                            </label>
                                            <input name="snap" value="{{$row->socials['snap']??''}}" class="form-control" type="url">
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <i class="os-icon os-icon-instagram"></i> رابط  انستجرام
                                            </label>
                                            <input name="instagram" value="{{$row->socials['instagram']??''}}" class="form-control" type="url">
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                                <div class="form-buttons-w">
                                    <button class="btn btn-primary create-submit" type="submit"> تعديل</button>
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
                    console.log(value)
                    $('#'+index).notify(
                        value,
                        'error',
                        { position:"top" }
                    );
                });
            })
        </script>
    @endif
    <script type="text/javascript">
        let new_map;
        let old_map;
        let marker;
        function initMap() {
            if (!document.getElementById('show_map')){
                marker = false;
            }else {
                // show map
                let lat_str = document.getElementById('map').getAttribute("data-lat");
                let long_str = document.getElementById('map').getAttribute("data-lng");
                let uluru = {lat:parseFloat(lat_str), lng: parseFloat(long_str)};
                let centerOfOldMap = new google.maps.LatLng(uluru);
                let oldMapOptions = {
                    center: centerOfOldMap,
                    zoom: 14
                };
                old_map = new google.maps.Map(document.getElementById('map'), oldMapOptions);
                marker = new google.maps.Marker({position: centerOfOldMap,animation:google.maps.Animation.BOUNCE});
                marker.setMap(old_map);
                // end show map
            }
            // new map
            let centerOfNewMap = new google.maps.LatLng(24.665658,46.7440368);
            let newMapOptions = {
                center: centerOfNewMap,
                zoom: 14
            };
            new_map = new google.maps.Map(document.getElementById('map'), newMapOptions);
            // end new map
            google.maps.event.addListener(new_map, 'click', function(event) {
                let clickedLocation = event.latLng;
                if(marker === false){
                    marker = new google.maps.Marker({
                        position: clickedLocation,
                        map: new_map,
                        draggable: true
                    });
                    google.maps.event.addListener(marker, 'dragend', function(event){
                        markerLocation();
                    });
                } else{
                    marker.setPosition(clickedLocation);
                }
                markerLocation();
            });
        }
        function markerLocation(){
            let currentLocation = marker.getPosition();
            document.getElementById('lat').value = currentLocation.lat();
            document.getElementById('lng').value = currentLocation.lng();
        }
        google.maps.event.addDomListener(window, 'load', initMap);
    </script>
    <script>
        $("#licence_ar").change(function(){
            $('#licence_ar_preview').html("");
            var total_file=document.getElementById("licence_ar").files.length;
            for(var i=0;i<total_file;i++)
            {
                $('#licence_ar_preview').append("" +
                    "<iframe src='"+URL.createObjectURL(event.target.files[i])+"' style='width:100%; height:500px;'></iframe>");
            }
        });
        $("#licence_en").change(function(){
            $('#licence_en_preview').html("");
            var total_file=document.getElementById("licence_en").files.length;
            for(var i=0;i<total_file;i++)
            {
                $('#licence_en_preview').append("" +
                    "<iframe src='"+URL.createObjectURL(event.target.files[i])+"' style='width:100%; height:500px;'></iframe>");
            }
        });
    </script>
@stop

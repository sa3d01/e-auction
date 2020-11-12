@extends('dashboard.master.base')
@section('title',$title)
@section('style')
    <link rel="stylesheet" href="{{asset('panel/dropify/dist/css/dropify.min.css')}}">
    <style>
        #map{
            position: absolute !important;
            height: 100% !important;
            width: 100% !important;
        }
        .map-div{
            height: 400px;
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
                            <form class="formValidate" method="POST" action="{{ route($action) }}"
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
                                                @if($value=='note')
                                                    <div class="col-sm-12">
                                                        <div class="form-group" id="{{$value}}">
                                                            <label> {{$key}} </label>
                                                            <textarea name="{{$value}}" class="form-control" cols="80" rows="5"></textarea>
                                                        </div>
                                                    </div>
                                                @elseif(strpos($value, 'price'))
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
                                                @elseif($value=='role')
                                                    <div class="col-sm-12" id="roles">
                                                        <div class="form-group">
                                                            <label for=""> الدور </label>
                                                            <select id="role_id" name="role_id" class="form-control">
                                                                @foreach(\Spatie\Permission\Models\Role::all() as $role)
                                                                    <option value="{{$role->id}}">
                                                                        {{$role->blank}}
                                                                    </option>
                                                                @endforeach
                                                            </select>
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
                                            @if(isset($password))
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for=""> كلمة المرور</label><input name="password" class="form-control" data-minlength="6" placeholder="كلمة المرور" type="password">
                                                        <div class="help-block form-text text-muted form-control-feedback">
                                                            يجب أﻻ يقل عن 6 خانات على الأقل
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if(isset($image))
                                                <div class="col-sm-12">
                                                    <div class="white-box">
                                                        <label for="input-file-now-custom-1">الصورة</label>
                                                        <input name="image" type="file" id="input-file-now-custom-1 image" class="dropify" data-default-file="{{asset('media/images/logo.png')}}"/>
                                                    </div>
                                                </div>
                                            @endif
                                            @if(isset($pdf))
                                                <div class="col-sm-12">
                                                    <label for="pdf">الملف</label>
                                                    <div class="wrapper">
                                                        <div class="file-upload">
                                                            <input id="pdf" name="file" type="file" accept="application/pdf"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br/>
                                                <div class="col-sm-12 form-group" id="pdf_preview"></div>
                                            @endif
                                            @if(isset($video))
                                                <div class="col-sm-12">
                                                    <label for="file">الفديو</label>
                                                    <div class="wrapper">
                                                        <div class="file-upload">
                                                            <input id="pdf" name="file" type="file"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br/>
                                                <div class="col-sm-12 form-group" id="pdf_preview" accept="video/mp4,video/x-m4v,video/*"></div>
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
                                            @if(isset($multi_select))
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for=""> {{$multi_select['title']}}</label>
                                                        <select name="{{$multi_select['input_name']}}[]" class="form-control select2" multiple="true">
                                                            @foreach($multi_select['rows'] as $multi_select_row)
                                                                <option value="{{$multi_select_row->id}}" @if($loop->first) selected="true" @endif>
                                                                    {{$multi_select_row->nameForSelect()}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            @endif
                                            @if(isset($address))
                                                <div class="col-sm-12 map-div">
                                                    <div class="card-img" >
                                                        <label for="map">الموقع</label>
                                                        <div id="map" class="map"></div>
                                                        <input name="lat" type="hidden" id="lat">
                                                        <input name="lng" type="hidden" id="lng">
                                                        <input name="address" type="hidden" id="address">
                                                    </div>
                                                </div>
                                            @endif
                                            @if($type=='provider')
                                                <div class="form-group col-md-12">
                                                    <label class="control-label">الأعمال السابقة</label>
                                                    <table id="sampleTable" class=" table sample-list">
                                                        <thead>
                                                        <tr>
                                                            <td>عنوان العمل</td>
                                                            <td>صورة العمل</td>
                                                            <td>رابط العمل</td>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="kk">
                                                        <tr>
                                                            <td class="col-sm-4">
                                                                <input required type="text" name="title[]" class="form-control" />
                                                            </td>
                                                            <td class="col-sm-4">
                                                                <input required type="file" name="images[]"  class="form-control"/>
                                                            </td>
                                                            <td class="col-sm-3">
                                                                <input required type="text" name="link[]"  class="form-control"/>
                                                            </td>
                                                            <td class="col-sm-2"><a class="deleteSampleRow"></a></td>
                                                        </tr>
                                                        </tbody>
                                                        <tfoot>
                                                        <tr>
                                                            <td colspan="5" style="text-align: left;">
                                                                <input type="button" class="btn btn-lg btn-block " id="addSampleRow" value="عمل آخر" />
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                        </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            @endif
                                            @if(isset($permissions))
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for=""> الصلاحيات</label>
                                                        <select name="permissions[]" class="form-control select2" multiple="true">
                                                            @foreach(\Spatie\Permission\Models\Permission::all() as $permission)
                                                                <option value="{{$permission->name}}" @if($loop->first) selected="true" @endif>
                                                                    {{$permission->blank}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </fieldset>
                                @endif
                                <div class="form-buttons-w">
                                    <button class="btn btn-primary create-submit" type="submit"> إضافة</button>
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
    <script type="text/javascript">
        $("#pdf").change(function(){
            $('#pdf_preview').html("");
            var total_file=document.getElementById("pdf").files.length;
            for(var i=0;i<total_file;i++)
            {
                $('#pdf_preview').append("" +
                    "<iframe src='"+URL.createObjectURL(event.target.files[i])+"' style='width:100%; height:500px;'></iframe>");
            }
        });
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
    <script>

        if($('#user_type_id').val() != 4){
            $('.map-div').attr('hidden','hidden').removeAttr('height');
        }
        $('#user_type_id').change(function (){
            let user_type_id = $('#user_type_id').val();
            if(user_type_id==4){
                $('.map-div').removeAttr('hidden').attr('height','400px !important');
                $('#map').css({
                    'position': "absolute !important",
                    'height': "100% !important",
                    'width': "100% !important"
                });
            }else {
                $('.map-div').attr('hidden','hidden').removeAttr('height');
            }
        });
    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBKhmEeCCFWkzxpDjA7QKjDu4zdLLoqYVw&language=ar&&callback=initMap" type="text/javascript">
    </script>
    <script type="text/javascript">
        let map;
        let marker = false;
        function initMap() {
            let centerOfMap = new google.maps.LatLng(24.665658,46.7440368);
            let options = {
                center: centerOfMap,
                zoom: 14
            };
            map = new google.maps.Map(document.getElementById('map'), options);
            google.maps.event.addListener(map, 'click', function(event) {
                let clickedLocation = event.latLng;
                if(marker === false){
                    marker = new google.maps.Marker({
                        position: clickedLocation,
                        map: map,
                        draggable: true //make it draggable
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

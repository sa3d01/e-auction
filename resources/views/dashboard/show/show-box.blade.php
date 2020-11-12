<div class="col-sm-7">
    <div class="element-wrapper">
        <div class="element-box">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </div>
            @endif
                @if(isset($edit_fields))
                    <div class="element-info">
                        <div class="element-info-with-icon">
                            <div class="element-info-icon">
                                <div class="os-icon os-icon-wallet-loaded"></div>
                            </div>
                            <div class="element-info-text">
                                <h5 class="element-inner-header">
                                     البيانات
                                </h5>
                                @if(isset($edit_alert))
                                    <div class="element-inner-desc">
                                        {{$edit_alert}}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <fieldset class="form-group">
                        <div class="row">
                            @foreach($edit_fields as $key=>$value)
                                @if(isset($languages) && $languages==true)
                                    @foreach($settings->languages as $lang)
                                        @if($value=='note')
                                            <div class="col-sm-12">
                                                <div class="form-group" id="{{$value}}.{{$lang}}">
                                                    <label> {{$key}} </label>
                                                    <textarea disabled name="{{$value}}.{{$lang}}" class="form-control" cols="80" rows="10" id="ckeditor1">
                                                        {{$row->$value[$lang]}}
                                                    </textarea>
                                                </div>
                                            </div>
                                        @elseif(strpos($value, 'price'))
                                            <div class="col-sm-12">
                                                <div class="form-group" id="{{$value}}">
                                                    <label for=""> {{$key}}</label>
                                                    <input disabled name="{{$value}}" class="form-control" value="{{$row->$value}}" type="number" min="1">
                                                    <div class="help-block form-text with-errors form-control-feedback"></div>
                                                </div>
                                            </div>
                                        @elseif($value=='start_date' || $value=='end_date')
                                            <div class="col-sm-12" id="{{$value}}">
                                                <div class="form-group row">
                                                    <label for="{{$value}}" class="col-2 col-form-label">{{$key}}</label>
                                                    <p>{{$row->showTimeStampDate($row->$value)}}</p>
                                                </div>
                                            </div>
                                        @elseif($value=='images')
                                            <div class="col-sm-12" id="{{$value}}">
                                                <div class="form-group row hidden">
                                                    <label for="{{$value}}" class="col-2 col-form-label">{{$key}}</label>
                                                    <input hidden disabled class="upload form-control" id="uploadFile" type="file" data-images="{{$row->imagesArray()}}" accept="image/*" name="images[]" multiple />
                                                </div>
                                            </div>
                                            <br/>
                                            <div class="form-group" id="image_preview"></div>
                                        @else
                                            <div class="col-sm-12">
                                                <div class="form-group" id="{{$value}}.{{$lang}}">
                                                    <label for=""> {{$key}}</label>
                                                    <input disabled name="{{$value}}.{{$lang}}" class="form-control" value="{{$row->$value[$lang]}}" type="text">
                                                    <div class="help-block form-text with-errors form-control-feedback"></div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    @if($value=='note')
                                        <div class="col-sm-12">
                                            <div class="form-group" id="{{$value}}">
                                                <label> {{$key}} </label>
                                                <textarea disabled name="{{$value}}" class="form-control" cols="80" rows="10">
                                                        {{$row->$value}}
                                                    </textarea>
                                            </div>
                                        </div>
                                    @elseif(strpos($value, 'price'))
                                        <div class="col-sm-12">
                                            <div class="form-group" id="{{$value}}">
                                                <label for=""> {{$key}}</label>
                                                <input disabled name="{{$value}}" value="{{$row->$value}}" class="form-control" type="number" min="1">
                                                <div class="help-block form-text with-errors form-control-feedback"></div>
                                            </div>
                                        </div>
                                    @elseif($value== 'created_at')
                                        <div class="col-sm-12">
                                            <div class="form-group" id="{{$value}}">
                                                <label for=""> {{$key}}</label>
                                                <input disabled name="{{$value}}" value="{!! $row->published_at() !!}" class="form-control" type="text">
                                            </div>
                                        </div>
                                    @elseif($value=='start_date' || $value=='end_date')
                                        <div class="col-sm-12" id="{{$value}}">
                                            <div class="form-group row">
                                                <label for="{{$value}}" class="col-2 col-form-label">{{$key}}</label>
                                                <p>{{$row->showTimeStampDate($row->$value)}}</p>
                                            </div>
                                        </div>
                                    @elseif($value=='images')
                                        <div class="col-sm-12" id="{{$value}}">
                                            <div class="form-group row">
                                                <label for="{{$value}}" class="col-2 col-form-label">{{$key}}</label>
                                                <input class="upload form-control" id="uploadFile" type="file" data-images="{{$row->imagesArray()}}" accept="image/*" name="images[]" multiple />
                                            </div>
                                        </div>
                                        <br/>
                                        <div class="form-group" id="image_preview"></div>
                                    @else
                                        <div class="col-sm-12">
                                            <div class="form-group" id="{{$value}}">
                                                <label for=""> {{$key}}</label><input disabled name="{{$value}}" class="form-control" value="{{$row->$value}}" type="text">
                                                <div class="help-block form-text with-errors form-control-feedback"></div>
                                            </div>
                                        </div>
                                    @endif
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
                                @if(isset($image))
                                    <div class="col-sm-12">
                                        <div class="white-box">
                                            <label for="input-file-now-custom-1">الصورة</label>
                                            <input disabled name="image" type="file" id="input-file-now-custom-1" class="dropify" data-default-file="{{$row->image}}"/>
                                        </div>
                                    </div>
                                @endif
                                @if(isset($pdf) && $row->pdf!=null)
                                    <div class="col-sm-12">
                                        <label for="pdf">تقرير الفحص</label>
                                        <div>
                                            <iframe id="iframe" src="https://top-auction.com/media/files/{{$row->pdf}}" style="width:100%; height:500px;" frameborder="0"></iframe>
                                        </div>
                                    </div>
                                    <br>
                                    <br>
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
                                @if(isset($attachments))
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
                                @endif
                        </div>
                    </fieldset>
                @endif
        </div>
    </div>
</div>


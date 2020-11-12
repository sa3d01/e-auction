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
                {!! Form::model($row, ['method'=>'POST','name'=>'update', 'files'=>true, 'route'=>[$action, $row->id], 'id' => 'formValidate']) !!}
                {!! Form::hidden('id', $row->id) !!}
                {!! Form::hidden('edit_by', \Illuminate\Support\Facades\Auth::user()->id) !!}
                <div class="element-info">
                    <div class="element-info-with-icon">
                        <div class="element-info-icon">
                            <div class="os-icon os-icon-wallet-loaded"></div>
                        </div>
                        <div class="element-info-text">
                            <h5 class="element-inner-header">
                                تعديل البيانات
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
                                                <textarea name="{{$value}}.{{$lang}}" class="form-control" cols="80" rows="10" id="ckeditor1">
                                                        {{$row->$value[$lang]}}
                                                    </textarea>
                                            </div>
                                        </div>
                                    @elseif(strpos($value, 'price'))
                                        <div class="col-sm-12">
                                            <div class="form-group" id="{{$value}}">
                                                <label for=""> {{$key}}</label>
                                                <input name="{{$value}}" class="form-control" value="{{$row->$value}}" type="number" min="1">
                                                <div class="help-block form-text with-errors form-control-feedback"></div>
                                            </div>
                                        </div>
                                    @elseif($value=='start_date' || $value=='end_date')
                                        <div class="col-sm-12" id="{{$value}}">
                                            <div class="form-group row">
                                                <label for="{{$value}}" class="col-2 col-form-label">{{$key}}</label>
                                                <input name="{{$value}}" value="{{$row->getDateLocalFormat($value)}}" class="form-control" type="datetime-local" id="{{$value}}">
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
                                            <div class="form-group" id="{{$value}}.{{$lang}}">
                                                <label for=""> {{$key}}</label><input name="{{$value}}.{{$lang}}" class="form-control" value="{{$row->$value[$lang]}}" type="text">
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
                                            <textarea name="{{$value}}" class="form-control" cols="80" rows="10">
                                                        {{$row->$value}}
                                                    </textarea>
                                        </div>
                                    </div>
                                @elseif(strpos($value, 'price'))
                                    <div class="col-sm-12">
                                        <div class="form-group" id="{{$value}}">
                                            <label for=""> {{$key}}</label>
                                            <input name="{{$value}}" value="{{$row->$value}}" class="form-control" type="number" min="1">
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                    </div>
                                @elseif($value=='start_date' || $value=='end_date')
                                    <div class="col-sm-12" id="{{$value}}">
                                        <div class="form-group row">
                                            <label for="{{$value}}" class="col-2 col-form-label">{{$key}}</label>
                                            {{--                                                2017-06-01T08:30--}}
                                            <input name="{{$value}}"  value="{{$row->getDateLocalFormat($value)}}" class="form-control" type="datetime-local" id="{{$value}}">
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
                                @elseif($value=='role')
                                    <div class="col-sm-12" id="roles">
                                        <div class="form-group">
                                            <label for=""> الدور </label>
                                            <select id="role_id" name="role_id" class="form-control">
                                                @foreach(\Spatie\Permission\Models\Role::all() as $role)
                                                    <option @if($row->hasRole($role->name)) selected @endif value="{{$role->id}}">
                                                        {{$role->blank}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-sm-12">
                                        <div class="form-group" id="{{$value}}">
                                            <label for=""> {{$key}}</label><input name="{{$value}}" class="form-control" value="{{$row->$value}}" type="text">
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endforeach
                        @if(isset($password))
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for=""> كلمة المرور</label><input name="password" class="form-control" data-minlength="6" placeholder="كلمة المرور" type="password">
                                    <div class="help-block form-text text-muted form-control-feedback">
                                        يجب أﻻ يقل عن 6 خانات على الأقل
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if(isset($order_by))
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">الترتيب الرقمى</label><input name="order_by" class="form-control" min="1" value="{{$row->order_by}}" placeholder="الترتيب الرقمى" type="number">
                                </div>
                            </div>
                        @endif
                        @if(isset($image))
                            <div class="col-sm-6">
                                <div class="white-box">
                                    <label for="input-file-now-custom-1">الصورة</label>
                                    <input name="image" type="file" id="input-file-now-custom-1" class="dropify" data-default-file="{{$row->image}}"/>
                                </div>
                            </div>
                        @endif
                        @if(isset($pdf))
                            <div class="col-sm-12">
                                <label for="pdf">تقرير الفحص</label>
                                <div>
                                    <input id="pdf" name="pdf" type="file" accept="application/pdf"/>
                                </div>
                            </div>
                            <br/>
                            <div class="col-sm-12 form-group" id="pdf_preview">
                                @if($row->pdf!=null)
                                    <iframe id="iframe" src="https://top-auction.com/media/files/{{$row->pdf}}" style="width:100%; height:500px;" frameborder="0"></iframe>
                                @endif
                            </div>
                            <br>
                        @endif
                        @if(isset($address))
                            <div class="col-sm-12">
                                <div class="card-img" style="height: 400px">
                                    <label for="sale_map">الموقع</label>
                                    <input id="pac-input" name="address_search"
                                           class="controls" value="{{old('address_search')}}"
                                           type="text"
                                           style="z-index: 1; position: absolute;  top: 10px !important;
		                                            left: 197px; height: 40px;   width: 63%;   border: none;  box-shadow: rgba(0, 0, 0, 0.3) 0px 1px 4px -1px; padding: 0 1em;   color: rgb(86, 86, 86);
		                                            font-family: Roboto, Arial, sans-serif;   user-select: none;  font-size: 18px;   margin: 10px;"  placeholder="بحث"  >
                                    <div id="sale_map" data-lat="{{$row->address['lat']}}" data-lng="{{$row->address['lng']}}" class="sale_map"></div>
                                    <input name="lat" type="hidden" id="lat">
                                    <input name="lng" type="hidden" id="lng">
                                    <input name="address" type="hidden" id="address">
                                </div>
                            </div>
                        @endif
                        @if(isset($permissions))
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for=""> الصلاحيات</label>
                                    <select name="permissions[]" class="form-control select2" multiple="true">
                                        @foreach(\Spatie\Permission\Models\Permission::all() as $permission)
                                            <option value="{{$permission->name}}" @if($row->hasPermissionTo($permission->id)) selected="true" @endif>
                                                {{$permission->blank}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                    </div>
                </fieldset>
{{--                @can('edit-'.$type.'s')--}}
                    <div class="form-buttons-w">
                        <button class="btn btn-primary edit-submit" type="submit"> تعديل</button>
                    </div>
{{--                @endcan--}}
                {!! Form::close() !!}
            @endif
        </div>
    </div>
</div>


@extends('dashboard.master.base')
@section('title','الاعدادات العامة')
@section('style')
    <link rel="stylesheet" href="{{asset('panel/dropify/dist/css/dropify.min.css')}}">
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
                                                <i class="os-icon os-icon-calendar-time"></i>مدة الزيادة على المزاد الموشك على الانتهاء
                                            </label>
                                            <input name="auction_increasing_period" value="{{$row->auction_increasing_period}}" class="form-control" type="number" min="0">
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
                                            <label> عن التطبيق باللغة العربية</label>
                                            <textarea name="about_ar" class="form-control" cols="80" rows="5">{{$row->about['ar']}}</textarea>
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                        <div class="form-group">
                                            <i class="os-icon os-icon-file-text"></i>
                                            <label> عن التطبيق باللغة الانجليزية</label>
                                            <textarea name="about_en" class="form-control" cols="80" rows="5">{{$row->about['en']}}</textarea>
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>

                                        <div class="form-group">
                                            <i class="os-icon os-icon-file-text"></i>
                                            <label>شروط الاستخدام باللغة العربية </label>
                                            <textarea name="licence_ar" class="form-control" cols="80" rows="5">{{$row->licence['ar']}}</textarea>
                                            <div class="help-block form-text with-errors form-control-feedback"></div>
                                        </div>
                                        <div class="form-group">
                                            <i class="os-icon os-icon-file-text"></i>
                                            <label>شروط الاستخدام باللغة الانجليزية </label>
                                            <textarea name="licence_en" class="form-control" cols="80" rows="5">{{$row->licence['en']}}</textarea>
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
@stop

@extends('dashboard.master.base')
@section('title',$title)
@section('content')
    <div class="content-i">
        <div class="content-box">
            <div class="element-wrapper">
                <div class="element-box">
                    <h5 class="form-header">
                        {{$title}}
                    </h5>
                    <div class="element-box">
                        {!! Form::open(['method'=>'post', 'files'=>true, 'enctype' => 'multipart/form-data', 'route'=>[$action], 'class' => 'formValidate']) !!}
                        {!! Form::hidden('add_by', \Illuminate\Support\Facades\Auth::user()->id) !!}
                        {!! Form::hidden('admin_notify_type', $admin_notify_type) !!}
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
                                                    <textarea name="{{$value}}_ar" class="form-control" cols="80" rows="5"></textarea>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-sm-12">
                                                <div class="form-group" id="{{$value}}">
                                                    <label for=""> {{$key}}</label>
                                                    <input name="{{$value}}_ar" class="form-control" type="text">
                                                    <div class="help-block form-text with-errors form-control-feedback"></div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </fieldset>
                        @endif
                        <div class="form-buttons-w">
                            <button class="btn btn-primary create-submit" type="submit"> إرسال</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                    <div  class="table-responsive">
                        <table id="datatable" width="100%" class="table table-striped table-lightfont">
                            <thead>
                                <tr>
                                    <th hidden></th>
                                    @foreach($index_fields as $key=>$value)
                                        <th>{{$key}}</th>
                                    @endforeach
                                    <th>عدد مستقبلي الاشعار</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th hidden></th>
                                    @foreach($index_fields as $key=>$value)
                                        <th>{{$key}}</th>
                                    @endforeach
                                    <th>عدد مستقبلي الاشعار</th>
                                </tr>
                            </tfoot>
                            <tbody>
                            @foreach($rows as $row)
                                <tr>
                                    <td hidden>{{$row->id}}</td>
                                @foreach($index_fields as $key=>$value)
                                    @if($value=='created_at')
                                        <td>{{$row->published_at()}}</td>
                                    @else
                                        <td>{{$row->$value['ar']}}</td>
                                    @endif
                                @endforeach
                                    <td>{{count((array)$row->receivers)}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(function() {
            var Table = $('#datatable').DataTable({
                "order": [[ 1, "asc" ]],
                "oLanguage": {
                    "sEmptyTable":     "ليست هناك بيانات متاحة في الجدول",
                    "sLoadingRecords": "جارٍ التحميل...",
                    "sProcessing":   "جارٍ التحميل...",
                    "sLengthMenu":   "أظهر _MENU_ مدخلات",
                    "sZeroRecords":  "لم يعثر على أية سجلات",
                    "sInfo":         "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
                    "sInfoEmpty":    "يعرض 0 إلى 0 من أصل 0 سجل",
                    "sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
                    "sInfoPostFix":  "",
                    "sSearch":       "ابحث:",
                    "sUrl":          "",
                    "oPaginate": {
                        "sFirst":    "الأول",
                        "sPrevious": "السابق",
                        "sNext":     "التالي",
                        "sLast":     "الأخير"
                    },
                    "oAria": {
                        "sSortAscending":  ": تفعيل لترتيب العمود تصاعدياً",
                        "sSortDescending": ": تفعيل لترتيب العمود تنازلياً"
                    }
                },
            });
            var rows=Table.rows().data();
            $(
                ".filters-groups .date-picker-max, .filters-groups .date-picker-min"
            ).change(function() {
                var val = parseInt((new Date(this.value).getTime() / 1000).toFixed(0));
                var op = "min";
                if ($(this).hasClass("date-picker-max")) {
                    op = "max";
                }
                Table.rows().every(function() {
                    var row_id=this.data()[0];
                    var date = Date.parse(this.data()[1])/1000;
                    if (date) {
                        if (op === "min") {
                            if (date < val) {
                                $('#'+row_id).hide();
                            } else {
                                $('#'+row_id).show();
                            }
                        } else {
                            if (date > val) {
                                $('#'+row_id).hide();
                            } else {
                                $('#'+row_id).show();
                            }
                        }
                    }
                });
                Table.draw();
            });
        });
    </script>
@endsection

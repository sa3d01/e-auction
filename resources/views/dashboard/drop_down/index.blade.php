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
                    <div class="form-buttons-w">
                        <a href="{{route('admin.drop_down.new',['class'=>$type])}}" class="btn btn-primary create-submit" ><label>+</label> إضافة</a>
                    </div>
                    <div  class="table-responsive">
                        <table id="datatable" width="100%" class="table table-striped table-lightfont">
                            <thead>
                            <tr>
                                <th hidden></th>
                                @foreach($index_fields as $key=>$value)
                                    <th>{{$key}}</th>
                                @endforeach
                                <th>الحالة</th>
                                @if($image==true)
                                    <th>الصورة</th>
                                @endif
                                <th>المزيد</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th hidden></th>
                                @foreach($index_fields as $key=>$value)
                                    <th>{{$key}}</th>
                                @endforeach
                                <th>الحالة</th>
                                @if($image==true)
                                    <th>الصورة</th>
                                @endif
                                <th>المزيد</th>
                            </tr>
                            </tfoot>
                            <tbody>
                            @foreach($rows as $row)
                                <tr>
                                    <td hidden>{{$row->id}}</td>
                                    @foreach($index_fields as $key=>$value)
                                        @if(substr($value, "-3")=='_id')
                                            <td>{{$row->parent->name['ar']}}</td>
                                        @else
                                            @if(is_array($row->$value))
                                                <td>{{$row->$value['ar']}}</td>
                                            @else
                                                <td>{{$row->$value}}</td>
                                            @endif
                                        @endif
                                    @endforeach
                                    <td>
                                        {!!$row->getStatusIcon()!!}
                                    </td>
                                    @if($image==true)
                                        <td data-toggle="modal" data-target="#imgModal{{$row->id}}">
                                            <img width="50px" height="50px" class="img_preview" src="{{ $row->image}}">
                                        </td>
                                        <div id="imgModal{{$row->id}}" class="modal fade" role="img">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-body">
                                                        <img data-toggle="modal" data-target="#imgModal{{$row->id}}" class="img-preview" src="{{ $row->image}}" style="max-height: 500px">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <td>
{{--                                        <form class="delete" data-id="{{$row->id}}" method="POST" action="{{ route('admin.'.$type.'.destroy',[$row->id]) }}">--}}
{{--                                            @csrf--}}
{{--                                            {{ method_field('DELETE') }}--}}
{{--                                            <input type="hidden" value="{{$row->id}}">--}}
{{--                                            <button type="button " class="btn p-0 no-bg">--}}
{{--                                                <i class="fa fa-trash text-danger"></i>--}}
{{--                                            </button>--}}
{{--                                        </form>--}}
                                        <a href="{{route('admin.drop_down.show',$row->id)}}"><i class="os-icon os-icon-eye"></i></a>
                                    </td>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script>
        $(document).on('click', '.delete', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            Swal.fire({
                title: "هل انت متأكد من الحذف ؟",
                text: "لن تستطيع استعادة هذا العنصر مرة أخرى!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: 'btn-danger',
                confirmButtonText: 'نعم , قم بالحذف!',
                cancelButtonText: 'ﻻ , الغى عملية الحذف!',
                closeOnConfirm: false,
                closeOnCancel: false,
                preConfirm: () => {
                    $("form[data-id='" + id + "']").submit();
                },
                allowOutsideClick: () => !Swal.isLoading()
            })
        });
    </script>
@endsection

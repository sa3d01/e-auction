@extends('dashboard.master.base')
@section('title',$title)
@section('style')
    <style>
        .image-upload > input {
            visibility:hidden;
            width:0;
            height:0
        }
    </style>
@endsection
@section('content')
    <div class="content-i">
        <div class="content-box">
            <div class="element-wrapper">
                <div class="element-box">
                    <h5 class="form-header">
                        {{$title}}
                    </h5>
                    @if($type=='role'|| $type=='admin')
{{--                        @can('add-'.$type.'s')--}}
                        <div class="form-buttons-w">
                            <a href="{{route('admin.'.$type.'.create')}}" class="btn btn-primary create-submit" ><label>+</label> إضافة</a>
                        </div>
{{--                        @endcan--}}
                    @endif
                    <div  class="table-responsive">
                        <table id="datatable" width="100%" class="table table-striped table-lightfont">
                            <thead>
                                <tr>
                                    <th hidden></th>
                                    @foreach($index_fields as $key=>$value)
                                        <th>{{$key}}</th>
                                    @endforeach
                                    @if(isset($selects))
                                        @foreach($selects as $select)
                                            <th>{{$select['title']}}</th>
                                        @endforeach
                                    @endif
                                    @if(isset($status))
                                        <th>الحالة</th>
                                    @endif
                                    @if(isset($image))
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
                                    @if(isset($selects))
                                        @foreach($selects as $select)
                                            <th>{{$select['title']}}</th>
                                        @endforeach
                                    @endif
                                    @if(isset($status))
                                        <th>الحالة</th>
                                    @endif
                                    @if(isset($image))
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
                                    @if($value=='created_at')
                                        <td>{{$row->published_at()}}</td>
                                    @elseif($value=='start_date' || $value=='end_date')
                                        <td>{{$row->showTimeStampDate($row->$value)}}</td>
                                    @elseif($value=='role')
                                        @if($row->hasRole(\Spatie\Permission\Models\Role::all()))
                                            <td>{{$row->getRoleArabicName()}}</td>
                                        @else
                                            ﻻ يمتلك أي صﻻحيات حتى الآن
                                        @endif
                                    @elseif($type=='role' && $value=='users_count')
                                        <td>{{$row->users()->count()}}</td>
                                    @else
                                        <td>{{$row->$value}}</td>
                                    @endif
                                @endforeach
                                    @if(isset($selects))
                                        @foreach($selects as $select)
                                            @php($related_model=$select['name'])
                                            <td>{{$row->$related_model->nameForSelect()}}</td>
                                        @endforeach
                                    @endif
                                    @if(isset($status))
                                        <td>
                                        {!!$row->getStatusIcon()!!}
                                        </td>
                                    @endif
                                    @if(isset($image))
                                        <td><img width="50px" height="50px" src="{{$row->image}}"></td>
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
                                        {!! $row->activate() !!}
                                        <a href="{{route('admin.'.$type.'.show',$row->id)}}"><i class="os-icon os-icon-grid-10"></i></a>
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

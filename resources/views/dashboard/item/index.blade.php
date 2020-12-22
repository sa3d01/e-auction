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
                    @if($status=='accepted')
                        <div class="element-inner-desc centered-header" style="color: red">
                            السلع التى يمكن اضافتها لمزاد هى السلع التى تم تحديد
                            سعر ابتدائى
                            لها للمزايدة واضافة
                            تقرير فحص واحد
                            على الأقل .. بجانب
                            تسديد رسوم الإضافة
                            من قبل صاحب السلعة
                        </div>
                        <br>
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
                                @if(isset($image))
                                    <th>الصورة</th>
                                @endif
                                @if($status=='accepted')
                                    <th>السعر المطروح للمزايدة</th>
                                    <th>تقارير الفحص</th>
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
                                @if(isset($image))
                                    <th>الصورة</th>
                                @endif
                                @if($status=='accepted')
                                    <th>السعر المطروح للمزايدة</th>
                                    <th>تقارير الفحص</th>
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
                                    @if(isset($image))
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
                                    @if($status=='accepted')
                                        <td>{!! $row->auctionPriceLabel() !!}</td>
                                        <td>
                                            {!! $row->reportLabel() !!}
                                        </td>
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
                                        <a href="{{route('admin.item.show',$row->id)}}"><i class="os-icon os-icon-eye"></i></a>
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
        $(document).on('click', '.auction_price', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            Swal.fire({
                title: 'من فضلك اذكر سعر المزايدة الابتدائى',
                input: 'number',
                showCancelButton: true,
                confirmButtonText: 'إتمام',
                cancelButtonText: 'الغاء',
                showLoaderOnConfirm: true,
                preConfirm: (auction_price) => {
                    $.ajax({
                        url: $(this).data('href'),
                        type:'GET',
                        data: {auction_price}
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then(() => {
                location.href = "/admin/item/status/accepted";
            })
        });
    </script>
@endsection

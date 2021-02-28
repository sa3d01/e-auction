@extends('dashboard.master.base')
@section('title',$title)
@section('content')
    <div class="content-i">
        <div class="content-box">
            <div class="row">
                <div class="col-sm-5">
                    <div class="user-profile compact">
                    @if(array_key_exists('item_id',$row->more_details))
                        @php($item=\App\Item::find($row->more_details['item_id']))
                    @endif
                        @if(array_key_exists('item_id',$row->more_details))
                            <div class="up-head-w" style="background-image:url({{$item->images[0]}}) ">
                        @else
                            <div class="up-head-w" style="background-image:url({{$row->user->image}}) ">
                        @endif
                            <div class="up-main-info">
                                <h2 class="up-header">
                                   {{$row->user->name}}
                                </h2>
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
                                @if($row->status===0)
                                <div class="col-md-6">
                                    <form class="POST" name="reject" enctype="multipart/form-data" method="POST" style="width: 125px" data-href="{{route('admin.transfer.reject',$row->id)}}" action="{{route('admin.transfer.reject',$row->id)}}" data-type="{{$type}}" id="reject">
                                        @csrf
                                        {{ method_field('PUT') }}
                                    <button type="button"
                                            class="btn btn-danger btn-custom waves-effect waves-light">
                                        <i
                                            class="fa fa-trash"></i>رفض
                                    </button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form class="POST" name="accept" enctype="multipart/form-data" method="POST" style="width: 125px" data-href="{{route('admin.transfer.accept',$row->id)}}" action="{{route('admin.transfer.accept',$row->id)}}" data-type="{{$type}}" id="accept">
                                        @csrf
                                        {{ method_field('PUT') }}
                                        <button type="button"
                                                class="btn btn-success btn-custom waves-effect waves-light">
                                            <i
                                                class="fa fa-opencart"></i>قبول
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
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
                                    @if($type=='transfer')
                                        <div class="col-sm-12">
                                            <div class="white-box">
                                                <label for="input-file-now-custom-1">الصورة</label>
                                                <div>
                                                    <iframe id="iframe" src="{{asset('media/images/transfer/'.$row->more_details['image'])}}" style="width:100%; height:500px;" frameborder="0"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="">صاحب التحويل</label>
                                                {{--                                                <a href="{{route('admin.user.show',$row->user_id)}}"><br>--}}
                                                <span>{{$row->user->name}}</span>
                                                {{--                                                </a>--}}
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="">السلعة</label>
                                                <a href="{{route('admin.item.show',$row->more_details['item_id'])}}"><br>
                                                    <span>{{$row->more_details['item_id']}}</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="">المبلغ المحول</label><br>
                                                <span class="fa-sort-amount-asc">{{$row->money}}</span>
                                                ريال
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="">صاحب الطلب</label>
                                                {{--                                                <a href="{{route('admin.user.show',$row->user_id)}}"><br>--}}
                                                <span>{{$row->user->name}}</span>
                                                {{--                                                </a>--}}
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="">المبلغ </label><br>
                                                <span class="fa-sort-amount-asc">{{$row->money}}</span>
                                                ريال
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script>
        $(document).on('click', '#reject', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'من فضلك اذكر سبب الرفض',
                input: 'text',
                showCancelButton: true,
                confirmButtonText: 'رفض',
                cancelButtonText: 'الغاء',
                showLoaderOnConfirm: true,
                preConfirm: (reject_reason) => {
                    $.ajax({
                        url: $(this).data('href'),
                        type:'GET',
                        data: {reject_reason}
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then(() => {
                location.href = '/admin/transfer';
            })
        });
        $(document).on('click', '#accept', function (e) {
            e.preventDefault();
            Swal.fire({
                title: "هل انت متأكد من القبول ؟",
                text: "تأكد من اجابتك قبل التأكيد!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: 'btn-danger',
                confirmButtonText: 'نعم , قم بتأكيد التحويل!',
                cancelButtonText: 'ﻻ , الغى العملية !',
                closeOnConfirm: false,
                closeOnCancel: false,
                preConfirm: () => {
                    $.ajax({
                        url: $(this).data('href'),
                        type:'GET',
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then(() => {
                window.location.href = '/admin/transfer';
            })
        });
    </script>

    <script>
        $(document).ready(function() {
            $("#pdf").change(function(){
                $('#pdf_preview').html("");
                var total_file=document.getElementById("pdf").files.length;
                for(var i=0;i<total_file;i++)
                {
                    $('#pdf_preview').append("" +
                        "<iframe src='"+URL.createObjectURL(event.target.files[i])+"' style='width:100%; height:500px;'></iframe>");
                }
            });
        });
    </script>
@endsection

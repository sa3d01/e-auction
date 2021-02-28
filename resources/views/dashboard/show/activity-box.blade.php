<div class="element-box">
    <h6 class="element-header">
        اخر النشاطات
    </h6>
    <div class="timed-activities compact" style="overflow:scroll;max-height: 500px">

        @if(isset($sale_users))
            @foreach($sale_users as $sale_user)
                @php
                    $user_route=route('admin.user.show',$sale_user->user_id);
                    $user_name=\App\User::whereId($sale_user->user_id)->value('name');
                    $sale_route=route('admin.sale.show',$sale_user->sale_id);
                    $sale_name=$sale_user->sale->title['ar'];
                    $user_href="<a href='".$user_route."'>".$user_name."</a>";
                    $sale_href="<a href='".$sale_route."'>".$sale_name."</a>";
                @endphp
                <div class="timed-activity">
                    <div class="ta-date">
                        <span>{{$sale_user->ArabicDate($sale_user->created_at)}}</span>
                    </div>
                    <div class="ta-record-w">
                        <div class="ta-record">
                            <div class="ta-timestamp">
                                <strong>{{\Carbon\Carbon::parse($sale_user->created_at)->format('H:i A')}}</strong>
                            </div>
                            <div class="ta-activity">
                                @if($sale_user->charge_price==0)
                                    {!!$sale_href!!} قام بدفع تأمين مزاد{!!$user_href !!}
                                @elseif($sale_user->type=='automatic')
                                    {!! $sale_href. 'قام بمزايدة تلقائية بمبلغ ' .$sale_user->charge_price. ' ريال على مزاد ' . $user_href !!}
                                @else
                                    {!! $sale_href. 'قام بمزايدة يدوية بمبلغ ' .$sale_user->charge_price. ' ريال على مزاد ' . $user_href !!}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @elseif(isset($row->more_details['history']))
            @foreach(array_reverse($row->more_details['history']) as $date=>$obj)
                <div class="timed-activity">
                    <div class="ta-date">
                        <span>{{$date}}</span>
                    </div>
                    <div class="ta-record-w">
                        @foreach($obj as $key=>$value)
                            <div class="ta-record">
                                <div class="ta-timestamp">
                                    <strong>{{\Carbon\Carbon::parse($value['time'])->format('H:i A')}}</strong>
                                </div>
                                <div class="ta-activity">
                                    @if($key=='block')
                                        <a href="{{route('admin.profile',$value['admin_id'])}}">{{\App\Admin::whereId($value['admin_id'])->value('name')}}</a>  تم الحظر بواسطة
                                    @else
                                        <a href="{{route('admin.profile',$value['admin_id'])}}">{{\App\Admin::whereId($value['admin_id'])->value('name')}}</a>  تم التفعيل بواسطة
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            <div class="ta-activity font-italic">
                ﻻ يوجد اى نشاطات بعد
            </div>
        @endif
    </div>
</div>





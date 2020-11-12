<div class="element-box">
    <h6 class="element-header">
        اخر النشاطات
    </h6>
    <div class="timed-activities compact">
        @if(isset($row->more_details['history']))
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

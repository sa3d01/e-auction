@if($type=='user')
    <div class="element-box">
        <h6 class="element-header">
            اخر النشاطات الخاصة باضافة السلع
        </h6>
        <div class="timed-activities compact" style="overflow:scroll;max-height: 500px">
            @if(\App\Item::where('user_id', $row->id)->count() > 0)
                @php
                    $user_items=\App\Item::where('user_id', $row->id)->latest()->get();
                @endphp
                @foreach($user_items as $user_item)
                    @php
                        $item_route=route('admin.item.show',$user_item->id);
                        $item_href="<a href='".$item_route."'>".$user_item->id."</a>";
                    @endphp
                    <div class="timed-activity">
                        <div class="ta-date">
                            <span>{{$user_item->ArabicDate($user_item->created_at)}}</span>
                        </div>
                        <div class="ta-record-w">
                            <div class="ta-record">
                                <div class="ta-timestamp">
                                    <strong>{{\Carbon\Carbon::parse($user_item->created_at)->format('H:i A')}}</strong>
                                </div>
                                <div class="ta-activity">
                                    {!!$item_href!!} قام بإضافة سلعة
                                </div>
                            </div>
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

    <div class="element-box">
        <h6 class="element-header">
            اخر النشاطات الخاصة بالمزايدات
        </h6>
        <div class="timed-activities compact" style="overflow:scroll;max-height: 500px">
            @if(\App\AuctionUser::where('user_id', $row->id)->count() > 0)
                @php
                    $user_auctions=\App\AuctionUser::where('user_id', $row->id)->latest()->get();
                @endphp
                @foreach($user_auctions as $user_auction)
                    @php
                        $auction_route=route('admin.item.show',$user_auction->item_id);
                        $auction_href="<a href='".$auction_route."'>".$user_auction->item_id."</a>";
                    @endphp
                    <div class="timed-activity">
                        <div class="ta-date">
                            <span>{{$user_auction->ArabicDate($user_auction->created_at)}}</span>
                        </div>
                        <div class="ta-record-w">
                            <div class="ta-record">
                                <div class="ta-timestamp">
                                    <strong>{{\Carbon\Carbon::parse($user_auction->created_at)->format('H:i A')}}</strong>
                                </div>
                                <div class="ta-activity">
                                    {!!$item_href!!} قام بإضافة مزايدة بقيمة{{$user_auction->charge_price}}  على سلعة
                                </div>
                            </div>
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

    <div class="element-box">
        <h6 class="element-header">
            اخر النشاطات الخاصة بالسلع التى قد فاز بها
        </h6>
        <div class="timed-activities compact" style="overflow:scroll;max-height: 500px">
            @php
                $paid_auction_items=\App\AuctionItem::where('more_details->status','paid')->orWhere('more_details->status','delivered')->get();
                $item_ids=[];
                foreach ($paid_auction_items as $paid_auction_item){
                    $winner=\App\AuctionUser::where(['auction_id'=>$paid_auction_item->auction_id,'item_id'=>$paid_auction_item->item_id])->latest()->value('user_id');
                    if ($winner==$row->id){
                        $item_ids[]=$paid_auction_item->item_id;
                    }
                }
                $paid_items=\App\Item::whereIn('id',$item_ids)->latest()->get();
            @endphp
            @if(count($item_ids) > 0)
                @foreach($paid_items as $paid_item)
                    @php
                        $paid_item_route=route('admin.item.show',$paid_item->id);
                        $paid_item_href="<a href='".$paid_item_route."'>".$paid_item->id."</a>";
                        $win=\App\AuctionUser::where(['user_id'=>$row->id,'item_id'=>$paid_item->id])->latest()->first();
                    @endphp
                    <div class="timed-activity">
                        <div class="ta-date">
                            <span>{{$win->ArabicDate($win->created_at)}}</span>
                        </div>
                        <div class="ta-record-w">
                            <div class="ta-record">
                                <div class="ta-timestamp">
                                    <strong>{{\Carbon\Carbon::parse($win->created_at)->format('H:i A')}}</strong>
                                </div>
                                <div class="ta-activity">
                                    {!!$paid_item_href!!} فاز بالسلعة
                                </div>
                            </div>
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

@else
@endif






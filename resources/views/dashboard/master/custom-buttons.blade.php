{{--<div class="floated-colors-btn first-floated-btn">--}}
{{--    <div class="os-toggler-w">--}}
{{--        <div class="os-toggler-i">--}}
{{--            <div class="os-toggler-pill"></div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <span>الليلى </span><span>الوضع</span>--}}
{{--</div>--}}
@if(isset($edit_fields))
    @if($type=='user' || $type=='provider')
            <div class="floated-chat-btn">
                <i class="os-icon os-icon-mail-07"></i><span>تواصل</span>
            </div>
            <div class="floated-chat-w">
                <div class="floated-chat-i">
                    <div class="chat-close">
                        <i class="os-icon os-icon-close"></i>
                    </div>
                    <div class="chat-head">
                        <div class="user-w with-status status-green">
                            <div class="user-avatar-w">
                                <div class="user-avatar">
                                    <img alt="" src="{{$row->image}}">
                                </div>
                            </div>
                            <div class="user-name">
                                <h6 class="user-title">
                                    {{$row->name}}
                                </h6>
                                <div class="user-role">
                                    {{$row->user_type->name}}
                                </div>
                            </div>
                        </div>
                    </div>
                    @php
                    $admin_messages=\App\Notification::where(['receiver_id'=>$row->id,'type'=>'admin'])->get();
                    @endphp
                    <div class="chat-messages">
                        @foreach($admin_messages as $note)
                        <div class="message self">
                            <div class="message-content">
                                {{$note->note}}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="chat-controls">
                        <input id="message-input" name="message-input" data-receiver="{{$row->id}}" class="message-input" placeholder="اكتب رسالتك هنا..." type="text">
                    </div>
                </div>
            </div>
    @endif
@endif


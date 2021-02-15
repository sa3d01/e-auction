<div class="menu-actions">
{{--    @can('view-contacts')--}}
    <!--------------------
    START - Contacts Link in secondary top menu
    -------------------->
{{--        <div class="messages-notifications os-dropdown-trigger os-dropdown-position-right">--}}
{{--            <i class="os-icon os-icon-message-circle"></i>--}}
{{--            <div class="new-messages-count">--}}
{{--                {{$new_contacts_count}}--}}
{{--            </div>--}}
{{--            @if($new_contacts_count > 0)--}}
{{--                <div class="os-dropdown light message-list">--}}
{{--                    <ul>--}}
{{--                        @foreach($new_contacts as $new_contact)--}}
{{--                            <li>--}}
{{--                                <a href="{{route('admin.contact.index')}}">--}}
{{--                                    <div class="user-avatar-w">--}}
{{--                                        <img alt="" src="{{$new_contact->user->image}}">--}}
{{--                                    </div>--}}
{{--                                    <div class="message-content">--}}
{{--                                        <h6 class="message-from">--}}
{{--                                            {{$new_contact->user->name}}--}}
{{--                                        </h6>--}}
{{--                                        <h6 class="message-title">--}}
{{--                                            {{$new_contact->message}}--}}
{{--                                        </h6>--}}
{{--                                    </div>--}}
{{--                                </a>--}}
{{--                            </li>--}}
{{--                        @endforeach--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            @endif--}}
{{--        </div>--}}
        <!--------------------
        END - Contacts Link in secondary top menu
        -------------------->
{{--    @endcan--}}
<!--------------------

            START - Settings Link in secondary top menu
            -------------------->

    <div class="messages-notifications os-dropdown-trigger os-dropdown-position-left">
        <i class="os-icon os-icon-zap"></i>
        <div class="new-messages-count">
            {{count($admin_notifications)}}
        </div>
        @if(count($admin_notifications)>0)
            <div class="os-dropdown light message-list">
                <div class="icon-w">
                    <i class="os-icon os-icon-zap"></i>
                </div>
                <ul>
                    @foreach($admin_notifications as $admin_notification)
                        <li>
                            @if($admin_notification->item_id!=null)
                                <a href="{{route('admin.item.show',[$admin_notification->item_id])}}">
                                    <div class="user-avatar-w">
                                        <img alt="" src="{{$admin_notification->item->images[0]}}">
                                    </div>
                                    <div class="message-content">
                                        <h6 class="message-from">
                                            @php
                                                try{
                                                    $handle_name=$admin_notification->item->user->name;
                                                }catch (Exception $e){
                                                    $handle_name='';
                                                }
                                            @endphp
                                            {{$handle_name}}
                                        </h6>
                                        <h6 class="message-title">
                                            {{$admin_notification->title['ar']}}
                                        </h6>
                                    </div>
                                </a>
                            @elseif($admin_notification->transfer_id!=null)
                                <a href="{{route('admin.transfer.show',[$admin_notification->transfer_id])}}">
                                    <div class="user-avatar-w">
                                        <img alt="" src="{{$admin_notification->transfer->user->image}}">
                                    </div>
                                    <div class="message-content">
                                        <h6 class="message-title">
                                            {{$admin_notification->title}}
                                        </h6>
                                    </div>
                                </a>
                            @else
                                <a href="{{route('admin.transfer.index')}}">
                                    <div class="user-avatar-w">
                                        <img alt="" src="{{asset('media/images/user/default.jpeg')}}">
                                    </div>
                                    <div class="message-content">
                                        <h6 class="message-title">
                                            {{$admin_notification->title['ar']}}
                                        </h6>
                                    </div>
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{--@can('view-settings || view-pages || view-roles || view-admins')--}}

    <div class="top-icon top-settings os-dropdown-trigger os-dropdown-position-right">
        <i class="os-icon os-icon-ui-46"></i>
        <div class="os-dropdown">
            <div class="icon-w">
                <i class="os-icon os-icon-ui-46"></i>
            </div>
            <ul>
{{--                @can('edit-settings')--}}
                    <li>
                        <a href="{{route('admin.setting')}}"><i class="os-icon os-icon-ui-49"></i><span>إعدادات عامة</span></a>
                    </li>
{{--                @endcan--}}
                @can('edit-roles')
                    <li>
                        <a href="{{route('admin.role.index')}}"><i class="os-icon os-icon-ui-83"></i><span>الصلاحيات والأدوار</span></a>
                    </li>
                @endcan
                @can('edit-admins')
                    <li>
                        <a href="{{route('admin.admin.index')}}"><i class="os-icon os-icon-ui-93"></i><span>أعضاء الإدارة</span></a>
                    </li>
                @endcan
            </ul>
        </div>
    </div>
{{--@endcan--}}
<!--------------------
    END - Settings Link in secondary top menu
    -------------------->
</div>

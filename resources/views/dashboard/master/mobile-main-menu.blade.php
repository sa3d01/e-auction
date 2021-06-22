<ul class="main-menu">
    <li class="has-sub-menu">
        <a href="#">
            <div class="icon-w">
                <div class="os-icon os-icon-wallet-loaded"></div>
            </div>
            <span>البيانات العامة</span>
        </a>
        <ul class="sub-menu">
            <li>
                <a href="{{route('admin.bank.index')}}">الحسابات البنكية</a>
            </li>
            <li>
                <a href="{{route('admin.ask.index')}}">الأسئلة الشائعة</a>
            </li>
            <li>
                <a href="{{route('admin.drop_down.list',['Partner'])}}">شركاء النجاح</a>
            </li>
            <li>
                <a href="{{route('admin.drop_down.list',['City'])}}">المدن</a>
            </li>
            <li>
                <a href="{{route('admin.drop_down.list',['itemStatus'])}}">حالات المركبات</a>
            </li>
            <li>
                <a href="{{route('admin.drop_down.list',['ScanStatus'])}}">حالات الفحص</a>
            </li>
            <li>
                <a href="{{route('admin.drop_down.list',['PaperStatus'])}}">حالات الاستمارة</a>
            </li>
            <li>
                <a href="{{route('admin.drop_down.list',['Mark'])}}">ماركات المركبات</a>
            </li>
            <li>
                <a href="{{route('admin.drop_down.list',['Model'])}}">موديلات المركبات</a>
            </li>
            <li>
                <a href="{{route('admin.drop_down.list',['Color'])}}">الألوان</a>
            </li>
        </ul>
    </li>

    <li class="has-sub-menu">
        <a href="#">
            <div class="icon-w">
                <div class="os-icon os-icon-alert-triangle"></div>
            </div>
            <span>الاشعارات الجماعية </span>
        </a>
        <ul class="sub-menu">
            <li>
                <a href="{{route('admin.notification.admin_notify_type',['admin_notify_type'=>'all'])}}"> الاشعارات العامة  </a>
            </li>
        </ul>
    </li>

{{--    <li class="has-sub-menu">--}}
{{--        <a href="#">--}}
{{--            <div class="icon-w">--}}
{{--                <div class="os-icon os-icon-package"></div>--}}
{{--            </div>--}}
{{--            <span>الباقات </span>--}}
{{--        </a>--}}
{{--        <ul class="sub-menu">--}}
{{--            <li>--}}
{{--                <a href="{{route('admin.package.index')}}"> قائمة البيانات </a>--}}
{{--            </li>--}}
{{--            <li>--}}
{{--                <a href="{{route('admin.package.create')}}"> إضافة </a>--}}
{{--            </li>--}}
{{--        </ul>--}}
{{--    </li>--}}

    <li class="has-sub-menu">
        <a href="#">
            <div class="icon-w">
                <div class="os-icon os-icon-wallet-loaded"></div>
            </div>
            <span>المركبات</span>
        </a>
        <ul class="sub-menu">
            <li>
                <a href="{{route('admin.item.create')}}"> إضافة مركبة </a>
            </li>
            <li>
                <a href="{{route('admin.item.status',['status'=>'pending'])}}"> المركبات الجديدة </a>
            </li>
            <li>
                <a href="{{route('admin.item.status',['status'=>'rejected'])}}"> المركبات المرفوضة  </a>
            </li>
            <li>
                <a href="{{route('admin.item.status',['status'=>'accepted'])}}"> المركبات فى انتظار الاعداد لمزاد </a>
            </li>
            <li>
                <a href="{{route('admin.items.vip')}}"> المركبات المميزة </a>
            </li>
            <li>
                <a href="{{route('admin.items.sold')}}"> المركبات المباعة </a>
            </li>
{{--            <li>--}}
{{--                <a href="{{route('admin.items.hidden')}}"> المركبات المختفيه من التطبيق </a>--}}
{{--            </li>--}}
        </ul>
    </li>

    <li class="has-sub-menu">
        <a href="#">
            <div class="icon-w">
                <div class="os-icon os-icon-activity"></div>
            </div>
            <span>المزادات </span>
        </a>
        <ul class="sub-menu">
            <li>
                <a href="{{route('admin.auction.index')}}"> المزادات الحالية </a>
            </li>
            <li>
                <a href="{{route('admin.auction.create')}}"> إضافة مزاد  </a>
            </li>
        </ul>
    </li>


    <li class="sub-menu">
        <a href="{{route('admin.user.index')}}">
            <div class="icon-w">
                <div class="os-icon os-icon-user-male-circle2"></div>
            </div>
            <span> الأعضاء</span>
        </a>
    </li>

    <li class="sub-menu">
        <a href="{{route('admin.transfer.index')}}">
            <div class="icon-w">
                <div class="os-icon os-icon-mail"></div>
            </div>
            <span> الحوالات البنكية</span>
        </a>
    </li>

    <li class="sub-menu">
        <a href="{{route('admin.refund.index')}}">
            <div class="icon-w">
                <div class="os-icon os-icon-rewind"></div>
            </div>
            <span>طلبات استرداد المستحقات </span>
        </a>
    </li>

    <li class="sub-menu">
        <a href="{{route('admin.contact.index')}}">
            <div class="icon-w">
                <div class="os-icon os-icon-email-2-at"></div>
            </div>
            <span>رسائل الأعضاء</span>
        </a>
    </li>

    <li class="sub-menu">
        <a href="{{route('admin.feed_back.index')}}">
            <div class="icon-w">
                <div class="os-icon os-icon-feather"></div>
            </div>
            <span>آراء العملاء</span>
        </a>
    </li>
</ul>

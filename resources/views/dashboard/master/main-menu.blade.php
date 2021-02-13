<ul class="main-menu">
{{--drop downs--}}
    <li class="sub-header">
        <span>البيانات العامة</span>
    </li>
    <li class="has-sub-menu">
        <a href="#">
            <div class="icon-w">
                <div class="os-icon os-icon-trending-up"></div>
            </div>
            <span>البيانات العامة</span>
        </a>
        <div class="sub-menu-w">
            <div class="sub-menu-header">
                البيانات العامة
            </div>
            <div class="sub-menu-icon">
                <i class="os-icon os-icon-trending-up"></i>
            </div>
            <div class="sub-menu-i">
                <ul class="sub-menu">
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
            </div>
        </div>
    </li>
{{--    //packages--}}
    <li class="sub-header">
        <span>الباقات </span>
    </li>
    <li class=" has-sub-menu">
        <a href="#">
            <div class="icon-w">
                <div class="os-icon os-icon-package"></div>
            </div>
            <span>الباقات </span></a>
        <div class="sub-menu-w">
            <div class="sub-menu-header">
                الباقات
            </div>
            <div class="sub-menu-icon">
                <i class="os-icon os-icon-package"></i>
            </div>
            <div class="sub-menu-i">
                <ul class="sub-menu">
                    <li>
                        <a href="{{route('admin.package.index')}}"> قائمة البيانات </a>
                    </li>
                    <li>
                        <a href="{{route('admin.package.create')}}"> إضافة </a>
                    </li>
                </ul>
            </div>
        </div>
    </li>
{{--    //items--}}
    <li class="sub-header">
        <span>السلع </span>
    </li>
    <li class=" has-sub-menu">
        <a href="#">
            <div class="icon-w">
                <div class="os-icon os-icon-shopping-cart"></div>
            </div>
            <span>السلع </span></a>
        <div class="sub-menu-w">
            <div class="sub-menu-header">
                السلع
            </div>
            <div class="sub-menu-icon">
                <i class="os-icon os-icon-shopping-cart"></i>
            </div>
            <div class="sub-menu-i">
                <ul class="sub-menu">
                    <li>
                        <a href="{{route('admin.item.status',['status'=>'pending'])}}"> السلع الجديدة </a>
                    </li>
                    <li>
                        <a href="{{route('admin.item.status',['status'=>'rejected'])}}"> السلع المرفوضة  </a>
                    </li>
                    <li>
                        <a href="{{route('admin.item.status',['status'=>'accepted'])}}"> السلع فى انتظار الاعداد لمزاد </a>
                    </li>
                    <li>
                        <a href="{{route('admin.items.vip')}}"> السلع المميزة </a>
                    </li>
                </ul>
            </div>
        </div>
    </li>
{{--    //auctions--}}
    <li class="sub-header">
        <span>المزادات </span>
    </li>
    <li class=" has-sub-menu">
        <a href="#">
            <div class="icon-w">
                <div class="os-icon os-icon-activity"></div>
            </div>
            <span>المزادات </span></a>
        <div class="sub-menu-w">
            <div class="sub-menu-header">
                المزادات
            </div>
            <div class="sub-menu-icon">
                <i class="os-icon os-icon-activity"></i>
            </div>
            <div class="sub-menu-i">
                <ul class="sub-menu">
                    <li>
                        <a href="{{route('admin.auction.index')}}"> المزادات الحالية </a>
                    </li>
                    <li>
                        <a href="{{route('admin.auction.create')}}"> إضافة مزاد  </a>
                    </li>
                </ul>
            </div>
        </div>
    </li>
{{--    //users--}}
    <li class="sub-header">
        <span>الأعضاء </span>
    </li>
    <li class="sub-menu">
        <a href="{{route('admin.user.index')}}">
            <div class="icon-w">
                <div class="os-icon os-icon-user-male-circle2"></div>
            </div>
            <span> الأعضاء</span>
        </a>
    </li>
{{--    //transfers--}}
    <li class="sub-header">
        <span>الحوالات البنكية </span>
    </li>
    <li class="sub-menu">
        <a href="{{route('admin.transfer.index')}}">
            <div class="icon-w">
                <div class="os-icon os-icon-mail"></div>
            </div>
            <span> الحوالات البنكية</span>
        </a>
    </li>
{{--contacts--}}
    <li class="sub-header">
        <span>رسائل الأعضاء</span>
    </li>
    <li class="sub-menu">
        <a href="{{route('admin.contact.index')}}">
            <div class="icon-w">
                <div class="os-icon os-icon-email-2-at"></div>
            </div>
            @if($new_contacts_count > 0)
                <span style="border-radius: 50%;padding: 5px;background:#fff;border: 2px solid blue;color: orangered;text-align: center;">
                    {{$new_contacts_count}}
                </span>
            @endif
            <span>رسائل الأعضاء</span>
        </a>
    </li>
{{--feed_backs--}}
    <li class="sub-header">
        <span>آراء العملاء</span>
    </li>
    <li class="sub-menu">
        <a href="{{route('admin.feed_back.index')}}">
            <div class="icon-w">
                <div class="os-icon os-icon-feather"></div>
            </div>
            @if($new_feed_backs_count > 0)
                <span style="border-radius: 50%;padding: 5px;background:#fff;border: 2px solid blue;color: orangered;text-align: center;">
                    {{$new_feed_backs_count}}
                </span>
            @endif
            <span>آراء العملاء</span>
        </a>
    </li>
</ul>

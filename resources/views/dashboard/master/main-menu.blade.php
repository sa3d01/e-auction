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
                        <a href="{{route('admin.item.status',['status'=>'accepted'])}}"> السلع فى انتظار الاعداد لمزاد </a>
                    </li>
                    <li>
                        <a href="{{route('admin.item.status',['status'=>'rejected'])}}"> السلع المرفوضة  </a>
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
{{--contacts--}}
{{--    <li class="sub-header">--}}
{{--        <span>رسائل الأعضاء</span>--}}
{{--    </li>--}}
{{--    <li class="sub-menu">--}}
{{--        <a href="{{route('admin.contact.index')}}">--}}
{{--            <div class="icon-w">--}}
{{--                <div class="os-icon os-icon-email-2-at"></div>--}}
{{--            </div>--}}
{{--            <span>رسائل الأعضاء</span>--}}
{{--        </a>--}}
{{--    </li>--}}
</ul>

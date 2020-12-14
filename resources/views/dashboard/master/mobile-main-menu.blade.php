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
                <div class="os-icon os-icon-wallet-loaded"></div>
            </div>
            <span>السلع</span>
        </a>
        <ul class="sub-menu">
            <li>
                <a href="{{route('admin.item.status',['status'=>'pending'])}}"> السلع الجديدة </a>
            </li>
{{--            <li>--}}
{{--                <a href="{{route('admin.item.status',['status'=>'accepted'])}}"> السلع فى انتظار الاضافة لمزاد  </a>--}}
{{--            </li>--}}
{{--            <li>--}}
{{--                <a href="{{route('admin.item.status',['status'=>'rejected'])}}"> السلع المرفوضة  </a>--}}
{{--            </li>--}}
        </ul>
    </li>
{{--    <li class="sub-menu">--}}
{{--        <a href="{{route('admin.contact.index')}}">--}}
{{--            <div class="icon-w">--}}
{{--                <div class="os-icon os-icon-email-2-at"></div>--}}
{{--            </div>--}}
{{--            <span>رسائل الأعضاء</span>--}}
{{--        </a>--}}
{{--    </li>--}}
</ul>

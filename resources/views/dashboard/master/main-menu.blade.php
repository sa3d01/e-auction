<ul class="main-menu">
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
                        <a href="{{route('admin.item.status',['status'=>'accepted'])}}"> السلع فى انتظار الاضافة لمزاد  </a>
                    </li>
                    <li>
                        <a href="{{route('admin.item.status',['status'=>'rejected'])}}"> السلع المرفوضة  </a>
                    </li>
                </ul>
            </div>
        </div>
    </li>

    <li class="sub-header">
        <span>رسائل الأعضاء</span>
    </li>
    <li class="sub-menu">
        <a href="{{route('admin.contact.index')}}">
            <div class="icon-w">
                <div class="os-icon os-icon-email-2-at"></div>
            </div>
            <span>رسائل الأعضاء</span>
        </a>
    </li>
</ul>

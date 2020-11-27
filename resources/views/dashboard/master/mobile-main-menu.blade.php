<ul class="main-menu">
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
            <li>
                <a href="{{route('admin.item.status',['status'=>'accepted'])}}"> السلع فى انتظار الاضافة لمزاد  </a>
            </li>
            <li>
                <a href="{{route('admin.item.status',['status'=>'rejected'])}}"> السلع المرفوضة  </a>
            </li>
        </ul>
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

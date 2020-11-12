<ul class="main-menu">
    @can('view-users')
    <li class="sub-header">
        <span>المستخدمين</span>
    </li>
    <li class="has-sub-menu">
        <a href="#">
            <div class="icon-w">
                <div class="os-icon os-icon-users"></div>
            </div>
            <span>المستخدمين</span></a>
        <div class="sub-menu-w">
            <div class="sub-menu-header">
                المستخدمين
            </div>
            <div class="sub-menu-icon">
                <i class="os-icon os-icon-users"></i>
            </div>
            <div class="sub-menu-i">
                <ul class="sub-menu">
                    <li>
                        <a href="{{route('admin.user.index')}}"> المستخدمين</a>
                    </li>
                </ul>
            </div>
        </div>
    </li>
    @endcan
{{--    //providers--}}
    @can('view-providers')
    <li class="sub-header">
        <span>مزودى الخدمات</span>
    </li>
    <li class=" has-sub-menu">
        <a href="#">
            <div class="icon-w">
                <div class="os-icon os-icon-agenda-1"></div>
            </div>
            <span>مزودى الخدمات</span></a>
        <div class="sub-menu-w">
            <div class="sub-menu-header">
                مزودى الخدمات
            </div>
            <div class="sub-menu-icon">
                <i class="os-icon os-icon-agenda-1"></i>
            </div>
            <div class="sub-menu-i">
                <ul class="sub-menu">
                    <li>
                        <a href="{{route('admin.provider.index')}}"> مزودى الخدمات </a>
                    </li>
                </ul>
            </div>
        </div>
    </li>
    @endcan
{{--    //orders--}}
    @can('view-orders')
    <li class="sub-header">
        <span>الطلبات </span>
    </li>
    <li class=" has-sub-menu">
        <a href="#">
            <div class="icon-w">
                <div class="os-icon os-icon-shopping-cart"></div>
            </div>
            <span>الطلبات </span></a>
        <div class="sub-menu-w">
            <div class="sub-menu-header">
                الطلبات
            </div>
            <div class="sub-menu-icon">
                <i class="os-icon os-icon-shopping-cart"></i>
            </div>
            <div class="sub-menu-i">
                <ul class="sub-menu">
                    <li>
                        <a href="{{route('admin.order.status',['status'=>'new'])}}"> الطلبات الجديدة  </a>
                        <a href="{{route('admin.order.status',['status'=>'in_progress'])}}"> الطلبات الجارية  </a>
                        <a href="{{route('admin.order.status',['status'=>'done'])}}"> الطلبات المنتهية  </a>
                    </li>
                </ul>
            </div>
        </div>
    </li>
    @endcan
{{--//notifications--}}
    @can('view-notifications')
    <li class="sub-header">
        <span>الاشعارات الجماعية </span>
    </li>
    <li class=" has-sub-menu">
        <a href="#">
            <div class="icon-w">
                <div class="os-icon os-icon-alert-octagon"></div>
            </div>
            <span>الاشعارات الجماعية </span></a>
        <div class="sub-menu-w">
            <div class="sub-menu-header">
                الاشعارات الجماعية
            </div>
            <div class="sub-menu-icon">
                <i class="os-icon os-icon-alert-octagon"></i>
            </div>
            <div class="sub-menu-i">
                <ul class="sub-menu">
                    <li>
                        <a href="{{route('admin.notification.admin_notify_type',['admin_notify_type'=>'user'])}}"> اشعارات المستخدمين </a>
                        <a href="{{route('admin.notification.admin_notify_type',['admin_notify_type'=>'provider'])}}"> اشعارات مقدمى الخدمات  </a>
                        <a href="{{route('admin.notification.admin_notify_type',['admin_notify_type'=>'all'])}}"> الاشعارات العامة  </a>
                    </li>
                </ul>
            </div>
        </div>
    </li>
    @endcan
    @can('view-wallets')
    <li class="sub-header">
        <span>التقارير المالية </span>
    </li>
    <li class="sub-menu">
        <a href="{{route('admin.wallet.index')}}">
            <div class="icon-w">
                <div class="os-icon os-icon-wallet-loaded"></div>
            </div>
            <span>التقارير المالية </span>
        </a>
    </li>
    @endcan
    @can('view-contacts')
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
    @endcan
</ul>

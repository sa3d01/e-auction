@extends('dashboard.master.base')
@section('content')

    <ul class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{route('admin.home')}}">الرئيسية</a>
        </li>
    </ul>
    <!--------------------
    END - Breadcrumbs
    -------------------->
    <div class="content-panel-toggler">
        <i class="os-icon os-icon-grid-squares-22"></i><span>قائمة المهام</span>
    </div>
    <div class="content-i">
        <div class="content-box">
            @include('dashboard.master.cards')
            @include('dashboard.master.charts')
        </div>
    </div>
@endsection


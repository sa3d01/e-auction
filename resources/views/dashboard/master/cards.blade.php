<div class="row">
    <div class="col-sm-12">
        <div class="element-wrapper">
            <h6 class="element-header">
                آخر الإحصائيات
            </h6>
            <div class="element-content">
                <div class="row">
                    <div class="col-sm-4 col-xxxl-3">
                        <a class="element-box el-tablo" href="#">
                            <div class="label">
                                المستخدمين
                            </div>
                            <div class="value">
                                {{$users_count}}
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-4 col-xxxl-3">
                        <a class="element-box el-tablo" href="#">
                            <div class="label">
                                مزودى الخدمات
                            </div>
                            <div class="value">
                                {{$providers_count}}
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-4 col-xxxl-3">
                        <a class="element-box el-tablo" href="#">
                            <div class="label">
                                الطلبات الجديدة
                            </div>
                            <div class="value">
                                {{$new_orders_count}}
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-4 col-xxxl-3">
                        <a class="element-box el-tablo" href="#">
                            <div class="label">
                                الطلبات الجديدة صاحبة العروض
                            </div>
                            <div class="value">
                                {{$offered_orders_count}}
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-4 col-xxxl-3">
                        <a class="element-box el-tablo" href="#">
                            <div class="label">
                                الطلبات الجارية المدفوعة
                            </div>
                            <div class="value">
                                {{$paid_in_progress_orders_count}}
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-4 col-xxxl-3">
                        <a class="element-box el-tablo" href="#">
                            <div class="label">
                                الطلبات الجارية غير المدفوعة
                            </div>
                            <div class="value">
                                {{$not_paid_in_progress_orders_count}}
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-4 col-xxxl-3">
                        <a class="element-box el-tablo" href="#">
                            <div class="label">
                                الطلبات المنتهية
                            </div>
                            <div class="value">
                                {{$done_orders_count}}
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-4 col-xxxl-3">
                        <a class="element-box el-tablo" href="#">
                            <div class="label">
                                الطلبات الملغاة
                            </div>
                            <div class="value">
                                {{$rejected_orders_count}}
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-4 col-xxxl-3">
                        <a class="element-box el-tablo" href="#">
                            <div class="label">
                                رسائل الأعضاء الغير مقروءة
                            </div>
                            <div class="value">
                                {{$new_contacts_count}}
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

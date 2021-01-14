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
                                السلع الجديدة فى انتظار المراجعة
                            </div>
                            <div class="value">
                                {{$new_items_count}}
                            </div>
                        </a>
                    </div>

                    <div class="col-sm-4 col-xxxl-3">
                        <a class="element-box el-tablo" href="#">
                            <div class="label">
                                السلع فى المزاد قبل المباشر
                            </div>
                            <div class="value">
                                {{$pre_auction_items}}
                            </div>
                        </a>
                    </div>

                    <div class="col-sm-4 col-xxxl-3">
                        <a class="element-box el-tablo" href="#">
                            <div class="label">
                                السلع فى المزاد المباشر
                            </div>
                            <div class="value">
                                {{$live_auction_items}}
                            </div>
                        </a>
                    </div>

                    <div class="col-sm-4 col-xxxl-3">
                        <a class="element-box el-tablo" href="#">
                            <div class="label">
                                السلع المنتهى المزايدة عليها
                            </div>
                            <div class="value">
                                {{$expire_auction_items}}
                            </div>
                        </a>
                    </div>

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

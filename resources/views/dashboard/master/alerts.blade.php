@if (session()->has('updated'))
    <script>
        $.notify("تم تعديل البيانات بقاعدة بياناتك", "success");
    </script>
@elseif (session()->has('created'))
    <script>
        $.notify("تم اضافة البيانات بقاعدة بياناتك", "success");
    </script>
@elseif (session()->has('deleted'))
    <script>
        $.notify("تم الحذف بنجاح", "success");
    </script>
@elseif (session()->has('notice'))
    <script>
        $.notify("تم تعديل البيانات بقاعدة بياناتك", "success");
    </script>
@endif

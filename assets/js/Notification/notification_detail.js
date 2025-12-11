  $(document).ready(function () {

    id = sessionStorage.getItem('noti_detail');
    
    console.log(id);

   $.ajax({
    url: "Notification_detail/get_data_detail",
     method: "POST",
    data:{
      id:id
    },
    success: function (data) {

      console.log(data);

      obj = JSON.parse(data);

      if (obj && obj.broadcast_title) {
        $("#title").text(obj.broadcast_title);
        $("#detail").html(obj.broadcast_detail);
        $("#start-date").text("เริ่มประกาศเมื่อ: " + obj.broadcast_start_date);
        $("#badge").show();
      } else {
        $("#title").text("ไม่พบข้อมูลการแจ้งเตือน");
        $("#badge").hide();
      }
    },
    error: function () {
      $("#title").text("เกิดข้อผิดพลาดในการโหลดข้อมูล");
      $("#badge").hide();
    }
  });
});


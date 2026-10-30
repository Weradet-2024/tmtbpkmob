function getCookie(name) {
	var value = "; " + document.cookie;
	var parts = value.split("; " + name + "=");
	if (parts.length == 2) return parts.pop().split(";").shift();
}

function renderNotifications(data) {
    const $list = $("#notification-list");
    $list.empty(); // เคลียร์รายการเก่า

    const ck_lang = getCookie("ck_emp_lang");
    let badgeText = ck_lang === "en" ? "New" : "ใหม่";
    let readIds = JSON.parse(localStorage.getItem("readBroadcastIds")) || [];

    $.each(data, function (index, item) {
      const isRead = readIds.includes(item.broadcast_id);
      const badge = isRead ? "" : `<span class="badge-new">${badgeText}</span>`;
      const maxLength = 30; // จำนวนตัวอักษรที่ต้องการให้แสดง
      let shortDetail = item.broadcast_detail;

      if (shortDetail.length > maxLength) {
          shortDetail = shortDetail.substring(0, maxLength).trim() + "...";
      }
      const notiDate = item.broadcast_lastest_noti
      ? item.broadcast_lastest_noti.substring(0, 10)
      : "-";

      const html = `
      <div class="notification-card" data-id="${item.broadcast_id}" onclick="goToDetail(${item.broadcast_id})">
        <div class="notification-icon text-dark bg-light">📢</div>
        <div class="notification-content">
          <h6>${item.broadcast_title} ${badge}</h6>
          <p>${shortDetail}</p>
          <div class="notification-time">${notiDate}</div>
        </div>
      </div>
    `;
      $list.append(html);
    });
  }

  function showLoader() {
    var overlay = document.getElementById('loading-overlay');
    if (overlay) overlay.style.display = 'flex';
  }

  function goToDetail(broadcast_id) {
    showLoader();
    // ดึงค่าที่เคยเก็บไว้ (ถ้ามี)
    let readIds = JSON.parse(localStorage.getItem("readBroadcastIds")) || [];
  
    // ถ้ายังไม่มี id นี้ → เพิ่มเข้าไป
    if (!readIds.includes(broadcast_id)) {
      readIds.push(broadcast_id);
      localStorage.setItem("readBroadcastIds", JSON.stringify(readIds));
    }
  
    // ไปหน้ารายละเอียด
    sessionStorage.setItem("noti_detail", broadcast_id);
    window.location.href = `Notification_detail`;
  }

  $(document).ready(function () {
    // ใช้งานจริง: ดึงข้อมูลจาก backend
    const API_URL = "Notification/get_data"; // เปลี่ยนเป็น URL ของ API ที่ใช้ดึงข้อมูล
    $.ajax({
      url: API_URL,
      method: "GET",
      success: function (data) {

        console.log(data);

        jsojnData = JSON.parse(data);
        renderNotifications(jsojnData);
      },
      error: function () {
        alert("ไม่สามารถโหลดข้อมูลการแจ้งเตือนได้");
      }
    });
    

    // ตัวอย่าง: ใช้ mock data แสดงผลทันที
  });
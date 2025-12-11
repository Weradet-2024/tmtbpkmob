 <style>
    .notification-detail-card {
      max-width: 600px;
      margin: 50px auto;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      background-color: #fff;
    }
    .notification-icon {
      font-size: 2rem;
    }
  </style>


<style>
    body {
        background-color: #f5f7fa;
        font-family: 'Segoe UI', sans-serif;
    }

    .notification-container {
        max-width: 700px;
        margin: auto;
    }

    .notification-card {
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        display: flex;
        align-items: flex-start;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        transition: background-color 0.2s ease;
    }

    .notification-card:hover {
        background-color: #f1f3f5;
    }

    .notification-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .notification-content h6 {
        margin: 0 0 4px 0;
        font-weight: 600;
    }

    .notification-content p {
        margin: 0;
        font-size: 0.95rem;
        color: #555;
    }

    .notification-time {
        font-size: 0.8rem;
        color: #888;
        margin-top: 5px;
    }

    .badge-new {
        background-color: #0d6efd;
        color: white;
        font-size: 0.7rem;
        padding: 4px 8px;
        border-radius: 10px;
        margin-left: 10px;
    }
</style>

ิ<br>
<br>
<br>
<div class="container">
  <div class="notification-detail-card">
    <div class="d-flex align-items-center mb-3">
      <div class="notification-icon">📢</div>
      <h4 class="notification-title mb-0" id="title">กำลังโหลด...</h4>
      <span class="badge-new" id="badge">ใหม่</span>
    </div>
    <p id="detail"></p>
    <div class="notification-time" id="start-date"></div>
    <br>
    <a href="javascript:history.back()" class="btn btn-secondary">ย้อนกลับ</a>
  </div>
</div>
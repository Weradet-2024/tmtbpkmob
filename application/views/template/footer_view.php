</body>
  <style>
    .footer {
      background: linear-gradient(135deg, #007bff, #00c6ff);
      box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.1);
      color: white;
    }

    .footer-link {
      text-decoration: none;
      color: rgba(255, 255, 255, 0.9);
      font-size: 0.75rem;
      font-weight: 500;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 12px 0;
      transition: transform 0.3s ease, color 0.3s ease;
    }

    .footer-icon {
      font-size: 1.6rem;
      margin-bottom: 4px;
      transition: transform 0.6s ease-in-out;
    }

    .footer-link:hover .footer-icon {
      transform: rotate(360deg);
    }

    .footer-link:hover {
      color: #fff;
    }

    @media (min-width: 768px) {
      .footer-icon {
        font-size: 1.9rem;
      }

      .footer-link {
        font-size: 0.85rem;
      }
    }
  </style>


<!-- Footer -->
<footer class="footer fixed-bottom">
  <div class="container-fluid">
    <div class="row row-cols-3 text-center">
      <div class="col">
        <a href="Home" class="footer-link">
          <i class="bi bi-house-door footer-icon"></i>
          <?=$this->lang->line('home'); ?>
        </a>
      </div>
      <div class="col">
        <a href="Attendance" class="footer-link">
          <i class="bi bi-upc-scan footer-icon"></i>
          <?=$this->lang->line('scan'); ?>
        </a>
      </div>
      <div class="col">
        <a href="Setting" class="footer-link">
          <i class="bi bi-sliders footer-icon"></i>
          <?=$this->lang->line('setting'); ?>
        </a>
      </div>
    </div>
  </div>
</footer>

<!-- Loading Overlay -->
<div id="loading-overlay" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:10000;background:rgba(255,255,255,0.7);justify-content:center;align-items:center;">
  <div class="spinner"></div>
</div>
<style>
.spinner {
  border: 8px solid #f3f3f3;
  border-top: 8px solid #3498db;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  animation: spin 1s linear infinite;
}
@keyframes spin {
  0% { transform: rotate(0deg);}
  100% { transform: rotate(360deg);}
}
</style>

</html>




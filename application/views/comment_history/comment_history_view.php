<style>
    body, html {
        height: 100%;
        margin: 0;
        background: #e5e5e5;
        font-family: 'Segoe UI', Arial, sans-serif;
    }
    .header {
        background: #1da1f2;
        color: #fff;
        padding: 18px 0 12px 0;
        text-align: center;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .header .logo {
        font-weight: bold;
        font-size: 1.3rem;
        letter-spacing: 1px;
        margin-left: 20px;
    }
    .header .icon {
        font-size: 1.5rem;
        margin-right: 20px;
        cursor: pointer;
    }
    .main-content {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding-top: 80px;
        padding-bottom: 80px;
    }
    .card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.07);
        padding: 32px 24px;
        max-width: 350px;
        width: 100%;
        margin: 0 auto;
        text-align: center;
    }
    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 18px;
        color: #333;
    }
    .line-link {
        display: inline-block;
        background: #f5f6fa;
        border-radius: 6px;
        padding: 12px 10px;
        font-size: 1rem;
        color: #1da1f2;
        word-break: break-all;
        text-decoration: none;
        transition: background 0.2s;
    }
    .line-link:hover {
        background: #e1f5fe;
        text-decoration: underline;
    }
    .bottom-nav {
        position: fixed;
        left: 0;
        bottom: 0;
        width: 100vw;
        background: #1da1f2;
        display: flex;
        justify-content: space-around;
        align-items: center;
        height: 60px;
        box-shadow: 0 -2px 8px rgba(0,0,0,0.04);
        z-index: 1000;
    }
    .bottom-nav .nav-item {
        color: #fff;
        text-align: center;
        font-size: 0.9rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: color 0.2s;
    }
    .bottom-nav .nav-item.active {
        color: #ffe082;
    }
    .bottom-nav .nav-item i {
        font-size: 1.4rem;
        margin-bottom: 2px;
    }
    @media (max-width: 400px) {
        .card { padding: 20px 5px; }
        .header .logo, .header .icon { font-size: 1rem; }
    }
</style>

<!-- Header -->
<div class="header">
    <span class="logo">TOGETAMOVE</span>
    <span class="icon"><i class="fa fa-bell"></i></span>
</div>

<div class="main-content">
    <div class="card">
        <div class="card-title">Line OA</div>
        <a class="line-link" href="https://line.me/R/ti/p/@371whdcx" target="_blank">
        https://line.me/R/ti/p/@371whdcx
        </a>
    </div>
</div>


<!-- FontAwesome CDN for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

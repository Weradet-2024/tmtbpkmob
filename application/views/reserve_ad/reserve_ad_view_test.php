<?php $lang = $_COOKIE["ck_emp_lang"]; ?>


<style>
    #map {
        height: calc(100vh - 100px);
        background: #eee;
        padding-left: 260px;
    }

    .drawer {
        position: fixed;
        bottom: 0;
        width: 100%;
        padding: 0 26px;
        background: white;
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.2);
        height: 20vh;
        overflow: hidden;
        transition: height 0.2s ease;
        z-index: 1050;
        box-sizing: border-box;
        /* 👈 ป้องกันทะลุ */
    }

    .drawer-header {
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: grab;
    }

    .drawer-toggle-bar {
        width: 80px;
        height: 10px;
        background-color: #ccc;
        border-radius: 10px;
    }

    @media (max-width: 768px) {
        #map {
            padding-left: 0;
        }

        .drawer {
            left: 0;
            width: 100%;
        }
    }

    /* Filters form */
    #searchForm {
        background: #fff;
        padding: 1rem 1.5rem;
        box-shadow: 0 1px 4px rgb(0 0 0 / 0.1);
        border-radius: 0.375rem;
        margin: 1rem auto;
        max-width: 1200px;
    }


    /* ปรับ input date ให้สูงเท่า Select2 */
    input[type="date"].form-control {
        height: calc(2.375rem + 2px) !important;
        /* ความสูงเดียวกับ .form-select */
        padding: 0.375rem 0.75rem !important;
    }

    .select2-container--default .select2-selection--single {
        height: 38px !important;
        /* ตรงกับ .form-select ของ Bootstrap */
        padding: 6px 12px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    .select2-selection__rendered {
        line-height: 24px !important;
    }

    input[type="date"].form-control {
        height: 38px;
        /* ให้เท่ากับ select2 */
        padding: 6px 12px;
        font-size: 1rem;
    }

    /* แก้ vertical alignment ของปุ่มให้เท่าช่องอื่น */
    .d-grid button.btn {
        height: 38px;
    }

    .select2-container {
        width: 100% !important;
        /* สำคัญมากบนมือถือ */
    }

    @media (max-width: 768px) {
        .flatpickr-time {
            min-width: 120px;
            font-size: 14px;
        }
    }

    @media (max-width: 576px) {
        #searchForm {
            margin: 8px !important;
        }

        #searchForm .form-label {
            margin-bottom: 2px;
            font-size: 0.9rem;
        }

        #searchForm .form-select,
        #searchForm .form-control {
            padding: 4px 8px;
            font-size: 0.9rem;
            height: auto;
        }

        #searchForm button {
            padding: 6px;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 576px) {
        #searchForm {
            margin: 8px !important;
        }

        #searchForm .form-label {
            margin-bottom: 2px;
            font-size: 0.9rem;
        }

        #searchForm .form-select,
        #searchForm .form-control {
            padding: 4px 8px;
            font-size: 0.9rem;
            height: auto;
        }

        #searchForm button {
            padding: 6px;
            font-size: 0.9rem;
        }
    }

    #map {
        width: 100%;
        height: 500px;
        /* ค่า default บนจอใหญ่ */
        background-color: #f2f2f2;
    }

    @media (max-width: 576px) {
        #map {
            height: 300px;
            /* ลดขนาดลงบนมือถือ */
        }

        #map h4 {
            font-size: 1rem;
        }
    }
</style>

<br>
<br>
<br>
<br>
<br>

<div class="container-fluid">
    <!-- Filters -->
    <form class="container mt-4 p-3 border rounded shadow-sm bg-white" id="searchForm">
        <div class="row g-3">

            <!-- สายรถ -->
            <div class="col-12 col-md-4">
                <label for="route" class="form-label">สายรถ</label>
                <select id="route" class="form-select select2" onchange="getLocation()">
                    <option value="">เลือกสายรถ</option>
                </select>
            </div>

            <!-- จุดขึ้น-ลง -->
            <div class="col-12 col-md-4">
                <label for="location" class="form-label">จุดขึ้น-ลง</label>
                <select id="location" class="form-select select2">
                    <option value="">เลือกจุดขึ้น-ลง</option>
                </select>
            </div>
        </div>

        <!-- ปุ่มค้นหา -->
        <div class="row mt-3">
            <div class="col-12 d-grid">
                <button type="button" id="btn_search" onclick="getData()" class="btn btn-primary">
                    <i class="bx bx-search"></i> ค้นหา
                </button>
            </div>
        </div>
    </form>




    <!-- Map -->
    <div id="map" class="d-flex justify-content-center align-items-center">
        <h4>แผนที่จะแสดงที่นี่</h4>
    </div>

    <!-- Drawer -->
    <div id="drawer" class="drawer">
        <div class="drawer-header" id="drawerHeader">
            <div class="drawer-toggle-bar"></div>
        </div>
        <div class="p-3 overflow-auto" style="max-height: calc(75vh - 40px);">
            <h5>ข้อมูลเพิ่มเติม</h5>
            <p>ลากขึ้นหรือลงเพื่อเปลี่ยนความสูง</p>

            <hr>





            <div class="d-flex mt-3">
                <button type="button" id="btn_save" onclick="saveData()" class="btn btn-info">
                    <i class="bx bx-save"></i> บันทึก
                </button>
            </div>



        </div>
    </div>



</div>


</div>

<script src="https://tttp-ldbox.longdo.com/map3"></script>

<!-- / Content -->

<!-- Drawer Toggle Script -->
<script>

    var map = new longdo.Map({
        placeholder: document.getElementById('map'),
        zoom: 12,
        layer: [
            longdo.Layers.NORMAL,    // หรือใช้ GRAY/GRAY_EN ตามต้องการ
            longdo.Layers.TRAFFIC    // เลเยอร์แสดงข้อมูลจราจร
        ]
    });

    const drawer = document.getElementById('drawer');
    const header = document.getElementById('drawerHeader');

    const snapPoints = [25, 50, 75];
    let currentIndex = 0; // เริ่มที่ index 0 = 25%

    function setDrawerHeight(percent) {
        percent = Math.min(75, Math.max(25, percent));
        drawer.style.height = percent + 'vh';
    }

    function onHeaderClick() {
        currentIndex = (currentIndex + 1) % snapPoints.length;
        setDrawerHeight(snapPoints[currentIndex]);
    }

    // เริ่มต้นความสูง
    setDrawerHeight(snapPoints[currentIndex]);

    // เพิ่ม event listener แค่คลิก
    header.addEventListener('click', onHeaderClick);



    // const drawer = document.getElementById('drawer');
    // const header = document.getElementById('drawerHeader');

    // let startY = 0;
    // let startHeight = 0;
    // let isDragging = false;

    // function setDrawerHeight(percent) {
    //     // constrain: only allow 25%, 50%, 75%
    //     percent = Math.min(75, Math.max(25, percent));
    //     drawer.style.height = percent + 'vh';
    // }

    // function onDragStart(e) {
    //     isDragging = true;
    //     startY = e.touches ? e.touches[0].clientY : e.clientY;
    //     startHeight = parseFloat(drawer.style.height);
    //     drawer.style.transition = 'none';
    //     document.body.style.userSelect = 'none';
    // }

    // function onDragMove(e) {
    //     if (!isDragging) return;
    //     const currentY = e.touches ? e.touches[0].clientY : e.clientY;
    //     const deltaY = startY - currentY;
    //     const deltaPercent = (deltaY / window.innerHeight) * 100;
    //     setDrawerHeight(startHeight + deltaPercent);
    // }

    // function onDragEnd() {
    //     isDragging = false;
    //     drawer.style.transition = 'height 0.2s ease';
    //     document.body.style.userSelect = '';

    //     // Snap to nearest level (25%, 50%, 75%)
    //     const height = parseFloat(drawer.style.height);
    //     const snapPoints = [25, 50, 75];
    //     let closest = snapPoints.reduce((a, b) => {
    //         return Math.abs(b - height) < Math.abs(a - height) ? b : a;
    //     });
    //     setDrawerHeight(closest);
    // }

    // // Touch events
    // header.addEventListener('touchstart', onDragStart);
    // window.addEventListener('touchmove', onDragMove);
    // window.addEventListener('touchend', onDragEnd);

    // // Mouse events
    // header.addEventListener('mousedown', onDragStart);
    // window.addEventListener('mousemove', onDragMove);
    // window.addEventListener('mouseup', onDragEnd);

    $(window).on('resize', function () {
        $('.select2').each(function () {
            $(this).css('width', '100%');        // force CSS
            //$(this).trigger('change.select2');   // notify Select2 to redraw
        });
    });

    $(document).on('touchstart click', '.select2-selection', function () {
        $(this).trigger('mousedown');
    });


</script>
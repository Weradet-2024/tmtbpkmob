<style>
    .card-menu {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
    }

    .card-menu .card {
        flex: 1 1 calc(25% - 1rem);
        max-width: calc(25% - 1rem);
        min-width: 120px;
        max-width: 140px;
        padding: 1rem;
        text-align: center;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }

    .card-menu .card:hover {
        transform: translateY(-5px);
    }

    .card-menu .card i {
        font-size: 2rem;
        color: #007bff;
        margin-bottom: 0.5rem;
    }

    .card-menu .card-title {
        font-size: 1rem;
        margin: 0;
    }
</style>
<section class="">
    <div class="container mt-5">
        <div class="col-12 mt-5 hero-text-image">
            <div class="row">
                <div class="col-lg-8 mb-5 text-center text-lg-start mt-5">

                    <!-- ข้อมูลพนักงาน -->
                    <div class="py-3 px-3">
                        <span id="lbl_emp_code" class="fs-6 text-muted d-block"></span>
                        <span id="lbl_name" class="fs-5 fw-semibold text-dark d-block"></span>
                    </div>

                    <!-- วันที่ -->
                    <div class="mt-3">
                        <p id="lbl_today" class="fs-5 fw-bold text-primary mb-0"></p>
                    </div>

                </div>

            </div>

            <ul class="list-group shadow-sm overflow-hidden mx-auto" style="font-size: 0.95rem;">
                <!-- Sarawuth : Disable display route out on 20/06/2025 -->
                <li class="list-group-item d-flex align-items-center py-3">
                    <i class="bi bi-signpost-split-fill text-danger fs-5 me-3"></i>
                <div>
                        <small id="lbl_pickup" class="text-muted">...</small>
                    </div>
                </li>
                <!-- จุดลงรถ -->
                <li class="list-group-item d-flex align-items-center py-3">
                    <i class="bi bi-signpost-split-fill text-danger fs-5 me-3"></i>
                    <div>
                        <small id="lbl_dropoff" class="text-muted">...</small>
                    </div>
                </li>

                <!-- ปุ่มดู Ticket -->
                <li class="list-group-item text-end bg-light">
                    <button class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="scrollToMyTicket()">
                        <i class="bi bi-ticket-perforated-fill me-1"></i> <?= $this->lang->line("view"); ?> Ticket
                    </button>
                </li>
            </ul>



            <div class="container">
                <div class="card-menu">
                    <?php for ($i = 0; $i < count($menu); $i++) {
                        $module_passenger_icon = $menu[$i]['module_passenger_icon'];
                        $module_passenger_name_th = $menu[$i]['module_passenger_name_th'];
                        $module_passenger_name_en = $menu[$i]['module_passenger_name_en'];
                        $module_passenger_link = $menu[$i]['module_passenger_link'];
                        ?>
                        <?php if ($_COOKIE['ck_emp_lang'] == "th") { ?>
                            <div class="card">
                                <a href="<?php echo $module_passenger_link ?>">
                                    <i class=" <?php echo $module_passenger_icon ?>"></i>
                                    <h5 class="card-title fs-5 text-muted"><?php echo $module_passenger_name_th ?></h5>
                                </a>
                            </div>


                        <?php } else { ?>
                            <div class="card">
                                <a href="<?php echo $module_passenger_link ?>">
                                    <i class=" <?php echo $module_passenger_icon ?>"></i>
                                    <h5 class="card-title fs-5 text-muted"><?php echo $module_passenger_name_en ?></h5>
                                </a>
                            </div>
                        <?php } ?>

                    <?php } ?>

                  
                </div>
            </div>

        </div>
        <div class="col-lg-4 iphone-wrap">
        </div>
    </div>
    </div>
    </div>
</section>

<section id="tickets_reservation_section">
    <div class="container mt-5" id="">
        <!-- หัวข้อหลัก -->
        <div class="text-center mb-5">
            <h2 class="fw-bold text-primary">
                <i class="bi bi-ticket-detailed-fill"></i><?= $this->lang->line('my_tickets'); ?>
            </h2>
            <p class="fs-5 text-muted"> <?= $this->lang->line('check_tickets'); ?></p>
        </div>

        <!-- รายการจอง -->
        <div id="tickets_reservation" class="mx-auto row g-5">

        </div>
    </div>
</section>


<div class="modal fade" id="modal_info">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $this->lang->line('data_reserve'); ?></h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr>
                        <td style="width:30%;"><?= $this->lang->line('passenger'); ?></td>
                        <td style="width:70%;" class="text-primary"><span id="modal_info_name"></span></td>
                    </tr>
                    <tr>
                        <td style="width:30%;"><?= $this->lang->line('workdate'); ?></td>
                        <td style="width:70%;" class="text-primary"><span id="modal_info_departure"></span></td>
                    </tr>
                    <tr>
                        <td style="width:30%;"><?= $this->lang->line('round'); ?></td>
                        <td style="width:70%;" class="text-primary"><span id="modal_info_round"></span></td>
                    </tr>
                    <tr>
                        <td style="width:30%;"><?= $this->lang->line('trip'); ?></td>
                        <td style="width:70%;" class="text-primary"><span id="modal_info_trip"></span></td>
                    </tr>
                    <tr>
                        <td style="width:30%;"><?= $this->lang->line('route'); ?></td>
                        <td style="width:70%;" class="text-primary"><span id="modal_info_route"></span></td>
                    </tr>
                    <tr>
                        <td style="width:30%;"><?= $this->lang->line('point'); ?></td>
                        <td style="width:70%;" class="text-primary"><span id="modal_info_point"></span></td>
                    </tr>
                    <tr>
                        <td style="width:30%;"><?= $this->lang->line('time'); ?></td>
                        <td style="width:70%;" class="text-primary"><span id="modal_info_time"></span></td>
                    </tr>
                    <tr>
                        <td style="width:30%;"><?= $this->lang->line('status'); ?></td>
                        <td style="width:70%;"><span id="modal_info_status"></span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function scrollToMyTicket() {
        const target = document.getElementById('my_ticket');
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        } else {
            // fallback: scroll to bottom of page
            window.scrollTo({
                top: document.body.scrollHeight,
                behavior: 'smooth'
            });
        }
    }
</script>
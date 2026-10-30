<div class="container" style="margin-top:20px;">
    <div class="row align-items-top">
        <div class="col-12 hero-text-image">
            <div class="row" style="display: none;" id="scan_qr_code">
                <div class="col-12" style="display: none;" id="notpass">
                    <span class="row justify-content-center text-center mb-5" data-aos="fade-up" style="font-size:24px !important; color:#FFF;"><?=$this->lang->line('attendance_complete'); ?></span>
                    <div style="background-color:#FFF; padding:10px; border-radius:5px;margin-left: 5px;margin-right: 5px; " data-aos="fade-left">
                        <div class="row">
                            <div class="col-12" style="text-align:center;">
                                <img src=" <?php echo base_url() . 'assets/img/notpass.png' ?>" style="width:48px;" />
                            </div>
                            <div class="col-12" style="margin-top: 5px;">
                                <span class="btn btn-danger aos-init aos-animate" style="width:100%;">
                                <?=$this->lang->line('no_reserve'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12" style="display: none;" id="pass">
                    <span class="row justify-content-center text-center mb-5" data-aos="fade-up" style="font-size:24px !important; color:#FFF;"><?=$this->lang->line('attendance_complete'); ?></span>
                    <div style="background-color:#FFF; padding:10px; border-radius:5px;margin-left: 5px;margin-right: 5px; " data-aos="fade-left">
                        <div class="row">
                            <div class="col-12" style="text-align:center;">
                                <img src="<?php echo base_url() . 'assets/img/pass.png' ?>" style="width:48px;" />
                            </div>
                            <div class="col-12" style="margin-top: 5px;">
                                <span class="btn btn-success aos-init aos-animate" style="width:100%;">
                                <?=$this->lang->line('reserve_complete'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div style="border-radius:5px;  padding:5px;">
                        <div style="background-color:#FFF; padding:20px; border-radius:5px; ">
                            <div class="row" style="margin-top:20px;">
                                <div class="col-4">
                                    <h6><?=$this->lang->line('status'); ?></h6>
                                </div>
                                <div class="col-8">
                                    <h2 id="lbl_result_status"></h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <h6><?=$this->lang->line('passenger'); ?></h6>
                                </div>
                                <div class="col-8">
                                    <h2 id="lbl_result_name"></h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <h6><?=$this->lang->line('date'); ?></h2>
                                </div>
                                <div class="col-8">
                                    <h2 id="lbl_result_date"></h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <h6><?=$this->lang->line('route'); ?></h2>
                                </div>
                                <div class="col-8">
                                    <h2 id="lbl_result_route"></h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <h6><?=$this->lang->line('round'); ?></h2>
                                </div>
                                <div class="col-8">
                                    <h2 id="lbl_result_trip"></h2>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-4">
                                    <h6><?=$this->lang->line('veh_type'); ?></h2>
                                </div>
                                <div class="col-8">
                                    <h2 id="lbl_result_veh_type"></h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <h6><?=$this->lang->line('licenseplate'); ?></h2>
                                </div>
                                <divc class="col-8">
                                    <h2 id="lbl_result_veh_license"></h2>
                                </divc>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <h6><?=$this->lang->line('veh_code'); ?></h2>
                                </div>
                                <div class="col-8">
                                    <h2 id="lbl_result_veh_code"></h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <h6><?=$this->lang->line('driver_name'); ?></h2>
                                </div>
                                <div class="col-8">
                                    <h2 id="lbl_result_driver"></h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <h6><?=$this->lang->line('driver_tel'); ?></h2>
                                </div>
                                <div class="col-8">
                                    <h2 id="lbl_result_driver_tel"></h2>
                                </div>
                            </div>
                            <div id="return_result"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="display: none;" id="bus_service">
                <div class="col-12">
                    <span class="row justify-content-center text-center mb-5" data-aos="fade-up" style="font-size:24px !important; color:#FFF;">ผลลัพธ์ข้อมูลการสแกน</span>
                    <div style="border-radius:5px;  padding:5px;">
                        <div style="background-color:#FFF; padding:20px; border-radius:5px; ">
                            <div class="row" style="margin-top:20px;">
                                <div class="col-12" style="text-align:center;">
                                    <img src="<?php echo base_url() . 'assets/img/notpass.png' ?>" style="width:280px;" />
                                </div>
                                <a href="Attendance" class="btn btn-danger aos-init aos-animate" style="width:100%;margin-top:20px;">
                                <?=$this->lang->line('no_post'); ?>
                                </a>
                            </div>
                        </div>
                        <div id="return_result"></div>
                    </div>
                </div>
            </div>

            <div class="row" style="display: none;" id="wrong_qr_code">
                <div class="col-12">
                    <span class="row justify-content-center text-center mb-5" data-aos="fade-up" style="font-size:24px !important; color:#FFF;">ผลลัพธ์ข้อมูลการสแกน</span>
                    <div style="border-radius:5px;  padding:5px;">
                        <div style="background-color:#FFF; padding:20px; border-radius:5px; ">
                            <div class="row" style="margin-top:20px;">
                                <div class="col-12" style="text-align:center;">
                                    <img src="<?php echo base_url() . 'assets/img/notpass.png' ?>" style="width:280px;" />
                                </div>
                                <a href="Attendance" class="btn btn-danger aos-init aos-animate" style="width:100%;margin-top:20px;">
                                    ขออภัย ข้อมูล QR CODE ไม่ถูกต้อง กรุณาตรวจสอบและทำรายการใหม่อีกครั้ง
                                </a>
                            </div>
                        </div>
                        <div id="return_result"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
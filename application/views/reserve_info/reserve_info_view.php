<style>
    label {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .form-control {
        font-size: 1.05rem;
        padding: 10px 14px;
        height: auto;
    }

   .select2-container--default .select2-selection--single {
    height: 56px !important;
    padding: 12px 16px !important;
    border: 1px solid #ced4da !important;
    border-radius: 0.5rem !important;
    background-color: #fff !important;
    font-size: 1.1rem !important;
    display: flex !important;
    align-items: center !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #212529 !important;
    line-height: 1.6 !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 100% !important;
    right: 14px !important;
    width: 24px !important;
}

</style>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="container" style="margin-top:100px;">
    <div class="row align-items-top">
        <div class="col-12 hero-text-image">
            <div class="row">
                <div class="col-12">
                    <div style="border-radius:5px; background-color:#FFF;  padding:5px;" data-aos="fade-left">
                        <span class="section-heading row justify-content-center text-center" data-aos="fade-up"
                            style="font-size:24px !important;"></span>
							  <span class="section-heading row justify-content-center text-center" data-aos="fade-up"
                            style="font-size:24px !important;"><?= $this->lang->line('booking_history'); ?></span>
                        <div class="row">
                            <div class="col-12" style="padding-left:20px; padding-right:20px;">
                                <label><?= $this->lang->line('status'); ?></label>
                                <input name="reserve_id" id="reserve_id" type="hidden" />
                                <input name="reserve_status" id="reserve_status" readonly type="text" class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <label><?= $this->lang->line('passenger'); ?></label>
                                <input name="reserve_name" id="reserve_name" readonly type="text" class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <label><?= $this->lang->line('departure_date'); ?></label>
                                <input name="reserve_date" id="reserve_date" readonly type="text" class="form-control"
                                    disabled />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <label><?= $this->lang->line('round'); ?></label>
                                <select name="reserve_trip" id="reserve_trip" class="form-control"
                                    onchange="chkRoute(this.value);">
                                    <option value="">-- <?= $this->lang->line('select'); ?> --</option>
                                    <option value="in"><?php echo $this->lang->line("trip_in");?></option>
                                    <option value="out"><?php echo $this->lang->line("trip_out");?></option>
                                </select>
                            </div>
                            <div class="col-6" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <input type="hidden" name="shift_id" id="shift_id">
                                <label><?= $this->lang->line('time'); ?></label>
                                <select name="reserve_round" id="reserve_round" class="form-control" onchange="getRouteByShift();">
                                    <option value="">-- select --</option>
                                </select>
                            </div>
                        </div>
                        <!-- <br> -->
                        <!-- <div class="alert alert-info" style="text-align:center;">
                            <h6><?= $this->lang->line('text_rhi_1'); ?></h6>
                            <h6><?= $this->lang->line('text_rhi_2'); ?></h6>
                        </div> -->
                        <div class="row">
                            <div class="col-12"
                                style="padding-left:20px; padding-right:20px; padding-top:10px; text-align:center;">
                                <div id="lbl_point"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <label><?= $this->lang->line('route'); ?></label>
								<!-- <label id="lbl_route"></label> -->
                                <select name="reserve_route" id="reserve_route" class="form-control"
                                    onchange="chkPoint();">
                                    <option value="">-- select --</option>
                                </select>
                            </div>
                        </div>
                        
                            <div class="row">
                                <div class="col-12" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                    <label><?= $this->lang->line('point_info'); ?></label>
									<!-- <label id="lbl_points"></label> -->
                                    <select name="reserve_point" id="reserve_point" class="form-control">
                                        <option value="">-- select --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="container text-center"></div>
                            <div class="row" style="margin-top:10px;">
                                <div id="div_btn_back" class="col-12" style="margin-top:10px;">
                                    <button id="btn_back" class="btn btn-secondary btn-sm"
                                        style="padding:10px !important; width:100%; height:100%; font-size:12px;"
                                        onclick="backVal();">
                                        <?= $this->lang->line('booking_history'); ?>
                                    </button>
                                </div>
                            </div>
                            <div class="row Check-feature-user-cancel">
                                <div id="div_btn_submit" class="col-12" style="margin-top:10px;">
                                    <button id="btn_submit" class="btn btn-primary"
                                        style="padding:10px !important; width:100%; height:100%; font-size:12px;"
                                        onclick="chkVal();">
                                        <?= $this->lang->line('save_data'); ?>
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div id="div_btn_del" class="col-12" style="margin-top:10px;">
                                    <button id="btn_del" class="btn btn-danger"
                                        style="padding:10px !important; width:100%; height:100%; font-size:12px;"
                                        onclick="cancelVal();">
                                        <?= $this->lang->line('cancel_booking'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="return_result"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<br>
<br>
<br>

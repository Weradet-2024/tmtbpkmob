<br>
<br>
<br>
<br>

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
<div class="container">
    <div class="row align-items-top">
        <div class="col-12 hero-text-image">
            <div class="row">
                <div class="col-12">
                    <div style="border-radius:5px; background-color:#FFF;  padding:5px;" data-aos="fade-left">
                        <span
                            class="section-heading row justify-content-center text-center mb-5 mb-5 fw-bold text-primary"
                            data-aos="fade-up"
                            style="font-size:24px !important;"><?= $this->lang->line('reserve_ad_menu'); ?></span>
                        <div class="row">
                            <div class="col-12" style="padding-left:20px; padding-right:20px;">
                                <label><?= $this->lang->line('passenger'); ?></label>
                                <input name="reserve_name" id="reserve_name" type="text" class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <label><?= $this->lang-> line('start_date'); ?></label>
                                <input name="reserve_date" id="reserve_date" type="text" class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <label><?= $this->lang->line('end_date'); ?></label>
                                <input name="reserve_date" id="reserve_date_end" type="text" class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <label><?= $this->lang->line('round'); ?></label>
                                <select name="round" id="round" class="form-control" onchange="chkRoundTrip(this.value);">
                                    <option value="">-- <?= $this->lang->line('select'); ?> --</option>
                                    <option value="one_way"><?= $this->lang->line('oneway'); ?></option>
                                    <option value="two_way"><?= $this->lang->line('roundtrip'); ?></option>
                                </select>
                            </div>
                            <span class="text-danger" style="display: none;" id="round_err">please select this.</span>
                        </div>

                        <div class="row">
                            <div class="col-12" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <label><?= $this->lang->line('time'); ?></label>
                                <select name="reserve_time" id="reserve_time" class="form-control"
                                    onchange="chkTrip(this.value);">
                                    <option value="">-- <?= $this->lang->line('select'); ?> --</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <!-- <div class="alert alert-info" style="text-align:center;">
                            <h6><?= $this->lang->line('routeauto1'); ?></h6>
                            <h6><?= $this->lang->line('routeauto2'); ?></h6>
                        </div> -->
                        <!-- route_in -->
                        <div class="row" style="display:none;" id="route_in">
                            <div class="col-12" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <label><?= $this->lang->line('routein'); ?></label>
                                <input name="reserve_route_name" id="reserve_route_name" class="form-control"
                                    readonly />
                                <select name="reserve_route" id="reserve_route" class="form-control select2"
                                    onchange="chkPoint();">
                                    <option value="">-- <?= $this->lang->line('select'); ?> --</option>
                                </select>
                            </div>
                        </div>

                        <!-- point_in -->
                        <div class="row" style="display:none;" id="point_in">
                            <div class="col-12" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <label><?= $this->lang->line('point'); ?></label>
                                <input name="reserve_point_name" id="reserve_point_name" class="form-control"
                                    readonly />
                                <select name="reserve_point" id="reserve_point" class="form-control select2">
                                    <option value=""> <?= $this->lang->line('select'); ?></option>
                                </select>
                            </div>
                        </div>

                        <!-- route_out -->
                        <div class="row" style="display:none;" id="route_out">
                            <div class="col-12" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <label><?= $this->lang->line('routeout'); ?></label>
                                <input name="reserve_route_out_name" id="reserve_route_out_name" class="form-control"
                                    readonly />
                                <select name="reserve_route_out" id="reserve_route_out" class="form-control select2"
                                    onchange="chkPointOut();">
                                    <option value=""> <?= $this->lang->line('select'); ?></option>
                                </select>
                            </div>
                        </div>

                        <!-- point_out -->
                        <div class="row" style="display:none;" id="point_out">
                            <div class="col-12" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <label><?= $this->lang->line('pointout'); ?></label>
                                <input name="reserve_point_out_name" id="reserve_point_out_name" type="text"
                                    class="form-control" readonly />
                                <select name="reserve_point_out" id="reserve_point_out" class="form-control select2">
                                    <option value=""> <?= $this->lang->line('select'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="row" style="padding-bottom:20px;">
                            <div class="col-6" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <button class="btn btn-secondary" style="width:100%;" onclick="backVal();">
                                    <?= $this->lang->line('cancel_setting'); ?>
                                </button>
                            </div>
                            <div class="col-6" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <button class="btn btn-primary" style="width:100%;" onclick="chkVal();">
                                    <?= $this->lang->line('confirm'); ?>
                                </button>
                            </div>
                        </div>
                        <div id="return_result"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="result_reservation" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="backDropModalTitle"><?= $this->lang->line('report_summary'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table" id="tb_result_reserve">
                    <thead>
                        <tr>
                            <th><?=$this->lang->line("date"); ?></th>
                            <th><?=$this->lang->line("trip"); ?></th>
							<!-- @Author: Natthanicha Pothiwat @Hide field Date : 28/06/2025 -->
                            <!-- <th><?=$this->lang->line("status"); ?></th> -->
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary"
                        data-bs-dismiss="modal"><?= $this->lang->line('close'); ?></button>
                    <button type="button" class="btn btn-primary"
                        onclick="historyVal();"><?= $this->lang->line('ok'); ?></button>
                </div> -->
				<div class="modal-footer">
                    <button type="button" class="btn btn-primary"
                        onclick="historyVal();"><?= $this->lang->line('done'); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

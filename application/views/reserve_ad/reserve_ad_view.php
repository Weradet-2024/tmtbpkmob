<br>
<br>
<br>
<br>
<div class="container">
    <div class="row align-items-top">
        <div class="col-12 hero-text-image">
            <div class="row">
                <div class="col-12">
                    <div style="border-radius:5px; background-color:#FFF;  padding:5px;" data-aos="fade-left">
                        <span class="section-heading row justify-content-center text-center mb-5 mb-5 fw-bold text-primary" data-aos="fade-up"
                            style="font-size:24px !important;"><?= $this->lang->line('reserve_ad_menu'); ?></span>
                        <div class="row">
                            <div class="col-12" style="padding-left:20px; padding-right:20px;">
                                <label><?= $this->lang->line('passenger'); ?></label>
                                <input name="reserve_name" id="reserve_name" type="text" class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                วันที่เข้ากะ
                                <label><?= $this->lang->line('start_date'); ?></label>
                                <input name="reserve_date" id="reserve_date" type="text" class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                วันที่เข้ากะ
                                <label><?= $this->lang->line('end_date'); ?></label>
                                <input name="reserve_date" id="reserve_date_end" type="text" class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <label>shift / กะทำงาน</label>
                                <select name="shift" id="shift" class="form-control"
                                    onchange="chkRoundTrip(this.value);">
                                    <option value="">-- <?= $this->lang->line('select'); ?> --</option>
                                    <option value="day">กะกลางวัน/Day shift</option>
                                    <option value="night">กะกลางคืน/Night shift</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <label><?= $this->lang->line('round'); ?></label>
                                <select name="round" id="round" class="form-control"
                                    onchange="chkTime(this.value);">
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
                        <div class="alert alert-info" style="text-align:center;" hidden>
                            <h6><?= $this->lang->line('routeauto1'); ?></h6>
                            <h6><?= $this->lang->line('routeauto2'); ?></h6>
                        </div>
                        <div class="row" style="display:none;" id="route_in">
                        <div class="col-12" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <label><?= $this->lang->line('routein'); ?></label>
                                <input name="reserve_route_name" id="reserve_route_name" class="form-control"
                                    readonly />
                                <select name="reserve_route" id="reserve_route" class="form-control"
                                    onchange="chkPoint();">
                                    <option value="">-- <?= $this->lang->line('select'); ?> --</option>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="display:none;" id="point_in">
                            <div class="col-12" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <label><?= $this->lang->line('point'); ?></label>
                                <input name="reserve_point_name" id="reserve_point_name" class="form-control"
                                    readonly />
                                <select name="reserve_point" id="reserve_point" class="form-control">
                                    <option value="">-- เลือก --</option>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="display:none;" id="route_out">
                            <div class="col-12" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <label><?= $this->lang->line('routeout'); ?></label>
                                <input name="reserve_route_out_name" id="reserve_route_out_name" class="form-control"
                                    readonly />
                                <select name="reserve_route_out" id="reserve_route_out" class="form-control"
                                    onchange="chkPointOut();">
                                    <option value="">-- เลือก --</option>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="display:none;" id="point_out">
                            <div class="col-12" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <label><?= $this->lang->line('pointout'); ?></label>
                                <input name="reserve_point_out_name" id="reserve_point_out_name" type="text"
                                    class="form-control" readonly />
                                <select name="reserve_point_out" id="reserve_point_out" class="form-control">
                                    <option value="">-- เลือก --</option>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="padding-bottom:20px;">
                            <div class="col-6" style="padding-left:20px; padding-right:20px; padding-top:10px;">
                                <button class="btn btn-secondary" style="width:100%;" onclick="backVal();">
                                    <?= $this->lang->line('back'); ?>
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
                <h5 class="modal-title" id="backDropModalTitle"><?=$this->lang->line('report_summary');?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table" id="tb_result_reserve">
                    <thead>
                        <tr>
                            <th><?= $this->lang->line('date'); ?></th>
                            <th><?= $this->lang->line('trip'); ?></th>
                            <th><?= $this->lang->line('status'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary"
                        data-bs-dismiss="modal"><?= $this->lang->line('close'); ?></button>
                    <button type="button" class="btn btn-primary" onclick="historyVal();"><?= $this->lang->line('ok'); ?></button>
                </div>
        </form>
    </div>
</div>

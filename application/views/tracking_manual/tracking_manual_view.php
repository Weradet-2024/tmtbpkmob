<div class="container" style="margin-top: 100px;">
    <div class="row align-items-top">
        <div class="col-12 hero-text-image">
            <div class="form-group row">
                <div class="col-12 text-center text-lg-start">
                    <div style="border-radius:5px;  padding:5px;" data-aos="fade-left">
                        
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div style="border-radius:5px; background-color:#FFF;  padding:15px; color:#000;"
                        data-aos="fade-left">
                        <div class="form-group">
                            <span><?= $this->lang->line('workdate'); ?> <span id="lbl_date"
                                    class="text-primary"></span></span>
                        </div>
                        <br>
                        <div class="form-group">
                            <label><?= $this->lang->line('trip'); ?></label>
                            <select name="tracking_manual_trip" id="tracking_manual_trip" class="form-control" onchange="getRouteData()">
                                <option value="">-- เลือก --</option>
                            </select>
                        </div>
                        <br>
                        <div class="form-group">
                            <label><?= $this->lang->line('route'); ?></label>
                            <select name="tracking_manual_route" id="tracking_manual_route" class="form-control">
                                <option value="">-- เลือก --</option>
                            </select>
                        </div>
                        <br>
                        <div class="form-group row">
                            <div class="col-12" data-aos="fade-right">
                                <button type="button" class="btn btn-primary" style="width:100%;" data-aos="fade-left"
                                    onclick="chkActiveRoute(this.value);">
                                    <?= $this->lang->line('show_data'); ?>
                                </button>
                            </div>
                        </div>
                        <br>
                        <div id="get_active_route" class="table-responsive">
                            <table id="tbActRoute" class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th><?= $this->lang->line('route'); ?></th>
                                        <th><?= $this->lang->line('round'); ?></th>
                                        <th><?= $this->lang->line('licenseplate'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="return_result"></div>
        </div>
    </div>
</div>
<br>
<br>
<br>
<br>

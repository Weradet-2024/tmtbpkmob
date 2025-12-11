<br>
<br>
<br>
<div class="container" style="margin-top:50px;">
    <div class="row align-items-top">
        <div class="col-12 hero-text-image">
            <div class="row">
                <div class="col-12">
                    <div style="border-radius:5px; background-color:#FFF;  padding:5px;" data-aos="fade-left">
                        <center>
                            <span class="section-heading" data-aos="fade-up"
                                style="font-size:24px !important;"><?= $this->lang->line('history_car'); ?></span>
                        </center>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <label><?= $this->lang->line('start_date'); ?></label>
                                <input name="get_date_start" id="get_date_start" type="text" class="form-control"
                                    style="height: 30px;" onchange="chkVal();">
                            </div>
                            <div class="col-6">
                                <label><?= $this->lang->line('end_date'); ?></label>
                                <input name="get_date_end" id="get_date_end" type="text" class="form-control"
                                    style="height: 30px;" onchange="chkVal();">
                            </div>


                        </div>
                        <div class="col-12">
                            <label><?= $this->lang->line('status'); ?></label>
                            <select name="get_status" id="get_status" class="form-control" style="height:40px;"
                                onchange="chkVal();">
                                <option value="All"><?= $this->lang->line('all'); ?></option>
                                <option value="Submitted">Confirm</option>
                                <option value="Waiting"><?= $this->lang->line('Waiting_Confirm'); ?></option>
                                <!-- <option value="Pending_cancel"><?= $this->lang->line('pending_cancel'); ?></option> -->
                                <option value="Cancelled"><?= $this->lang->line('cancel'); ?></option>
								<!-- <option value="Denied"><?= $this->lang->line('denied'); ?></option>  -->
                            </select>
                        </div>
                        <hr>
                        <div class="row Check-feature-user-cancel">
                            <div class="col-12" style="text-align:center; padding-top:5px; padding-bottom:5px;">
                                <button type="button" class="btn btn-danger btn-sm" style="padding:3px !important;"
                                    onclick="cancelSelect();">
                                    <?= $this->lang->line('cancel_select'); ?>
                                </button>
                            </div>
                        </div>
                        <form name="frmReserve" id="frmReserve" enctype="multipart/form-data">
                            <input name="chk_type" id="chk_type" type="hidden" value="cancelSelect" />
                            <input name='hdnMaxLine' id='hdnMaxLine' type='hidden' value='' />
                            <div class="table-responsive">
                                <table id='tbHistory' class='table table-striped table-hover' style='font-size:12px;'>
                                    <thead>
                                        <tr>
                                            <th style='width:5%;'><input name='chk_all' id='chk_all' type='checkbox' onchange="chkAll();" /></th>
                                            <th style='width:45%;'><?= $this->lang->line('date'); ?></th>
                                            <th style='width:15%;'><?= $this->lang->line('time'); ?></th>
                                            <th style='width:10%; text-align:center;'><?= $this->lang->line('trip_header'); ?></th>
                                            <th style='width:10%;'><?= $this->lang->line('status'); ?></th>
                                            <th style='width:10%;'><?= $this->lang->line('scan'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>

                            <div id="getContent" class="table-responsive">

                            </div>
                        </form>
                        <div id="return_result"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 
<script type="text/javascript">
    $('#get_date_start').datepicker({
        format: 'yyyy-mm-dd',
        todayHighlight: true,
        disableTouchKeyboard: true,
        autoclose: true
    });
    $('#get_date_end').datepicker({
        format: 'yyyy-mm-dd',
        todayHighlight: true,
        disableTouchKeyboard: true,
        autoclose: true
    });

    $('#tbHistory').dataTable({
        'bLengthChange': false,
        'ordering': false,
        'searching':false,
    });

</script> -->

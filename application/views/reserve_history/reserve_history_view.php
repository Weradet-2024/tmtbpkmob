<br>
<br>
<br>

<style>
    /* ===== Modern Table Style ===== */

    .table-modern {
        border-radius: 12px;
        overflow: hidden;
        font-size: 13px;
    }

    /* Header */
    .table-modern thead th {
        background: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
    }

    /* Row hover */
    .table-modern tbody tr:hover {
        background: #f5faff;
        transition: 0.2s;
    }

    /* Checkbox ใหญ่ขึ้น
    .table-modern input[type="checkbox"] {
        transform: scale(1.3);
        cursor: pointer;
    } */

    /* ===== ขยายช่องวันที่ ===== */

    #tbHistory th:nth-child(2),
    #tbHistory td:nth-child(2) {
        min-width: 60px;
        /* ⭐ ขยายช่องวันที่ */
        white-space: nowrap;
        /* ⭐ ห้ามขึ้นบรรทัดใหม่ */
    }

       #tbHistory th:nth-child(1),
    #tbHistory td:nth-child(1) {
        max-width: 20px;
    }

/* ===== MOBILE FIRST ===== */

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* ทำให้อ้วนขึ้น */
#tbHistory input[type="checkbox"] {
    transform: scale(1.5);   /* ปรับความอ้วน */
    cursor: pointer;
}

/* จัดให้อยู่กลาง cell */
#tbHistory td:first-child,
#tbHistory th:first-child {
    text-align: center;
    vertical-align: middle;
}


/* ลด padding ให้เล็กลงบนมือถือ */
@media (max-width: 768px) {

    #tbHistory th,
    #tbHistory td {
        padding: 6px 8px !important;
        font-size: 12px;
        white-space: nowrap; /* กันแตกบรรทัด */
    }

    /* ทำ checkbox เล็กลง */
    #tbHistory input[type="checkbox"] {
        transform: scale(0.9);
    }

    /* จำกัดความกว้าง column */
    #tbHistory th:nth-child(1),
    #tbHistory td:nth-child(1) {
        width: 40px;
    }

    #tbHistory th:nth-child(2),
    #tbHistory td:nth-child(2) {
        min-width: 80px;
    }

    #tbHistory th:nth-child(3),
    #tbHistory td:nth-child(3) {
        min-width: 30px;
    }

    #tbHistory th:nth-child(4),
    #tbHistory td:nth-child(4) {
        min-width: 30px;
    }

    #tbHistory th:nth-child(5),
    #tbHistory td:nth-child(5) {
        min-width: 90px;
    }

    #tbHistory th:nth-child(6),
    #tbHistory td:nth-child(6) {
        min-width: 60px;
    }

}

</style>
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
                                <table id='tbHistory' class='table table-striped table-hover table-modern'>
                                    <thead>
                                        <tr>
                                            <th><input id='chk_all' type='checkbox' onchange="chkAll();" /></th>
                                            <th><?= $this->lang->line('date'); ?></th>
                                            <th><?= $this->lang->line('time'); ?></th>
                                            <th class="text-center"><?= $this->lang->line('trip_header'); ?></th>
                                            <th><?= $this->lang->line('status'); ?></th>
                                            <th><?= $this->lang->line('scan'); ?></th>
                                        </tr>
                                    </thead>


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
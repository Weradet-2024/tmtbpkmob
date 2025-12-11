<style>
	.swal2-container {
		z-index: 9999 !important; /* ทำให้ SweetAlert อยู่ด้านหน้า */
	}
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

    .select2-container {
      z-index: 99999 !important;
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
<br>
<br>
<!-- Main Content -->
<div class="container my-5 mt-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <!-- Card Wrapper with List Group -->
      <div class="card" data-aos="fade-left">
        <div class="card-body">
          <ul class="list-group list-group-flush" style="font-size: 14px; font-family: 'Sarabun', 'Arial', sans-serif;">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <i class="bi bi-building text-primary me-2"></i>
                <?=$this->lang->line('company_name'); ?> :
                <span id="lbl_com_name">Company Name</span>
              </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <i class="bi bi-person-circle text-primary me-2"></i>
                <?=$this->lang->line('username'); ?> :
                <span id="lbl_emp_code">Employee Code</span>
              </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <i class="bi bi-person text-primary me-2"></i>
                <?=$this->lang->line('passenger'); ?> :
                <span id="lbl_emp_name">Employee Name</span>
              </div>
              <button class="btn btn-outline-primary btn-sm" type="button" onclick="edName()">
                <?=$this->lang->line('edit'); ?>
              </button>
            </li>
            <!-- Telephone (commented out)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <i class="bi bi-telephone text-primary me-2"></i>
                <span id="lbl_emp_tel">Employee Telephone</span>
              </div>
              <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal_info_tel">
                <?=$this->lang->line('edit'); ?>
              </button>
            </li> -->
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <i class="bi bi-geo-alt text-primary me-2"></i>
                <?=$this->lang->line('route_in_fav'); ?> :
                <span id="lbl_route_name">Route Name</span>
              </div>
              <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal_route">
                <?=$this->lang->line('edit'); ?>
              </button>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <i class="bi bi-geo-alt text-primary me-2"></i>
                <?=$this->lang->line('pickup_point_fav'); ?> :
                <span id="lbl_pickup_name">Pickup Name</span>
              </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <i class="bi bi-geo-fill text-primary me-2"></i>
                <?=$this->lang->line('route_out_fav'); ?> :
                <span id="lbl_route_out_name">Route Out Name</span>
              </div>
              <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal_route_out">
                <?=$this->lang->line('edit'); ?>
              </button>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <i class="bi bi-geo-fill text-primary me-2"></i>
                <?=$this->lang->line('dropoff_point_fav'); ?> :
                <span id="lbl_dropoff_name">Dropoff Name</span>
              </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <i class="bi bi-key-fill text-primary me-2"></i>
                <span id="lbl_reset"><?=$this->lang->line('reset_password'); ?></span>
              </div>
              <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modal_info_pw">
         
								<?=$this->lang->line('edit'); ?>
              </button>
            </li>
          </ul>
          <div id="return_result" class="mt-3"></div>
        </div>
      </div>
    </div>
  </div>
</div>


  <!-- Back Button -->
  <div class="container text-center mb-5">
    <button type="button" class="btn btn-secondary" style="width:200px;" onclick="backVal();">
      <?=$this->lang->line('back'); ?>
    </button>
  </div>

  <!-- ================== -->
  <!--      Modals      -->
  <!-- ================== -->

  <!-- Modal: Edit Name -->
  <div class="modal fade" id="modal_ed_name" tabindex="-1" aria-labelledby="modalEdNameLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEdNameLabel"><?=$this->lang->line('name'); ?> - <?=$this->lang->line('lname'); ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?=$this->lang->line('cancel_setting'); ?>"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-lg-6">
              <label><?=$this->lang->line('name'); ?></label>
              <input name="modal_ed_emp_id" id="modal_ed_emp_id" type="hidden" />
              <input name="modal_ed_emp_name" id="modal_ed_emp_name" type="text" class="form-control" />
            </div>
            <div class="col-lg-6">
              <label><?=$this->lang->line('lname'); ?></label>
              <input name="modal_ed_emp_lname" id="modal_ed_emp_lname" type="text" class="form-control" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal"><?=$this->lang->line('cancel_setting'); ?></button>
          <button class="btn btn-warning" onclick="udName();"><?=$this->lang->line('update'); ?></button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal: Edit Telephone -->
  <div class="modal fade" id="modal_info_tel" tabindex="-1" aria-labelledby="modalInfoTelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalInfoTelLabel"><?=$this->lang->line('tel'); ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?=$this->lang->line('cancel_setting'); ?>"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-12">
              <input name="modal_info_tel_id" id="modal_info_tel_id" type="hidden" />
              <input name="modal_info_tel_tel" id="modal_info_tel_tel" type="text" class="form-control" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal"><?=$this->lang->line('cancel_setting'); ?></button>
          <button class="btn btn-success" onclick="udTel();"><?=$this->lang->line('update'); ?></button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal: Change Password -->
  <div class="modal fade" id="modal_info_pw" tabindex="-1" aria-labelledby="modalInfoPwLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalInfoPwLabel"><?=$this->lang->line('chg_password'); ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?=$this->lang->line('cancel_setting'); ?>"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <input name="modal_info_pw_id" id="modal_info_pw_id" type="text" hidden/>
              <input name="modal_info_pw_pname" id="modal_info_pw_pname" type="password" class="form-control" placeholder="<?=$this->lang->line('new_password'); ?>" />
            </div>
            <div class="col-12">
              <input name="modal_info_pw_cname" id="modal_info_pw_cname" type="password" class="form-control" placeholder="<?=$this->lang->line('confirm_password'); ?>" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal"><?=$this->lang->line('cancel_setting'); ?></button>
          <button class="btn btn-success" onclick="udPw();"><?=$this->lang->line('update'); ?></button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal: Edit Route (Trip In) -->
  <div class="modal fade" id="modal_route" tabindex="-1" aria-labelledby="modalRouteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalRouteLabel"><?=$this->lang->line('defult'); ?> <?=$this->lang->line('trip_in'); ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?=$this->lang->line('cancel_setting'); ?>"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label><?=$this->lang->line('route'); ?></label>
              <input name="modal_route_emp_id" id="modal_route_emp_id" type="hidden" />
              <select name="modal_route_id" id="modal_route_id" class="form-select select2" onchange="modalRouteChkRoute(this.value);">
                <!-- Options -->
              </select>
            </div>
            <div class="col-12">
              <label><?=$this->lang->line('point'); ?></label>
              <select name="modal_pick_id" id="modal_pick_id" class="form-select select2">
                <!-- Options -->
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=$this->lang->line('cancel_setting'); ?></button>
          <button type="button" class="btn btn-success" id="udRoute" onclick="udRoute();"><?=$this->lang->line('update'); ?></button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal: Edit Route Out (Trip Out) -->
  <div class="modal fade" id="modal_route_out" tabindex="-1" aria-labelledby="modalRouteOutLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalRouteOutLabel"><?=$this->lang->line('defult,trip_out'); ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?=$this->lang->line('cancel_setting'); ?>"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label><?=$this->lang->line('route');?></label>
              <input name="modal_route_out_emp_id" id="modal_route_out_emp_id" type="hidden" />
              <select name="modal_route_out_id" id="modal_route_out_id" class="form-select select2" onchange="modalRouteOutChkRoute(this.value);">
                <!-- Options -->
              </select>
            </div>
            <div class="col-12">
              <label><?=$this->lang->line('point'); ?></label>
              <select name="modal_drop_id" id="modal_drop_id" class="form-select select2">
                <!-- Options -->
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=$this->lang->line('cancel_setting'); ?></button>
          <button type="button" class="btn btn-success" id="udRouteOut" onclick="udRouteOut();"><?=$this->lang->line('update'); ?></button>
        </div>
      </div>
    </div>
  </div>

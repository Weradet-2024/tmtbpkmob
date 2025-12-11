<style>
    .modal-dialog {
        max-width: 100%;
    }

    .modal-content {
        max-height: 80vh;
        /* Limits modal height */
        overflow-y: auto;
        /* Adds vertical scrolling */
    }

    .table-responsive {
        overflow-x: auto;
        /* Ensures the table can be scrolled horizontally */
    }

    .table-striped {
        font-size: 12px !important;
    }
    
    .upload-box {
        border: 2px dashed #007bff;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        border-radius: 10px;
        transition: 0.3s;
    }

    .upload-box:hover {
        background: #f8f9fa;
    }

    .preview-container {
        margin-top: 15px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .preview-item {
        position: relative;
        display: inline-block;
    }

    .preview-item img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 5px;
        border: 1px solid #ddd;
    }

    .remove-btn {
        position: absolute;
        top: -5px;
        right: -5px;
        background: red;
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 12px;
        cursor: pointer;
    }
	.required {
        color: #ff0000;
        margin: 1px;
        font-size: 12px;
    }
</style>

<br>
<div class="container-fluid mt-5" style="max-height: 90vh;">
    <div class="row">
        <div class="col-12">
            <div style="border-radius:5px; background-color:#FFF; padding:5px;" data-aos="fade-left">
                <span class="section-heading row justify-content-center text-center mb-5" data-aos="fade-up"
                    style="font-size:24px !important;"><?=$this->lang->line('suggestion'); ?></span>
                <div class="row">
                    <div class="col-6">
                        <label><?= $this->lang->line('month'); ?></label>
                        <select name="get_month" id="get_month" class="form-control" onchange="chkNewVal();">
                            <option value="">-- เลือก --</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label><?= $this->lang->line('year'); ?></label>
                        <select name="get_year" id="get_year" class="form-control" onchange="chkNewVal();">
                            <option value="">-- เลือก --</option>
                        </select>
                    </div>
                </div>
                <hr>
                <div class="table-responsive">
                    <table id='tbComment' class='table table-striped table-sm'>
                        <thead>
                            <tr>
                                <th><?= $this->lang->line('no'); ?></th>
                                <th><?= $this->lang->line('date'); ?></th>
                                <th><?= $this->lang->line('topic'); ?></th>
                                <th><?= $this->lang->line('status'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="matgin-bottom: 50rem !important;">
        <div class="col-12">
            <button class="btn btn-primary w-100" onclick="modal_comment();">
                <?= $this->lang->line('new'); ?>
            </button>
        </div>
    </div>
</div>


    <div class="modal fade" id="modal_comment" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-primary"><?=$this->lang->line('add')." ".$this->lang->line('comments'); ?></h5>
                    </div>
                    <div class="modal-body">
                        <div class="row mt-2">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label><?=$this->lang->line('topic'); ?></label>&nbsp;<span class="required">*</span>
                                    <select name="modal_type" id="modal_type" class="form-control" onchange="chkType(this.value);">
                                        <option value="">--<?=$this->lang->line('select');?>--</option>
                                        <option value="comment"><?=$this->lang->line('recommend');?></option>
                                        <!-- <option value="complaint"><?=$this->lang->line('complaint');?></option> -->
                                        <option value="compliment"><?=$this->lang->line('compliment');?></option>
                                    </select>
									<span id="error_add_type_comment" style="color: red;"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2 mb-3" id="subject_modal" style="display:none;">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label><?= $this->lang->line('subject'); ?></label>&nbsp;<span class="required">*</span>
                                    <input name="modal_subject" id="modal_subject" type="text" class="form-control" />
                                    <span id="error_add_subject" style="color: red;"></span>
                                </div>
                            </div>
                        </div>

						<div class="row mt-2 mb-3" id="route_modal" style="display:none;">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label><?=$this->lang->line('route');?></label>&nbsp;<span class="required">*</span>
                                    <select name="modal_route" id="modal_route" class="form-control">
                                        <option value=""><?=$this->lang->line('select');?></option>
                                    </select>
									<span id="error_add_route" style="color: red;"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2 mb-3" id="time_modal" style="display:none;">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label><?=$this->lang->line('round');?></label>&nbsp;<span class="required">*</span>
                                    <select name="modal_time" id="modal_time" class="form-control">
                                        <option value=""><?=$this->lang->line('select');?></option>
                                    </select>
									<span id="error_add_time" style="color: red;"></span>
                                </div>
                            </div>
                        </div>

						<div class="row mt-2 mb-3" id="license_plate_modal" style="display:none;">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label><?= $this->lang->line('license_plate'); ?></label>
                                    <input name="modal_license_plate" id="modal_license_plate" type="text" class="form-control" />
                                 
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2 mb-3" id="detail_modal" style="display:none;">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label><?=$this->lang->line('detail');?></label>&nbsp;<span class="required">*</span>
                                    <textarea name="modal_detail" id="modal_detail" class="form-control" style="height: 150px;"></textarea>
                                    <span id="error_add_detail"></span>
                                </div>
                            </div>
                        </div>
                       
                        <div class="row mt-2 mb-3" id="type_modal" style="display:none;">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label><?=$this->lang->line('condition');?></label>
                                    <select name="modal_type_com" id="modal_type_com" class="form-control">
                                        <option value=""><?=$this->lang->line('select');?></option>
                                        <option value="condition"><?=$this->lang->line('condition');?></option>
                                        <option value="driver"><?=$this->lang->line('driver');?></option>
                                        <option value="coordinator"><?=$this->lang->line('coordinator');?></option>
                                        <option value="other"><?=$this->lang->line('other');?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2 mb-3" id="upload_modal" style="display:none;">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label><?=$this->lang->line('upload_file');?></label>
                                    <div class="upload-box" id="dropArea">
                                        <p><?=$this->lang->line('ex_upload');?></p>
                                        <!-- <input type="file" id="fileUpload" name="fileUpload" multiple hidden> -->
										<input type="file" id="fileUpload" name="fileUpload">
                                    </div>
                                    <div class="preview-container" id="preview"></div>
                                </div>
                            </div>
                        </div>
                
                    <div class="modal-footer">
                        <button class="btn btn-secondary" onclick="$('#modal_comment').modal('hide');"><?=$this->lang->line('close');?></button>
                        <button class="btn btn-success" id="btn-comment" onclick="comment();"><?=$this->lang->line('confirm');?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>


<!-- history comment feedback -->
    <div class="modal fade" id="modal_comment_feedback">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?=$this->lang->line('detail')." ".$this->lang->line('comments'); ?></h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-6" id="subject_modal_feedback" style="display:none;">
                            <div class="row">
                                <label class="col-3 col-form-label text-end fw-bold"><?= $this->lang->line('topic'); ?></label>
                                <label id="modal_subject_feedback" class="col-9 col-form-label text-secondary"></label>
                            </div>
                        </div>
                        
						<!-- date inform -->
                        <div class="form-group col-lg-6" id="date_modal_feedback" style="display:none;">
                            <div class="row">
                                <label class="col-3 col-form-label text-end fw-bold"><?= $this->lang->line('date'); ?></label>
                                <label id="modal_date_feedback" class="col-9 col-form-label text-secondary"></label>
                            </div>
                        </div>
						<!-- time inform -->
						<div class="form-group col-lg-6" id="time_modal_feedback" style="display:none;">
                            <div class="row">
                                <label class="col-3 col-form-label text-end fw-bold"><?= $this->lang->line('time'); ?></label>
                                <label id="modal_time_feedback" class="col-9 col-form-label text-secondary"></label>
                            </div>
                        </div>
						<!-- rotue -->
						<div class="form-group col-lg-6" id="route_modal_feedback" style="display:none;">
                            <div class="row">
                                <label class="col-3 col-form-label text-end fw-bold"><?= $this->lang->line('route'); ?></label>
                                <label id="modal_route_feedback" class="col-9 col-form-label text-secondary"></label>
                            </div>
                        </div>
						<!-- trip time -->
						<div class="form-group col-lg-6" id="trip_time_modal_feedback" style="display:none;">
                            <div class="row">
                                <label class="col-3 col-form-label text-end fw-bold"><?= $this->lang->line('trip_time'); ?></label>
                                <label id="modal_trip_time_feedback" class="col-9 col-form-label text-secondary"></label>
                            </div>
                        </div>
						<!-- License Plate -->
						<div class="form-group col-lg-6" id="license_plate_modal_feedback" style="display:none;">
                            <div class="row">
                                <label class="col-3 col-form-label text-end fw-bold"><?= $this->lang->line('license_plate'); ?></label>
                                <label id="modal_license_plate_feedback" class="col-9 col-form-label text-secondary"></label>
                            </div>
                        </div>


                        <div class="form-group col-lg-6" id="detail_modal_feedback" style="display:none;">
                            <div class="row">
                                <label class="col-3 col-form-label text-end fw-bold"><?= $this->lang->line('detail'); ?></label>
                                <label id="modal_detail_feedback" class="col-9 col-form-label text-secondary"></label>
                            </div>
                        </div>

                        <div class="row">
                            <label class="col-3 col-form-label text-end fw-bold">
                                <?= $this->lang->line('feedback'); ?>
                            </label>
                            <div class="col-9">
                                <textarea id="comment_feedback" class="col-9 form-control" rows="5" disabled></textarea>
                            </div>
                        </div>

                        

                        <div class="row mt-3">
                            <label class="col-3 col-form-label text-end fw-bold">
                                <?=$this->lang->line('file'); ?>
                            </label>
                            <div class="col-9">
                                <div id="modal_file_user_feedback">
                                    <img id="previewImageUser" src="#" class="img-thumbnail" style="max-width: 300px; display: none;">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <label class="col-3 col-form-label text-end fw-bold"></label>
                            <label id="comment_feedback_detail" class="col-9 col-form-label text-secondary"></label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="javascript:$('#modal_comment_feedback').modal('hide');">
                        <?= $this->lang->line('close'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <script>
        $('#tbComment').dataTable({
            'bLengthChange': false,
            'ordering': false,
            'searching': false,
            'pageLength': 5,
        });
    </script>

<script>
document.getElementById("dropArea").addEventListener("click", function () {
    document.getElementById("fileUpload").click();
});

document.getElementById("fileUpload").addEventListener("change", function (event) {
    handleFiles(event.target.files);
});

document.getElementById("dropArea").addEventListener("dragover", function (event) {
    event.preventDefault();
    this.style.background = "#e9ecef";
});

document.getElementById("dropArea").addEventListener("dragleave", function () {
    this.style.background = "#fff";
});

document.getElementById("dropArea").addEventListener("drop", function (event) {
    event.preventDefault();
    this.style.background = "#fff";
    handleFiles(event.dataTransfer.files);
});

function handleFiles(files) {
    let preview = document.getElementById("preview");
    preview.innerHTML = "";

    for (let file of files) {
        let reader = new FileReader();
        reader.onload = function (e) {
            let div = document.createElement("div");
            div.className = "preview-item";

            let img = document.createElement("img");
            img.src = e.target.result;

            let removeBtn = document.createElement("button");
            removeBtn.innerHTML = "×";
            removeBtn.className = "remove-btn";
            removeBtn.onclick = function () {
                div.remove();
            };

            div.appendChild(img);
            div.appendChild(removeBtn);
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    }
}
</script>

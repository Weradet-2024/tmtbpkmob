$(document).ready(function () {
	getData();
	$("#modal_date").datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		disableTouchKeyboard: true,
		autoclose: true,
	});
});

function getData() {
	$.ajax({
		type: "POST",
		url: "Comment_history/get_data",
		success: function (response) {
			json_data = JSON.parse(response);
			set_date_inselection(
				json_data.data.arr_month_name,
				json_data.data.query_str_year,
				json_data.data.curr_year,
				json_data.data.curr_month
			);
			chkVal(json_data.data.curr_month, json_data.data.curr_year);
		},
	});
}

function set_date_inselection(arr_month_name, str_year, curr_year, curr_month) {
	//set data date in selection
	var get_month = document.getElementById("get_month");
	$("#get_month").empty();
	var option = document.createElement("option");
	option.value = "";
	option.text = "-- เลือก --";
	get_month.appendChild(option);
	for (i = 1; i <= 12; i++) {
		str_num = str_i = i.toString();
		month_num = str_num.padStart(2, "0");
		month_name = arr_month_name[i];
		var option = document.createElement("option");
		option.value = month_num;
		option.text = month_name;
		if (month_num == curr_month) {
			option.setAttribute("selected", "selected");
		}
		get_month.appendChild(option);
	}
	//set year on selection
	str_year.forEach(function (row, index) {
		if (str_year != "") {
			str_year_i = row;
			var option = document.createElement("option");
			option.value = row;
			option.text  = row;
			if (str_year_i == curr_year) {
				option.setAttribute("selected", "selected");
			}
			get_year.appendChild(option);
		}
	});
}

function chkVal(month, year) {
	$.ajax({
		type: "POST",
		url: "Comment_history/chk_val",
		data: { month: month, year: year },
		success: function (response) {
			json_data = JSON.parse(response);
			//set data in table
			set_data_in_table(json_data.data);
		},
	});
}

function set_data_in_table(data) {
	var tbComment = $("#tbComment").DataTable();
	tbComment.clear().draw();
	if (data != undefined) {
		data.forEach((row, index) => {
			no = index + 1;
			comment_id = row.comment_id;
			comment_reg_date = row.comment_reg_date;
			comment_subject = row.comment_subject;
			comment_status = row.comment_status;
			if (comment_status == "feedback") {
				btn_info =
					'<button class="btn-success btn-sm bth-success" onclick="infoVal(\'' +
					comment_id +
					'\')" data-href="' +
					comment_id +
					'" >';
				btn_info += comment_status;
				btn_info += "</button>";
			} else if (comment_status == "open") {
				btn_info =
					'<button class="btn-info btn-sm bth-info" onclick="infoVal(\'' +
					comment_id +
					'\')" data-href="' +
					comment_id +
					'" >';
				btn_info += comment_status;
				btn_info += "</button>";
			}
			tbComment.row
				.add([no, comment_reg_date, comment_subject, btn_info])
				.draw();
		});
	}
}

function chkNewVal() {
	var month = document.getElementById("get_month").value;
	var year = document.getElementById("get_year").value;
	chkVal(month, year);
}

function modal_comment() {
	$("#modal_comment").modal("show");
}

function comment() {
	var modal_type = $('#modal_type').val();
    var modal_subject = $("#modal_subject").val();
    var modal_detail = $("#modal_detail").val();
	var modal_route = $('#modal_route').val();
	var modal_time = $('#modal_time').val();
	var modal_type_com = $('#modal_type_com').val();

    var error_subject = document.getElementById("error_add_subject");
    var error_detail = document.getElementById("error_add_detail");

    // ลบข้อความผิดพลาดก่อนหน้านี้
    error_subject.innerHTML = "";
    error_detail.innerHTML = "";


	if (modal_type == "comment"){
		if (modal_subject.trim() == "") {
			error_subject.innerHTML = "<p style='color: red;'>กรุณากรอกหัวข้อ</p>";
			return; // หยุดการทำงานของฟังก์ชันหากไม่กรอกข้อมูล
		}

		if (modal_detail.trim() == "") {
			error_detail.innerHTML = "<p style='color: red;'>กรุณากรอกรายละเอียด</p>";
			return; // หยุดการทำงานของฟังก์ชันหากไม่กรอกข้อมูล
		}
	}else if (modal_type == "complaint") {
		if (modal_subject.trim() == "") {
			error_subject.innerHTML = "กรุณากรอกหัวข้อ";
			return;
		}

		if (modal_detail.trim() == ""){
			error_subject.innerHTML = "กรุณากรอกรายละเอียด";
			return;
		}
	}


    var formData = new FormData();
    formData.append("modal_subject", modal_subject);
    formData.append("modal_detail", modal_detail);
    formData.append("fileUpload", document.getElementById("fileUpload").files[0]);

    fetch("Comment_history/comment", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {

			$('#modal_comment').modal('hide');

            Swal.fire({
                title: 'สำเร็จ!',
                text: 'อัปโหลดสำเร็จ!',
                icon: 'success',
                confirmButtonText: 'ตกลง'
            }).then(() => {
                // เคลียร์ค่าฟอร์ม
                document.getElementById("modal_subject").value = "";
                document.getElementById("modal_detail").value = "";
                document.getElementById("fileUpload").value = "";  // เคลียร์ไฟล์ที่เลือก
                
                // ลบข้อความผิดพลาดที่เคยแสดง
                error_subject.innerHTML = "";
                error_detail.innerHTML = "";

				getData();
            });
        } else {
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: 'เกิดข้อผิดพลาด: ' + data.message,
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
        }
    })
    .catch(error => console.error("Error:", error));
}

function chkType(type){
	$('#modal_subject').val("");
	$('#modal_detail').val("");
	$('#modal_subject').val("");
	$('#modal_route').val("").trigger("change");
	$('#modal_time').val("").trigger("change");
	$('#modal_type_com').val("").trigger("change");

	if(type == "comment"){
		$('#subject_modal').show();
		$('#detail_modal').show();
		$('#upload_modal').show();
		$('#route_modal').hide();
		$('#time_modal').hide();
		$("#type_modal").hide();
	}else if(type == "complaint"){
		$('#subject_modal').show();
		$('#route_modal').show();
		$('#time_modal').show();
		$("#detail_modal").show();
		$('#type_modal').show();
		$('#upload_modal').show();
	}else if (type == "compliment"){
		$("#subject_modal").show();
		$("#route_modal").show();
		$("#upload_modal").show();
		$("#time_modal").show();
		$("#detail_modal").show();
		$('#type_modal').hide();
	}else{
		$('#subject_modal').hide();
		$('#detail_modal').hide();
		$('#route_modal').hide();
		$('#time_modal').hide();
		$("#type_modal").hide();
		$('#upload_modal').hide();
	}
}

function infoVal(comment_id) {
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "Comment_history/info_val",
        data: { comment_id: comment_id },
        success: function (data) {
            console.log(data);
            
            if (data.result[0].comment_status != "open") {
                var name_feedback =
                    data.result[0].comment_feedback_date +
                    " " +
                    data.result[0].comment_feedback_time +
                    " (" +
                    data.result[0].emp_name +
                    " " +
                    data.result[0].emp_lname +
                    " )";
            } else {
                var name_feedback = "";
            }

            if (data.status === "success") {
                $("#subject_modal_feedback").show();
                $("#date_modal_feedback").show();
                $("#time_modal_feedback").show();
                $("#detail_modal_feedback").show();
                $("#file_modal_feedback").show();

                $("#modal_subject_feedback").text(data.result[0].comment_subject);
                $("#modal_date_feedback").text(data.result[0].comment_reg_date);
                $("#modal_time_feedback").text(data.result[0].comment_reg_time);
                $("#modal_detail_feedback").text(data.result[0].comment_detail);

                var fileURL = data.result[0].comment_feedback_filepath;
                var fileLink = document.getElementById("fileLink");
                var previewImage = document.getElementById("previewImage");

				var fileURLUser = data.result[0].comment_user_file_path;
                var previewImageUser = document.getElementById("previewImageUser");

                // if (fileURL) {
                //     let fileExtension = fileURL.split('.').pop().toLowerCase();

                //     if (["jpg", "jpeg", "png", "gif", "bmp", "webp"].includes(fileExtension)) {
                //         previewImage.src = fileURL;
                //         previewImage.style.display = "block";
                //         fileLink.style.display = "none";
                //     } else {
                //         fileLink.href = fileURL;
                //         fileLink.textContent = "ดาวน์โหลดไฟล์";
                //         fileLink.style.display = "block";
                //         previewImage.style.display = "none";
                //     }
                // } else {
                //     previewImage.style.display = "none";
                //     fileLink.style.display = "none";
                // }

				if(fileURLUser  != null && previewImageUser) {
					let fileExtension = fileURLUser.split('.').pop().toLowerCase();
					// เอา index.php ออกจาก URL (ถ้ามี)
					let baseURL = "https://demoetvmobile.tttpdxservice.com/";

					fileURLUser = fileURLUser.replace("/index.php", "");
					
					if (["jpg", "jpeg", "png", "gif", "bmp", "webp"].includes(fileExtension)) {
                        previewImageUser.src = baseURL  + fileURLUser;
                        previewImageUser.style.display = "block";
                    } 
				}else {
					previewImageUser.style.display = "none";

				}

                $("#comment_feedback").val(data.result[0].comment_feedback);
                $("#comment_feedback_detail").text(name_feedback);
                $("#modal_comment_feedback").modal("show");
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Fail!',
                    text: 'ไม่สามารถดึงข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'เกิดข้อผิดพลาดในการดึงข้อมูล',
            });
        }
    });
}

function set_time_in_selection() {
	$.ajax({
		type: "POST",
		url: "Comment_history/get_shift",
		success: function (response) {
			json_data = JSON.parse(response);
			var modal_time = document.getElementById("modal_time");
			$("#modal_time").empty();
			var option   = document.createElement("option");
				option.value = "";
				option.text  = "---เลือก--- ";
				modal_time.appendChild(option);
			json_data.data.forEach((row, index) => {
				var option   = document.createElement("option");
				option.value = row.shift_text + " " + row.shift_type;
				option.text  = row.shift_text + " " + row.shift_type;
				modal_time.appendChild(option);
			});
		},
		error: function (error) {
			console.error(error);
		},
	});
}

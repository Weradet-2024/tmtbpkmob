$(document).ready(function () {
	//chkFeature();
	getData();
	getNightShift();
	$("#get_date_start").datepicker({
		orientation:"button",
		format: "yyyy-mm-dd",
		todayHighlight: true,
		disableTouchKeyboard: true,
		autoclose: true,
	});
	$("#get_date_end").datepicker({
		orientation:"button",
		format: "yyyy-mm-dd",
		todayHighlight: true,
		disableTouchKeyboard: true,
		autoclose: true,
	});

	$("#tbHistory").dataTable({
		bLengthChange: false,
		ordering: false,
		searching: false,
	});
});

var arr_night_shift = [];

// function chkFeature(){
// 	$.ajax({
// 		type: "POST",
// 		url: "Reserve_history/chkFeature",
// 		success: function(response){
// 			console.log(response);
// 			var obj = JSON.parse(response);

// 			if (obj.length > 0){
// 				sessionStorage.setItem('session_feature_set_item_user_cancel', obj[0].feature_set_item_user_cancel);

// 				if (obj[0].feature_set_item_user_cancel === "active"){
// 					$('.Check-feature-user-cancel').show();
// 					$('#chk_all').prop('disabled', false);
					
// 				}else{
// 					$('.Check-feature-user-cancel').hide();
// 					$('#chk_all').prop('disabled', true);

// 				}
// 			}

// 		//console.log( obj[0].feature_set_item_user_cancel);
// 		},	
// 		error: function (error){
// 			console.error(error);
// 		},
// 	});
// }
function getCookie(name) {
	var value = "; " + document.cookie;
	var parts = value.split("; " + name + "=");
	if (parts.length == 2) return parts.pop().split(";").shift();
}

function getNightShift() {
	$.ajax({
		type: "POST",
		url: "Reserve_history/get_trip_out_time",
		success: function (response) {
			json_data = JSON.parse(response);
			console.log(json_data);
			arr_night_shift = json_data;
		},
		error: function (error) {
			console.error(error);
		},
	});
}

function getData() {
	$.ajax({
		type: "POST",
		url: "Reserve_history/get_data",
		success: function (response) {
			json_data = JSON.parse(response);
			set_data_in_layout(json_data);
		},
		error: function (error) {
			console.error(error);
		},
	});
}

// function set_data_in_layout(json_data) {
// 	const ck_lang = getCookie("ck_emp_lang");

// 	let waiting = "Waiting Confirm";
// 	let confirm = "Confirm";
// 	if (ck_lang == "th") {
// 		waiting = "รอ Confirm";
// 		confirm = "จองสำเร็จ";
// 	} else {
// 		waiting = "Waiting Confirm";
// 		confirm = "Confirm";
// 	}

// 	var session_feature_set_item_user_cancel = sessionStorage.getItem('session_feature_set_item_user_cancel');
// 	var query_next = json_data.query_next;
// 	var last_reserve_date = "";

// 	console.log(query_next);

// 	// const trip_out_time = ["05:05", "08:00"];

// 	var tbHistory = $("#tbHistory").DataTable();

// 	tbHistory.clear().draw();

// 	if (json_data.reserve != undefined && json_data.reserve != null) {
// 		console.log(json_data);
// 		json_data.reserve.forEach((row, index) => {
// 			var reserve_id = row.reserve_id;
// 			var reserve_date = row.reserve_date;
// 			var reserve_round = row.reserve_round;
// 			var reserve_time = row.reserve_time;
// 			var reserve_trip = row.reserve_trip;
// 			var reserve_shift = row.reserve_shift;
// 			var reserve_status = row.reserve_status;
// 			var attendance_time = row.attendance_time;

// 			chk_box = "";
// 			//if (session_feature_set_item_user_cancel === "active"){

// 			if (reserve_date < json_data.curr_date) {
// 				chk_box =
// 					'<input name="chk_reserve_' +
// 					(index + 1) +
// 					'" id="chk_reserve_' +
// 					(index + 1) +
// 					'" type="checkbox" value="' +
// 					reserve_id +
// 					'" disabled/>';
// 			} else if (reserve_date == json_data.curr_date) {
			
// 				chk_box =
// 					'<input name="chk_reserve_' +
// 					(index + 1) +
// 					'" id="chk_reserve_' +
// 					(index + 1) +
// 					'" type="checkbox" value="' +
// 					reserve_id +
// 						'"/>';

// 			} else {
// 				chk_box =
// 					'<input name="chk_reserve_' +
// 					(index + 1) +
// 					'" id="chk_reserve_' +
// 					(index + 1) +
// 					'" type="checkbox" value="' +
// 					reserve_id +
// 					'"/>';
// 			}
// 			//}


// 			//index
// 			//reserve_date
// 			//reserve_time

// 			text_info = "";
// 			if (reserve_trip == "in") {
// 				text_info = '<span class="text-info">In</span>';
// 			} else if (reserve_trip == "out") {
// 				text_info = '<span class="text-warning">Out</span>';
// 			}

// 			btn_info = "";
// 			if (reserve_status == "Waiting") {
// 				btn_info =
// 					'<a type="button" class="btn-sm btn-warning" style="color:white;" onclick="infoVal(\'' +
// 					reserve_id +
// 					"', '" +
// 					reserve_shift +
// 					"')\">";
// 				btn_info += waiting;
// 				btn_info += "</a>";
// 			} else if (reserve_status == "Submitted") {
// 				btn_info =
// 					'<a type="button" class="btn-sm btn-success"" style="color:white;" role="button"  aria-pressed="true" onclick="infoVal(\'' +
// 					reserve_id +
// 					"','"+
// 					reserve_shift +
// 					"')\" >";
// 				btn_info += confirm;
// 				btn_info += "</a>";
// 			} else if (reserve_status == "Arranged") {
// 				btn_info =
// 					'<a class="bg-secondary btn-sm btn-success" style="color:white;" role="button"  aria-pressed="true" onclick="infoVal(\'' +
// 					reserve_id +
// 					"','"+
// 					reserve_shift +
// 					"')\" >";
// 				btn_info += "Arranged";
// 				btn_info += "</a>";
// 			} else if (reserve_status == "Cancelled") {
// 				btn_info =
// 					'<a class=" bg-danger btn-sm btn-danger" style="color:white;" role="button"  aria-pressed="true" onclick="infoVal(\'' +
// 					reserve_id +
// 					"','"+
// 					reserve_shift +
// 					"')\" >";
// 				btn_info += "ยกเลิก";
// 				btn_info += "</a>";
// 			} else if (reserve_status == "Pending") {
// 				btn_info =
// 					'<a class="bg-warning btn-sm btn-warning" style="color:white;" role="button"  aria-pressed="true" onclick="infoVal(\'' +
// 					reserve_id +
// 					"','"+
// 					reserve_shift +					
// 					"')\">";
// 				btn_info += "Pending";
// 				btn_info += "</a>";
// 			} else if (reserve_status == "Approved") {
// 				btn_info =
// 					'<a class="bg-warning btn-sm btn-warning" style="color:white;" role="button"  aria-pressed="true" onclick="infoVal(\'' +
// 					reserve_id +
// 					"','"+
// 					reserve_shift +					
// 					"')\" >";
// 				btn_info += "Approved";
// 				btn_info += "</a>";
// 			} else if (reserve_status == "Disapproved") {
// 				btn_info =
// 					'<a class="bg-danger btn-sm btn-danger" style="color:white;" role="button"  aria-pressed="true" onclick="infoVal(\'' +
// 					reserve_id +
// 					"','"+
// 					reserve_shift +					
// 					"')\">";
// 				btn_info += "GA disapproved";
// 				btn_info += "</a>";
// 			} else if (reserve_status == "Pending_cancel") {
// 				btn_info =
// 					'<a class="bg-warning btn-sm btn-warning" style="color:white;" role="button"  aria-pressed="true" onclick="infoVal(\'' +
// 					reserve_id +
// 					"','"+
// 					reserve_shift +					
// 					"')\" >";
// 				btn_info += "รออนุมัติ การยกเลิก";
// 				btn_info += "</a>";
// 			}

// 			//attendance time

// 			tbHistory.row
// 				.add([
// 					chk_box,
// 					reserve_date,
// 					reserve_time,
// 					text_info,
// 					btn_info,
// 					attendance_time,
// 				])
// 				.draw();

// 			last_reserve_date = reserve_date;
// 		});
// 	}

// 	max_line = json_data.reserve.length;

// 	document.getElementById("hdnMaxLine").value = max_line;
// 	document.getElementById("get_date_start").value = json_data.curr_date;
// 	document.getElementById("get_date_end").value = last_reserve_date;
// }

function set_data_in_layout(json_data) {
	const ck_lang = getCookie("ck_emp_lang");

	let waiting = "Waiting Confirm";
	let confirm = "Confirm";
	let waiting_cancel = "Waiting Cancel";
	if (ck_lang == "th") {
		waiting = "รอ Confirm";
		confirm = "Confirm";
		waiting_cancel = "รออนุมัติยกเลิก";
	} else {
		waiting = "Waiting Confirm";
		confirm = "Confirm";
		waiting_cancel = "Waiting Cancel";
	}

	var session_feature_set_item_user_cancel = sessionStorage.getItem('session_feature_set_item_user_cancel');
	var query_next = json_data.query_next;
	var last_reserve_date = "";

	console.log(query_next);

	// const trip_out_time = ["05:05", "08:00"];

	var tbHistory = $("#tbHistory").DataTable();

	tbHistory.clear().draw();

	if (json_data.reserve != undefined && json_data.reserve != null) {
		console.log(json_data);
		json_data.reserve.forEach((row, index) => {
			var reserve_id = row.reserve_id;
			var reserve_route = row.route;
			var reserve_date = row.reserve_date;
			var reserve_round = row.reserve_round;
			var reserve_time = row.reserve_time;
			var reserve_trip = row.reserve_trip;
			var reserve_status = row.reserve_status;
			var attendance_time = row.attendance_time;

			chk_box = "";

			const today = new Date();
			const year  = today.getFullYear();
			const month = String(today.getMonth() + 1).padStart(2, '0'); // เดือนเริ่มที่ 0 ต้อง +1
			const day   = String(today.getDate()).padStart(2, '0');
			const currentDate = `${year}-${month}-${day}`;
			console.log("Current Date : " + currentDate);

			// สร้างวันถัดไปโดยเพิ่ม 1 วัน
			const tomorrow = new Date();
			tomorrow.setDate(today.getDate() + 1);

			// จัดรูปแบบวันที่ใหม่
			const nextyear  = tomorrow.getFullYear();
			const nextmonth = String(tomorrow.getMonth() + 1).padStart(2, '0'); // เดือนเริ่มที่ 0
			const nextday   = String(tomorrow.getDate()).padStart(2, '0');

			const nextDate = `${nextyear}-${nextmonth}-${nextday}`;
			console.log("Tomorrow's Date: " + nextDate);


			//if (session_feature_set_item_user_cancel === "active"){

			if (reserve_date < json_data.curr_date) {
				chk_box =
					'<input name="chk_reserve_' +
					(index + 1) +
					'" id="chk_reserve_' +
					(index + 1) +
					'" type="checkbox" value="' +
					reserve_id +
					'" disabled/>';
			} else if (reserve_date == json_data.curr_date) {
			
				if(reserve_status == "Submitted") { 
					chk_box =
						'<input name="chk_reserve_' +
						(index + 1) +
						'" id="chk_reserve_' +
						(index + 1) +
						'" type="checkbox" value="' +
						reserve_id +
						'" disabled/>';
				} else { 
					chk_box =
						'<input name="chk_reserve_' +
						(index + 1) +
						'" id="chk_reserve_' +
						(index + 1) +
						'" type="checkbox" value="' +
						reserve_id +
							'"/>';
				}
				
			} else if (reserve_date == nextDate) {
			
				if(reserve_status == "Submitted") { 
					chk_box =
						'<input name="chk_reserve_' +
						(index + 1) +
						'" id="chk_reserve_' +
						(index + 1) +
						'" type="checkbox" value="' +
						reserve_id +
						'" disabled/>';
				} else { 
					chk_box =
						'<input name="chk_reserve_' +
						(index + 1) +
						'" id="chk_reserve_' +
						(index + 1) +
						'" type="checkbox" value="' +
						reserve_id +
							'"/>';
				}
				
			} else {
				chk_box =
					'<input name="chk_reserve_' +
					(index + 1) +
					'" id="chk_reserve_' +
					(index + 1) +
					'" type="checkbox" value="' +
					reserve_id +
					'"/>';
			}

			//}

			//index
			//reserve_date
			//reserve_time

			text_info = "";
			if (reserve_trip == "in") {
				text_info = '<span class="text-info">In</span>';
			} else if (reserve_trip == "out") {
				text_info = '<span class="text-warning">Out</span>';
			}
			var fixedBtnStyle = 'color:white; width:70px; height:35px; font-size:10px; text-align:center; padding:0 5px;';

			btn_info = "";
			if (reserve_status == "Waiting") {
				btn_info =
					'<button type="button" class="btn btn-sm btn-warning" style="' + fixedBtnStyle + '" aria-pressed="true" onclick="infoVal(\'' +
					reserve_id +
					'\')">';
				btn_info += waiting;
				btn_info += '</button>';
			} else if (reserve_status == "Submitted") {
				btn_info +=
					'<button type="button" class="btn btn-sm btn-success" style="' + fixedBtnStyle + '" aria-pressed="true" onclick="infoVal(\'' +
					reserve_id +
					'\')">';
				btn_info += confirm;
				btn_info += '</button>';
				btn_info += '</div>';
			} 
			else if (reserve_status == "Arranged") {
				btn_info =
					'<a class="bg-secondary btn-sm btn-success" style="color:white;" role="button"  aria-pressed="true" onclick="infoVal(\'' +
					reserve_id +
					"')\" >";
				btn_info += "Arranged";
				btn_info += "</a>";
			} else if (reserve_status == "Cancelled") {
				btn_info =
					'<a class=" bg-danger btn-sm btn-danger" style="color:white;" role="button"  aria-pressed="true" onclick="infoVal(\'' +
					reserve_id +
					"')\" >";
				btn_info += "ยกเลิก";
				btn_info += "</a>";
			} else if (reserve_status == "Pending") {
				btn_info =
					'<a class="bg-warning btn-sm btn-warning" style="color:white;" role="button"  aria-pressed="true" onclick="infoVal(\'' +
					reserve_id +
					"')\">";
				btn_info += "Pending";
				btn_info += "</a>";
			} else if (reserve_status == "Approved") {
				btn_info =
					'<a class="bg-warning btn-sm btn-warning" style="color:white;" role="button"  aria-pressed="true" onclick="infoVal(\'' +
					reserve_id +
					"')\" >";
				btn_info += "Approved";
				btn_info += "</a>";
			} else if (reserve_status == "Disapproved") {
				btn_info =
					'<a class="bg-danger btn-sm btn-danger" style="color:white;" role="button"  aria-pressed="true" onclick="infoVal(\'' +
					reserve_id +
					"')\">";
				btn_info += "GA disapproved";
				btn_info += "</a>";
			} else if (reserve_status == "Pending_cancel") {
				btn_info =
					'<button type="button" class="btn btn-sm btn-warning" style="' + fixedBtnStyle + '" aria-pressed="true" onclick="infoVal(\'' +
					reserve_id +
					'\')">';
				btn_info += waiting_cancel;
				btn_info += '</button>';
			}

			//attendance time

			tbHistory.row
				.add([
					chk_box,
					// reserve_route,
					reserve_date,
					reserve_time,
					'<div style="text-align:center;">' + text_info + '</div>',
					'<div style="text-align:center;">' + btn_info + '</div>',
					attendance_time,
				])
				.draw();

			last_reserve_date = reserve_date;
		});
	}

	max_line = json_data.reserve.length;

	document.getElementById("hdnMaxLine").value = max_line;
	document.getElementById("get_date_start").value = json_data.curr_date;
	document.getElementById("get_date_end").value = last_reserve_date;
}

function inArray(needle, haystack) {
	var length = haystack.length;
	for (var i = 0; i < length; i++) {
		if (haystack[i] == needle) return true;
	}
	return false;
}

function infoVal(reserve_id,reserve_shift) {
	console.log(reserve_id);
	sessionStorage.getItem('session_feature_set_item_user_cancel');
	sessionStorage.setItem("reserve_id", reserve_id);
	sessionStorage.setItem("reserve_shift", reserve_shift);

	window.location.href = "Reserve_info";
}

function chkVal() {
	var get_status = document.getElementById("get_status").value;
	var get_date_start = document.getElementById("get_date_start").value;
	var get_date_end = document.getElementById("get_date_end").value;

	if (get_date_end < get_date_start) {
		$("#get_date_end").val("");
		Swal.fire({
			title: "Warning",
			text: "ไม่สามารถเลือกวันที่สิ้นสุดน้อยกว่าวันที่เริ่มได้",
			icon: "warning",
		});
	} else {
		$.ajax({
			type: "POST",
			url: "Reserve_history/chk_val",
			data: {
				get_status: get_status,
				get_date_start: get_date_start,
				get_date_end: get_date_end,
			},
			success: function (response) {
				json_data = JSON.parse(response);
				console.log(json_data);
				set_data_in_layout(json_data);
			},
			error: function (error) {
				console.error(error);
			},
		});
	}
}

function cancelSelect() {
	var formData = new FormData($("#frmReserve")[0]);

	let lang = getCookie('ck_emp_lang');
	let text_confirm = lang === "th" ? "คุณต้องการยกเลิกรายการหรือไม่?" : "Confirm selected items?";

	Swal.fire({
		title: "Are you sure?",
		text:  text_confirm,
		icon: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: "Confirm",
		cancelButtonText: "Cancel",
		reverseButtons: true, // This ensures the cancel button comes first
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				type: "POST",
				url: "Reserve_history/cancel_select",
				data: formData,
				async: false,
				success: function (response) {
					console.log(response);

					let obj = JSON.parse(response);

					if (obj.message == "not_select") {
						Swal.fire(
							"Warning",
							"โปรดเลือกอย่างน้อย 1 รายการ เพื่อทำการยกเลิก",
							"warning"
						);
					} else if (obj.message == "before_date") {
						Swal.fire(
							"Warning",
							"บางรายการที่คุณเลือกเป็นข้อมูลย้อนหลัง โปรดตรวจสอบอกครั้ง",
							"warning"
						);
					} else if (obj.message == "1") {
						Swal.fire({
							title: "Successfully",
							text: "ยกเลิกรายการจองรถสำเร็จ.",
							icon: "success",
							confirmButtonColor: "#DD6B55",
							confirmButtonText: "OK",
						}).then(() => {
							window.location.reload();
						});
					} else if (obj.message == "error") {
						Swal.fire(
							"Error",
							"มีข้อผิดพลาดเกิดขิ้น โปรดตรวจสอบอีกครั้ง",
							"error"
						);
					} else {
						Swal.fire(
							"Warning",
							"พบ" +
								response.chk_error_time +
								"รายการ วันที่ " +
								response.curr_date +
								"ไม่สามารถยกเลิกได้ เนื่องจากเลยเวลาที่บริษัทกำหนด รายการอื่นหลังจากวันนี้ทำการยกเลิกสำเร็จ",
								"warning"
						);
					}
				},
				cache: false,
				contentType: false,
				processData: false,
			});
		}
	});
}

var pStart = { x: 0, y: 0 };
var pStop = { x: 0, y: 0 };

function swipeStart(e) {
	if (typeof e["targetTouches"] !== "undefined") {
		var touch = e.targetTouches[0];
		pStart.x = touch.screenX;
		pStart.y = touch.screenY;
	} else {
		pStart.x = e.screenX;
		pStart.y = e.screenY;
	}
}

function swipeEnd(e) {
	if (typeof e["changedTouches"] !== "undefined") {
		var touch = e.changedTouches[0];
		pStop.x = touch.screenX;
		pStop.y = touch.screenY;
	} else {
		pStop.x = e.screenX;
		pStop.y = e.screenY;
	}

	swipeCheck();
}

function swipeCheck() {
	var changeY = pStart.y - pStop.y;
	var changeX = pStart.x - pStop.x;
	if (isPullDown(changeY, changeX)) {
		window.location.reload();
	}
}

function isPullDown(dY, dX) {
	// methods of checking slope, length, direction of line created by swipe action
	return (
		dY < 0 &&
		((Math.abs(dX) <= 100 && Math.abs(dY) >= 300) ||
			(Math.abs(dX) / Math.abs(dY) <= 0.3 && dY >= 60))
	);
}

document.addEventListener(
	"touchstart",
	function (e) {
		swipeStart(e);
	},
	false
);
document.addEventListener(
	"touchend",
	function (e) {
		swipeEnd(e);
	},
	false
);

function chkAll() {
	// var chk_all = document.getElementById("chk_all");
	// var hdnMaxLine = document.getElementById("hdnMaxLine").value;
	// if (chk_all.checked) {
	// 	for (var i = 1; i <= hdnMaxLine; i++) {
	// 		if (!document.getElementById("chk_reserve_" + i).disabled) {
	// 			document.getElementById("chk_reserve_" + i).checked = true;
	// 		}
	// 	}
	// } else {
	// 	for (var i = 1; i <= hdnMaxLine; i++) {
	// 		if (!document.getElementById("chk_reserve_" + i).disabled) {
	// 			document.getElementById("chk_reserve_" + i).checked = false;
	// 		}
	// 	}
	// }

	var tbHistory = $('#tbHistory').DataTable();
	tbHistory.rows().every(function() {
		var row = this.node();
		var checkbox = $('input[type="checkbox"]', row)[0];
		// Check if checkbox is not disabled and apply the "select all" checkbox state
        if (checkbox && !checkbox.disabled) {
            checkbox.checked = chk_all.checked; // Set the checkbox checked state based on "select all" checkbox
        }
	  });
}

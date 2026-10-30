$(document).ready(async function () {
	const reserve_id = sessionStorage.getItem("reserve_id");
	const reserve_shift = sessionStorage.getItem("reserve_shift");
	const feature_set_item_user_cancel= sessionStorage.getItem("session_feature_set_item_user_cancel");
	//await checkFeature(feature_set_item_user_cancel);
	await getRound(reserve_id);
	await getRoute(reserve_id, reserve_shift);
	await getPoint(reserve_id);
	await getData(reserve_id);
});

function getCookie(name) {
	var value = "; " + document.cookie;
	var parts = value.split("; " + name + "=");
	if (parts.length == 2) return parts.pop().split(";").shift();
}

function showLoader() {
	var overlay = document.getElementById('loading-overlay');
	if (overlay) overlay.style.display = 'flex';
}

function backVal() {
	showLoader();
	window.location.href = "Reserve_history";
}

$('.select2').select2({
    width: '100%'
});

// function checkFeature(feature_set_item_user_cancel){
// 	if (feature_set_item_user_cancel === "active"){
// 		$('.Check-feature-user-cancel').show();
// 	}else{
// 		$('.Check-feature-user-cancel').hide();
// 	}
// }

function getRound(reserve_id) {
	$.ajax({
		type: "POST",
		url: "Reserve_info/get_round",
		data: { reserve_id: reserve_id },
		success: function (response) {
			let json_data = JSON.parse(response);
			let ck_lang = getCookie("ck_emp_lang");

			let html_code = "";

			// default option
			if (ck_lang == "th") {
				html_code += '<option value="">---เลือก---</option>';
			} else {
				html_code += '<option value="">---Select---</option>';
			}

			// ❗ ถ้าไม่มีเวลา
			if (!json_data.shift || json_data.shift.length === 0) {
				html_code +=
					'<option value="" disabled selected>' +
					(ck_lang == "th"
						? "ไม่มีเวลาเปิดขณะนี้"
						: "No available time at the moment") +
					"</option>";

				$("#reserve_round").html(html_code);
				return;
			}

			// มีเวลา → loop ตามปกติ
			let arr_shift = json_data.shift;
			for (let i = 0; i < arr_shift.length; i++) {
				if (json_data.reserve_time == arr_shift[i]) {
					html_code +=
						'<option value="' +
						arr_shift[i] +
						'" selected>' +
						arr_shift[i] +
						"</option>";
				} else {
					html_code +=
						'<option value="' +
						arr_shift[i] +
						'">' +
						arr_shift[i] +
						"</option>";
				}
			}

			$("#reserve_round").html(html_code);
		},
	});
}


function getRoute(reserve_id, reserve_shift) {
	$.ajax({
		type: "POST",
		url: "Reserve_info/get_route",
		data: { reserve_id: reserve_id, reserve_shift, reserve_shift },
		success: function (response) {
			$("#reserve_route").html(response);

			json_data = JSON.parse(response);

			console.log(json_data);

			var ck_lang = getCookie("ck_emp_lang");
			//set data in layout
			var html_code = "";

			if (ck_lang == "th") {
				html_code += '<option value="">---เลือก---</option>';
			} else {
				html_code += '<option value="">---Select---</option>';
			}

			if (json_data.route.length > 0) {
				json_data.route.forEach(function (row, index) {
					route_id = row.route_id;
					route_code = row.route_code;
					route_name = row.route_name;

					if (json_data.reserve_route == route_id) {
						html_code += '<option value = "' + route_id + '" selected>' + route_code + " " + route_name + "</option>";
					} else {
						html_code +=
							'<option value = "' +
							route_id +
							'">' +
							route_code +
							" " +
							route_name +
							"</option>";
					}
				});
			}

	
			$("#reserve_route").html(html_code);
		},
	});
}

function getPoint(reserve_id) {
	$.ajax({
		type: "POST",
		url: "Reserve_info/get_point",
		data: { reserve_id: reserve_id },
		success: function (response) {
			// $('#reserve_point').html(response);

			json_data = JSON.parse(response);
			var ck_lang = getCookie("ck_emp_lang");

			console.log(response);

			//set data in layout
			var html_code = "";

			if (ck_lang == "th") {
				html_code += '<option value="">---เลือก---</option>';
			} else {
				html_code += '<option value="">---Select---</option>';
			}

			if (json_data.arr_point.length > 0 && json_data.arr_point != null) {
				json_data.arr_point.forEach(function (row, index) {
					point_id = row.point_id;
					point_code = row.point_code;
					point_name = row.point_name;

					if (json_data.reserve_point == point_id) {
						html_code +=
							'<option value = "' +
							point_id +
							'" selected>' +
							point_code +
							" " +
							point_name +
							"</option>";
					} else {
						html_code +=
							'<option value = "' +
							point_id +
							'">' +
							point_code +
							" " +
							point_name +
							"</option>";
					}
				});
			}

			$("#reserve_point").html(html_code);
		},
	});
}

function getData(reserve_id) {
	$.ajax({
		type: "POST",
		url: "Reserve_info/get_data",
		data: { reserve_id: reserve_id },
		success: function (response) {
			try {
				var ck_lang = getCookie("ck_emp_lang");
				var json_data = JSON.parse(response);
				
				// ตรวจสอบว่า json_data เป็นอาร์เรย์และมีอย่างน้อย 1 รายการ
				if (!Array.isArray(json_data) || json_data.length === 0) {
					throw new Error("Invalid data format");
				}

				var reserveData = json_data[0];

				// อัปเดตค่าใน DOM
				var reserveIdInput = document.getElementById("reserve_id");
				var reserveStatusInput = document.getElementById("reserve_status");
				var reserveNameInput = document.getElementById("reserve_name");
				var reserveDateInput = document.getElementById("reserve_date");
				var reserveTripInput = document.getElementById("reserve_trip");

				if (reserveIdInput) {
					reserveIdInput.value = reserveData.reserve_id;
				}

				if (reserveStatusInput) {
					reserveStatusInput.readOnly = true;
					reserveStatusInput.value = translateStatus(
						reserveData.reserve_status,
						ck_lang
					);
				}

				if (reserveNameInput) {
					reserveNameInput.value = reserveData.reserve_name;
					reserveNameInput.readOnly = true;
				}

				if (reserveDateInput) {
					reserveDateInput.value = reserveData.reserve_date;
				}

				if (reserveTripInput) {
					reserveTripInput.value = reserveData.reserve_trip;
				}

				
				if (ck_lang === "th" && reserveData.reserve_trip === "in") {
					$('#lbl_route').text("สายรถรับเข้า");
					$('#lbl_points').text("จุดรับ");
				} else if (ck_lang === "th" && reserveData.reserve_trip === "out") {
					$('#lbl_route').text("สายรถรับออก");
					$('#lbl_points').text("จุดส่ง");
				} else if (ck_lang === "en" && reserveData.reserve_trip === "in") {
					$('#lbl_route').text("Route In");
					$('#lbl_points').text("Pick up Point");
				} else if (ck_lang === "en" && reserveData.reserve_trip === "out") {
					$('#lbl_route').text("Route Out");
					$('#lbl_points').text("Drop off Point");
				}

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
				
				// ตรวจสอบสถานะและกำหนดปุ่มให้ disabled หรือ enabled
				// if (reserveData.reserve_status === "Submitted" && reserveData.reserve_date == currentDate && reserveData.reserve_trip == "out") {
				// 	document.getElementById("btn_submit").disabled = true;
				// 	document.getElementById("btn_del").disabled = true;
				// } else if (reserveData.reserve_status === "Submitted" && reserveData.reserve_date == nextDate && reserveData.reserve_trip == "in") {
				// 	document.getElementById("btn_submit").disabled = true;
				// 	document.getElementById("btn_del").disabled = true;
				// } else if (reserve_status === "Waiting") {
				// 	document.getElementById("btn_submit").disabled = false;
				// 	document.getElementById("btn_del").disabled = false;
				// } else { 
				// 	document.getElementById("btn_submit").disabled = false;
				// 	document.getElementById("btn_del").disabled = false;
				// }


			} catch (error) {
				console.error("Error processing response:", error.message);
				// จัดการกับข้อผิดพลาดที่นี่
			}
		},
		error: function (xhr, status, error) {
			console.error("AJAX Error:", error);
			console.error(xhr);
		},
	});
}

function translateStatus(status, lang) {
	var translations = {
		th: {
			Waiting: "รอ Confirm",
			Submitted: "Confirm",
			Arranged: "Arranged",
			Cancelled: "ยกเลิก",
			Pending: "Pending",
			Approved: "Approved",
			Disapproved: "HR ไม่อนุมัติ",
			Pending_cancel: "รออนุมัติยกเลิก",
		},
		en: {
			// Waiting: "Waiting",
			// Submitted: "Submitted",
			Waiting: "Waiting Confirm",
			Submitted: "Confirm",
			Arranged: "Arranged",
			Cancelled: "Cancel",
			Pending: "Pending",
			Approved: "Approved",
			Disapproved: "HR not approved",
			Pending_cancel: "Pending Cancel",
		},
	};

	return (translations[lang] && translations[lang][status]) || status;
}

function getRouteByShift(){

	var reserve_trip = document.getElementById("reserve_trip").value;
	var reserve_time = document.getElementById("reserve_round").value;

	console.log(reserve_time);

	console.log("Reserve Trip -" + reserve_trip);

	clearOptions(reserve_point);

	if (reserve_round !== "") {
		fetchRouteData(reserve_trip, reserve_time)
			.done(function (response) {
				var json_data;

				try {
					json_data = JSON.parse(response);
					console.log(json_data);
				} catch (e) {
					console.error("JSON Parsing Error:", e);
					return; // ออกจากฟังก์ชันถ้าข้อมูล JSON ผิดพลาด
				}

				var ck_lang = getCookie("ck_emp_lang");
				var html_code = generateRouteOptions(json_data, ck_lang, reserve_trip);
				$("#reserve_route").html(html_code);

				//setShiftData(json_data.shift_data);
				chkPoint();
			})
			.fail(function (xhr, status, error) {
				console.error(xhr);
				// จัดการกับข้อผิดพลาดของ AJAX ที่นี่
			});
	}

}

function chkRoute() {
	var reserve_trip = document.getElementById("reserve_trip").value;

	var reserve_point = document.getElementById("reserve_point");
	clearOptions(reserve_point);

	if (reserve_round !== "") {
		fetchRouteData(reserve_trip)
			.done(function (response) {
				var json_data;

				// ลองแปลง JSON และจัดการข้อผิดพลาด
				try {
					json_data = JSON.parse(response);
					console.log(json_data);
				} catch (e) {
					console.error("JSON Parsing Error:", e);
					return; // ออกจากฟังก์ชันถ้าข้อมูล JSON ผิดพลาด
				}

				var ck_lang = getCookie("ck_emp_lang");
				var html_code = generateRouteOptions(json_data, ck_lang, reserve_trip);
				
				
				$("#reserve_route").html(html_code);

				setShiftData(json_data.shift_data);
				chkPoint();
			})
			.fail(function (xhr, status, error) {
				console.error(xhr);
				// จัดการกับข้อผิดพลาดของ AJAX ที่นี่
			});
	}
}

function clearOptions(selectElement) {
	var length = selectElement.options.length;
	for (var i = length; i > 0; i--) {
		selectElement.options[i] = null;
	}
}

function fetchRouteData(reserve_trip, reserve_time = null) {
	return $.ajax({
		type: "POST",
		url: "Reserve_info/chk_route",
		data: { reserve_trip: reserve_trip, reserve_time: reserve_time },
	});
}

function generateRouteOptions(json_data, ck_lang, reserve_trip) {
	var html_code =
		ck_lang === "th"
			? '<option value="">---เลือก---</option>'
			: '<option value="">---Select---</option>';

	json_data.route.forEach(function (row) {
		var route_id = row.route_id;
		var route_code = row.route_code;
		var route_name = row.route_name;
		var isSelected =
			(reserve_trip === "in" && route_id === json_data.emp_route_id) ||
			(reserve_trip === "out" && route_id === json_data.emp_route_out_id)
				? "selected"
				: "";

		html_code += `<option value="${route_id}" ${isSelected}>${route_code} ${route_name}</option>`;
	});

	return html_code;
}

function setShiftData(shift_data) {
	var reserve_round = document.getElementById("reserve_round");
	reserve_round.innerHTML = '<option value="">-- เลือก --</option>';

	shift_data.forEach(function (row) {
		var option = document.createElement("option");
		option.value = row.shift;
		option.text = row.shift;
		reserve_round.appendChild(option);
	});
}

function chkPoint() {
	var reserve_trip = document.getElementById("reserve_trip").value;
	var reserve_route = document.getElementById("reserve_route").value;

	if (reserve_trip && reserve_route) {
		$.ajax({
			type: "POST",
			url: "Reserve_info/chk_point",
			data: { reserve_trip: reserve_trip, reserve_route: reserve_route },
			success: function (response) {
				try {
					var json_data = JSON.parse(response);
					var ck_lang = getCookie("ck_emp_lang");
					var html_code = "";

					html_code +=
						ck_lang === "th"
							? '<option value="">---เลือก---</option>'
							: '<option value="">---Select---</option>';

					// Check if `arr_point` exists and has items
					if (json_data.arr_point && json_data.arr_point.length > 0) {
						json_data.arr_point.forEach(function (row) {
							var point_id = row.point_id;
							var point_code = row.point_code;
							var point_name = row.point_name;
							var selected = "";

							// Determine if the current point should be selected
							if (
								(reserve_trip === "in" && json_data.emp_pick_id === point_id) ||
								(reserve_trip === "out" && json_data.emp_drop_id === point_id)
							) {
								selected = " selected";
							}

							// Append option to the HTML code
							html_code += `<option value="${point_id}"${selected}>${point_code} ${point_name}</option>`;
						});
					}

					// Set the HTML content of the reserve_point select element
					$("#reserve_point").html(html_code);
				} catch (error) {
					console.error("Error parsing JSON response:", error);
				}
			},
			error: function (xhr, status, error) {
				console.log(xhr);
				console.error("AJAX request failed:", status, error);
			},
		});
	}
}

function chkVal() {
	var reserve_id = document.getElementById("reserve_id").value;
	var reserve_date = document.getElementById("reserve_date").value;
	var reserve_round = document.getElementById("reserve_round").value;
	var reserve_trip = document.getElementById("reserve_trip").value;
	var reserve_route = document.getElementById("reserve_route").value;
	var reserve_point = document.getElementById("reserve_point").value;

	console.log(reserve_id + ' \n ' + reserve_date + ' \n ' + reserve_round + ' \n ' + reserve_trip + ' \n ' + reserve_route + ' \n ' + reserve_point);
	
	var ck_emp_lang = getCookie("ck_emp_lang");
	if (reserve_date == "") {
		Swal.fire("Warning", "Please specify your date", "warning");
	} else if (reserve_round == "") {
		Swal.fire("Warning", "Please select your time", "warning");
	} else if (reserve_trip == "") {
		Swal.fire("Warning", "Please select your trip", "warning");
	} else if (reserve_route == "") {
		Swal.fire("Warning", "Please select your route", "warning");
	} else if (reserve_point == "") {
		Swal.fire("Warning", "Please select your point", "warning");
	} else {
		$.ajax({
			type: "POST",
			url: "Reserve_info/save_reservation",
			data: {
				reserve_id: reserve_id,
				reserve_date: reserve_date,
				reserve_round: reserve_round,
				reserve_trip: reserve_trip,
				reserve_route: reserve_route,
				reserve_point: reserve_point,
			},
			success: function (response) {
				//console.log(response);
				try {
					json_data = JSON.parse(response);

					if (json_data.data == "reserve_less_than") {
						if (ck_emp_lang == "th") {
							Swal.fire({
								title: "Warning",
								text: "ไม่สามารถทำรายการวันที่ย้อนหลังได้",
								icon: "warning",
								timer: 3000,
								timerProgressBar: true,
								showConfirmButton: false,
								//position: 'top-right',
							});
						} else {
							Swal.fire({
								title: "Warning",
								text: "Unable to make past date reservation", //Sarawuth Updated on 20/06/2025
								icon: "warning",
								timer: 3000,
								timerProgressBar: true,
								showConfirmButton: false,
								//position: 'top-right',
							});
						}
					} else if (json_data.data == "data_dup") {
						if (ck_emp_lang == "th") {
							Swal.fire({
								title: "Warning",
								text: "คุณมีการเดินทางในรอบและวันดังกล่าวแล้ว",
								icon: "warning",
								timer: 3000,
								timerProgressBar: true,
								showConfirmButton: false,
								//position: 'top-right',
							});
						} else {
							Swal.fire({
								title: "Warning",
								text: "Unable to make past date reservation", //Sarawuth Updated on 20/06/2025
								icon: "warning",
								timer: 4000,
								timerProgressBar: true,
								showConfirmButton: false,
								//position: 'top-right',
							});
						}
					}
						
						else if (json_data.data == "ng_cut_of_time") {
						if (ck_emp_lang == "th") {
							Swal.fire({
								title: "Warning",
								text: "ไม่สามารถทำรายการได้เนื่องจากใกล้เวลารถออก กรุณาตรวจสอบรอบรถและเวลารถออก", //Sarawuth Updated on 20/06/2025
								icon: "warning",
								timer: 3000,
								timerProgressBar: true,
								showConfirmButton: false,
								//position: 'top-right',
							});
						} else {
							Swal.fire({
								title: "Warning",
								text: "Unable to make reservation near departure time", //Sarawuth Updated on 20/06/2025
								icon: "warning",
								timer: 4000,
								timerProgressBar: true,
								showConfirmButton: false,
								//position: 'top-right',
							});
						}
					} 
					else if (json_data.data == "trip_dupplicate") {
						if (ck_emp_lang == "th") {
							Swal.fire({
								title: "Warning",
								text: "มีรายการจองอยู่แล้ว กรุณาแก้ไขการจอง",
								icon: "warning",
								timer: 3000,
								timerProgressBar: true,
								showConfirmButton: false,
								//position: 'top-right',
							});
						} else {
							Swal.fire({
								title: "Warning",
								text: "There is already a reservation. Please edit the reservation.",
								icon: "warning",
								timer: 3000,
								timerProgressBar: true,
								showConfirmButton: false,
								//position: 'top-right',
							});
						}
					} else if (json_data.data == "Submitted") {
						Swal.fire({
							title: "Successfully",
							text: "Successfully",
							icon: "success",
							confirmButtonColor: "#DD6B55",
							confirmButtonText: "OK",
							allowOutsideClick: false,
						}).then((result) => {
							if (result.isConfirmed) {
								window.location.href = "Reserve_history";
							}
						});
					} else if (
						json_data.data == "Waiting" ||
						json_data.data == "Pending_cancel"
					) {
						Swal.fire({
							title: "Warning",
							text:
								ck_emp_lang == "th"
									? "ทำการแก้ไขสำเร็จ"
									: "Save Data Completed",
							icon: "success",
							confirmButtonColor: "#DD6B55",
							confirmButtonText: "OK",
							allowOutsideClick: false,
						}).then((result) => {
							if (result.isConfirmed) {
								window.location.href = "Reserve_history";
							}
						});
			
					}
				} catch (error) {
					console.error("Error parsing JSON response:", error);
				}
			},
			error: function (error) {
				console.error(error);
			},
		});
	}
}

function cancelVal() {

    var ck_emp_lang = getCookie("ck_emp_lang");

	var reserve_id = document.getElementById("reserve_id").value;
	var reserve_date = document.getElementById("reserve_date").value;
	var reserve_round = document.getElementById("reserve_round").value;
	var reserve_trip = document.getElementById("reserve_trip").value;
	var reserve_route = document.getElementById("reserve_route").value;
	var reserve_point = document.getElementById("reserve_point").value;

	if (reserve_date == "") {
		Swal.fire("Warning", "Please specify your date", "warning");
	} else if (reserve_round == "") {
		Swal.fire("Warning", "Please select your round", "warning");
	} else if (reserve_trip == "") {
		Swal.fire("Warning", "Please select your trip", "warning");
	} else if (reserve_route == "") {
		Swal.fire("Warning", "Please select your route", "warning");
	} else if (reserve_point == "") {
		Swal.fire("Warning", "Please select your point", "warning");
	} else {
		Swal.fire({
			title: "Are you sure?",
			text: "โปรดยืนยันการยกเลิกการจอง?",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#DD6B55",
			cancelButtonText: "Cancel",
			confirmButtonText: "Confirm",
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					type: "POST",
					url: "Reserve_info/cancel_reservation",
					data: {
						reserve_id: reserve_id,
						reserve_date: reserve_date,
						reserve_round: reserve_round,
						reserve_trip: reserve_trip,
						reserve_route: reserve_route,
						reserve_point: reserve_point,
					},
					success: function (response) {
						console.log(response);

						try {
							json_data = JSON.parse(response);

							if (json_data.data == "reserve_less_than") {
								if (ck_emp_lang == "th") {
									Swal.fire({
										title: "Warning",
										text: "ไม่สามารถทำรายการวันที่ย้อนหลังได้",
										icon: "warning",
										timer: 3000,
										timerProgressBar: true,
										showConfirmButton: false,
										//position: 'top-right',
									});
								} else {
									Swal.fire({
										title: "Warning",
										text: "Unable to make past date reservation", //Sarawuth Updated on 20/06/2025
										icon: "warning",
										timer: 3000,
										timerProgressBar: true,
										showConfirmButton: false,
										//position: 'top-right',
									});
								}
							} else if (json_data.data == "data_dup") {
								if (ck_emp_lang == "th") {
									Swal.fire({
										title: "Warning",
										text: "คุณมีการเดินทางในรอบและวันดังกล่าวแล้ว",
										icon: "warning",
										timer: 3000,
										timerProgressBar: true,
										showConfirmButton: false,
										//position: 'top-right',
									});
								} else {
									Swal.fire({
										title: "Warning",
										text: "Unable to make past date reservation", //Sarawuth Updated on 20/06/2025
										icon: "warning",
										timer: 4000,
										timerProgressBar: true,
										showConfirmButton: false,
										//position: 'top-right',
									});
								}
							} else if (json_data.data == "trip_dupplicate") {
								if (ck_emp_lang == "th") {
									Swal.fire({
										title: "Warning",
										text: "มีรายการจองอยู่แล้ว กรุณาแก้ไขการจอง",
										icon: "warning",
										timer: 3000,
										timerProgressBar: true,
										showConfirmButton: false,
										//position: 'top-right',
									});
								} else {
									Swal.fire({
										title: "Warning",
										text: "There is already a reservation. Please edit the reservation.",
										icon: "warning",
										timer: 3000,
										timerProgressBar: true,
										showConfirmButton: false,
										//position: 'top-right',
									});
								}
							} else if (json_data.data == "Submitted") {
								Swal.fire({
									title: "Successfully",
									text: "Successfully",
									icon: "success",
									confirmButtonColor: "#DD6B55",
									confirmButtonText: "OK",
									allowOutsideClick: false,
								}).then((result) => {
									if (result.isConfirmed) {
										window.location.href = "Reserve_history";
									}
								});
							} else if (
								json_data.data == "Waiting" ||
								json_data.data == "Pending_cancel"
							) {
								if (ck_emp_lang == "th") {
									Swal.fire({
										title: "Warning",
										text: "การขอนี้จะไม่ถูกจัดรถ กรุณาติดต่อเจ้าหน้าที่ HR",
										icon: "warning",
										confirmButtonColor: "#DD6B55",
										confirmButtonText: "OK",
										allowOutsideClick: false,
									}).then((result) => {
										if (result.isConfirmed) {
											window.location.href = "Reserve_history";
										}
									});
								} else {
									Swal.fire({
										title: "Warning",
										text: "This request will not be arranged. Please contact HR staff.",
										icon: "success",
										confirmButtonColor: "#DD6B55",
										confirmButtonText: "OK",
										allowOutsideClick: false,
									}).then((result) => {
										if (result.isConfirmed) {
											window.location.href = "Reserve_history";
										}
									});
								}
							}else if(
								json_data.data == "Cancelled"){
									Swal.fire({
										title: "Successfully",
										text: "Successfully",
										icon: "success",
										confirmButtonColor: "#DD6B55",
										confirmButtonText: "OK",
										allowOutsideClick: false,
									}).then((result) => {
										if (result.isConfirmed) {
											window.location.href = "Reserve_history";
										}
									});
							}
						} catch (error) {
							console.error("Error parsing JSON response:", error);
						}
					},
					error: function (xhr) {
						console.error(xhr);
					},
				});
			}
		});
	}
}

function redirectHistory() {
	window.location.href = "Reserve_history";
}

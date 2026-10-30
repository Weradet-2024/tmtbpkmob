$(document).ready(function () {
	//$('#result_reservation').modal('show');
	getData();
	getPlant();
	chkFeature_ga_approval();

	$("#reserve_date").datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		disableTouchKeyboard: true,
		autoclose: true,
	});
	$("#reserve_date_end").datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		disableTouchKeyboard: true,
		autoclose: true,
	});
	$("#tb_result_reserve").DataTable({
		paging: true, // Disable pagination
		pageLength: 5,
		ordering: false, // Disable column ordering
		searching: false, // Disable search/filtering
		info: false, // Disable table information display
		bLengthChange: false, // Disable the ability to change the number of rows displayed
	});
});

document.addEventListener("DOMContentLoaded", function () {
	// When shift changes, clear round, time, and routes
	document.getElementById("shift").addEventListener("change", function () {
		clearSelections([
			"round",
			"reserve_time",
			"reserve_route",
			"reserve_route_out",
		]);
	});

	// When round changes, clear time and routes
	document.getElementById("round").addEventListener("change", function () {
		clearSelections(["reserve_time", "reserve_route", "reserve_route_out"]);
	});

	document
		.getElementById("reserve_time")
		.addEventListener("change", function () {
			clearSelections(["reserve_route", "reserve_route_out"]);
		});

	function clearSelections(ids) {
		ids.forEach((id) => {
			let element = document.getElementById(id);
			if (element) {
				element.value = "";
			}
		});
	}
});

function getCookie(name) {
	var value = "; " + document.cookie;
	var parts = value.split("; " + name + "=");
	if (parts.length == 2) return parts.pop().split(";").shift();
}

function backVal() {
	window.location.href = "Home";
}

function getPlant() {
	$.ajax({
		type: "POST",
		url: "Reserve_ad/get_plant",
		success: function (response) {
			json_data = JSON.parse(response);
			console.log(json_data);
		},
	});
}
function getData() {
	$.ajax({
		type: "POST",
		url: "Reserve_ad/get_data",
		success: function (response) {
			json_data = JSON.parse(response);
			console.log(json_data);
			document.getElementById("reserve_name").value = json_data.name;
			document.getElementById("reserve_name").readOnly = true;
			document.getElementById("reserve_date").value = json_data.today;
			document.getElementById("reserve_date_end").value = json_data.today;
			document.getElementById("reserve_route_name").style.display = "none";
			document.getElementById("reserve_route").style.display = "block";
			document.getElementById("reserve_point_name").style.display = "none";
			document.getElementById("reserve_point").style.display = "block";
			document.getElementById("reserve_route_out_name").style.display = "none";
			document.getElementById("reserve_route_out").style.display = "block";
			document.getElementById("reserve_point_out_name").style.display = "none";
			document.getElementById("reserve_point_out").style.display = "block";
		},
	});
}

function chkFeature_ga_approval() {
	$.ajax({
		type: "POST",
		url: "Reserve_ad/chkFeature_ga_approval",
		data: {},
		success: function (response) {
			console.log(response);
		},
	});
}

function chkTime(trip) {
	let shift = document.getElementById("shift").value;
	var ck_lang = getCookie("ck_emp_lang");

	$.ajax({
		type: "POST",
		url: "Reserve_ad/get_time",
		data: { reserve_trip: trip, shift: shift },
		success: function (response) {
			let obj = JSON.parse(response);
			var reserve_time = document.getElementById("reserve_time");
			$("#reserve_time").empty();
			var option = document.createElement("option");
			option.value = "";
			if (ck_lang == "th") {
				option.text = "-- เลือก --";
			} else if (ck_lang == "en") {
				option.text = "-- Select --";
			}
			reserve_time.appendChild(option);
			if (obj.data) {
				obj.data.forEach(function (row, index) {
					var option = document.createElement("option");
					option.value = row.shift_value;
					option.text = ck_lang === 'th' ? row.shift_text : row.shift_text_en;
					reserve_time.appendChild(option);
				});
			}
		},
	});
}

function chkTrip(trip) {
	let shift = document.getElementById("shift").value;
	var ck_lang = getCookie("ck_emp_lang");

	$.ajax({
		type: "POST",
		url: "Reserve_ad/chk_trip",
		data: { trip: trip },
		success: function (response) {
			json_data = JSON.parse(response);
			const arr_trip = trip.split(" ");

			if (arr_trip[0] == "in") {
				$("#route_in").removeAttr("style");
				$("#point_in").removeAttr("style");
				document.getElementById("route_out").style.display = "none";
				document.getElementById("point_out").style.display = "none";
			} else if (arr_trip[0] == "out") {
				$("#route_out").removeAttr("style");
				$("#point_out").removeAttr("style");
				document.getElementById("route_in").style.display = "none";
				document.getElementById("point_in").style.display = "none";
			} else if (arr_trip[0] == "in_out") {
				$("#route_in").removeAttr("style");
				$("#point_in").removeAttr("style");
				$("#route_out").removeAttr("style");
				$("#point_out").removeAttr("style");
			} else {
				document.getElementById("route_out").style.display = "none";
				document.getElementById("point_out").style.display = "none";
				document.getElementById("route_in").style.display = "none";
				document.getElementById("point_in").style.display = "none";
			}

			// Set route_zone data
			var ck_lang = getCookie("ck_emp_lang");
			var reserve_route = document.getElementById("reserve_route");
			$("#reserve_route").empty();
			var option = document.createElement("option");
			option.value = "";
			option.text = ck_lang == "th" ? "-- เลือก --" : "-- Select --";
			reserve_route.appendChild(option);

			json_data.route_zone.forEach(function (row) {
				var option = document.createElement("option");
				option.value = row.route_zone_id;
				option.text = row.route_zone_code + " " + row.route_zone_name;
				if (row.route_zone_id == json_data.emp_route_zone_id) {
					option.setAttribute("selected", "selected");
					document.getElementById("reserve_route_name").value =
						row.route_zone_code + " " + row.route_zone_name;
				}
				reserve_route.appendChild(option);
			});

			// Set pickup data
			var reserve_point = document.getElementById("reserve_point");
			$("#reserve_point").empty();
			var option = document.createElement("option");
			option.value = "";
			option.text = ck_lang == "th" ? "-- เลือก --" : "-- Select --";
			reserve_point.appendChild(option);

			json_data.pickup.forEach(function (row) {
				var option = document.createElement("option");
				option.value = row.pickup_id;
				option.text = row.pickup_code + " " + row.pickup_name;
				if (row.pickup_id == json_data.emp_pick_id) {
					option.setAttribute("selected", "selected");
					document.getElementById("reserve_point_name").value =
						row.pickup_code + " " + row.pickup_name;
				}
				reserve_point.appendChild(option);
			});

			// Set route_zone_out data
			var reserve_route_out = document.getElementById("reserve_route_out");
			$("#reserve_route_out").empty();
			var option = document.createElement("option");
			option.value = "";
			option.text = ck_lang == "th" ? "-- เลือก --" : "-- Select --";
			reserve_route_out.appendChild(option);

			json_data.route_zone_out.forEach(function (row) {
				var option = document.createElement("option");
				option.value = row.route_zone_out_id;
				option.text = row.route_zone_out_code + " " + row.route_zone_out_name;
				if (row.route_zone_out_id == json_data.emp_route_zone_out_id) {
					option.setAttribute("selected", "selected");
					document.getElementById("reserve_route_out_name").value =
						row.route_zone_out_code + " " + row.route_zone_out_name;
				}
				reserve_route_out.appendChild(option);
			});

			// Set dropoff data
			var reserve_point_out = document.getElementById("reserve_point_out");
			$("#reserve_point_out").empty();
			var option = document.createElement("option");
			option.value = "";
			option.text = ck_lang == "th" ? "-- เลือก --" : "-- Select --";
			reserve_point_out.appendChild(option);

			json_data.dropoff.forEach(function (row) {
				var option = document.createElement("option");
				option.value = row.dropoff_id;
				option.text = row.dropoff_code + " " + row.dropoff_name;
				if (row.dropoff_id == json_data.emp_drop_id) {
					option.setAttribute("selected", "selected");
					document.getElementById("reserve_point_out_name").value =
						row.dropoff_code + " " + row.dropoff_name;
				}
				reserve_point_out.appendChild(option);
			});
		},
	});
}

function chkRoundTrip(shift) {
	// 1. Clear the round dropdown
	let roundSelect = document.getElementById("round");
	roundSelect.value = "";
	var ck_lang = getCookie("ck_emp_lang");

	// 2. Check plant and shift condition to show/hide 'two_way' option
	$.ajax({
		type: "POST",
		url: "Reserve_ad/get_plant",
		success: function (response) {
			let plant_data = JSON.parse(response);
			let plant = plant_data.plant;

			let two_way_option = roundSelect.querySelector('option[value="two_way"]');

			if (plant == 1 && shift == "night") {
				if (two_way_option) {
					two_way_option.remove(); // ใช้ remove แทน
				}
			} else {
				let exists = roundSelect.querySelector('option[value="two_way"]');
				if (!exists) {
					let option = document.createElement("option");
					option.value = "two_way";
					if (ck_lang == "th") {
						option.text = "ไป-กลับ";
					} else {
						option.text = "Round-trip";
					}
					roundSelect.appendChild(option);
				}
			}
		},
	});

	// 3. Clear subsequent dropdowns
	let round_trip = roundSelect.value;
	if (round_trip == "") {
		document.getElementById("round_err").style.display = "none";
	}
	$("#reserve_time").empty();

	// 4. Fetch time data based on the new (empty) round trip and selected shift
	var ck_lang = getCookie("ck_emp_lang");
	$.ajax({
		type: "POST",
		url: "Reserve_ad/get_time",
		data: { reserve_trip: round_trip, shift: shift },
		success: function (response) {
			let obj = JSON.parse(response);
			console.log(obj);
			var reserve_time = document.getElementById("reserve_time");
			$("#reserve_time").empty();
			var option = document.createElement("option");
			option.value = "";
			if (ck_lang == "th") {
				option.text = "-- เลือก --";
			} else if (ck_lang == "en") {
				option.text = "-- Select --";
			}
			reserve_time.appendChild(option);
			
			// ❗ ถ้าไม่มีเวลา
			if (!obj.data || obj.data.length === 0) {
				let noOption = document.createElement("option");
				noOption.value = "";
				noOption.disabled = true;
				noOption.selected = true;
				noOption.text = (ck_lang == "th")
					? "ไม่มีเวลาเปิดขณะนี้"
					: "No available time at the moment";
				reserve_time.appendChild(noOption);
				return;
			}

			if (obj.data) {
				obj.data.forEach(function (row, index) {
					var option = document.createElement("option");
					option.value = row.shift_value;
					option.text = ck_lang === 'th' ? row.shift_text : row.shift_text_en;
					reserve_time.appendChild(option);
				});
			}
		},
	});
}

function chkPoint() {
	var reserve_route = document.getElementById("reserve_route").value;
	var ck_lang = getCookie("ck_emp_lang");
	$.ajax({
		type: "POST",
		url: "Reserve_ad/chk_point",
		data: { reserve_route: reserve_route },
		success: function (response) {
			json_data = JSON.parse(response);
			//set data in layout
			//set pickup
			var reserve_point = document.getElementById("reserve_point");
			$("#reserve_point").empty();
			var option = document.createElement("option");
			option.value = "";
			if (ck_lang == "th") {
				option.text = "-- เลือก --";
			} else if (ck_lang == "en") {
				option.text = "--select--";
			}
			reserve_point.appendChild(option);
			json_data.forEach(function (row, index) {
				pickup_id = row.pickup_id;
				pickup_code = row.pickup_code;
				pickup_name = row.pickup_name;
				var option = document.createElement("option");
				option.value = pickup_id;
				option.text = pickup_code + " " + pickup_name;
				if (pickup_id == json_data.emp_pick_id) {
					option.setAttribute("selected", "selected");
					document.getElementById("reserve_point_name").value =
						pickup_code + " " + pickup_name;
				}
				reserve_point.appendChild(option);
			});
		},
	});
}

function chkPointOut() {
	var reserve_route_out = document.getElementById("reserve_route_out").value;
	var ck_lang = getCookie("ck_emp_lang");

	$.ajax({
		type: "POST",
		url: "Reserve_ad/chk_point_out",
		data: { reserve_route_out: reserve_route_out },
		success: function (response) {
			json_data = JSON.parse(response);
			//set drop off
			var reserve_point_out = document.getElementById("reserve_point_out");
			$("#reserve_point_out").empty();
			var option = document.createElement("option");
			option.value = "";
			if (ck_lang == "th") {
				option.text = "-- เลือก --";
			} else if (ck_lang == "en") {
				option.text = "--select--";
			}
			reserve_point_out.appendChild(option);
			json_data.forEach(function (row, index) {
				dropoff_id = row.dropoff_id;
				dropoff_code = row.dropoff_code;
				dropoff_name = row.dropoff_name;
				var option = document.createElement("option");
				option.value = dropoff_id;
				option.text = dropoff_code + " " + dropoff_name;
				if (dropoff_id == json_data.emp_drop_id) {
					option.setAttribute("selected", "selected");
					document.getElementById("reserve_point_out_name").value =
						dropoff_code + " " + dropoff_name;
				}
				reserve_point_out.appendChild(option);
			});
		},
	});
}

function formatThaiDate(dateStr) {
	const days = [
		"อาทิตย์",
		"จันทร์",
		"อังคาร",
		"พุธ",
		"พฤหัสบดี",
		"ศุกร์",
		"เสาร์",
	];
	const months = [
		"ม.ค.",
		"ก.พ.",
		"มี.ค.",
		"เม.ย.",
		"พ.ค.",
		"มิ.ย.",
		"ก.ค.",
		"ส.ค.",
		"ก.ย.",
		"ต.ค.",
		"พ.ย.",
		"ธ.ค.",
	];
	const d = new Date(dateStr);
	const dayName = days[d.getDay()];
	const date = d.getDate();
	const month = months[d.getMonth()];
	return `วัน${dayName} ${date} ${month}`;
}

function addOneDay(dateStr) {
	const d = new Date(dateStr);
	d.setDate(d.getDate() + 1);
	return d.toISOString().split("T")[0];
}

function getReserveSetting() {
	return $.ajax({
		type: "POST",
		url: "Reserve_ad/get_reserve_setting",
		success: function (response) {
			json_data = JSON.parse(response);
			console.log(json_data);
		},
	});
}

function chkVal() {
	var reserve_date = document.getElementById("reserve_date").value;
	var reserve_date_end = document.getElementById("reserve_date_end").value;
	var reserve_route = document.getElementById("reserve_route").value;
	var reserve_point = document.getElementById("reserve_point").value;
	var reserve_route_out = document.getElementById("reserve_route_out").value;
	var reserve_point_out = document.getElementById("reserve_point_out").value;

	var round = document.getElementById("round").value;
	var reserve_shift = document.getElementById("shift").value;
	var trip_time = document.getElementById("reserve_time").value;
	let round_txt =
		document.getElementById("reserve_time").options[
			document.getElementById("reserve_time").selectedIndex
		].text;
	let arr_reserve_time = trip_time.split(" ");
	let reserve_time = arr_reserve_time[1];
	let reserve_trip = arr_reserve_time[0];
	var ck_lang = getCookie("ck_emp_lang");

	// --- Start Validation ---
	if (reserve_date == "") {
		const msg =
			ck_lang == "th"
				? "โปรดระบุวันที่เริ่ม"
				: "Please specify the start date.";
		Swal.fire("Warning", msg, "warning");
		return;
	}

	if (reserve_date_end == "") {
		const msg =
			ck_lang == "th"
				? "โปรดระบุวันที่สิ้นสุด"
				: "Please specify the end date.";
		Swal.fire("Warning", msg, "warning");
		return;
	}

	if (trip_time == "") {
		const msg =
			ck_lang == "th"
				? "โปรดระบุช่วงเวลาการทำงาน"
				: "Please specify working hours.";
		Swal.fire("Warning", msg, "warning");
		return;
	}

	if (round == "one_way") {
		if (reserve_trip == "in" && (reserve_route == "" || reserve_point == "")) {
			const msg =
				ck_lang == "th"
					? "โปรดระบุสายรถและจุดรับรอบเข้า"
					: "Please specify route and pickup point for arrival trip.";
			Swal.fire("Warning", msg, "warning");
			return;
		}
		if (
			reserve_trip == "out" &&
			(reserve_route_out == "" || reserve_point_out == "")
		) {
			const msg =
				ck_lang == "th"
					? "โปรดระบุสายรถและจุดส่งรอบออก"
					: "Please specify route and dropoff point for departure trip.";
			Swal.fire("Warning", msg, "warning");
			return;
		}
	} else if (round == "two_way") {
		if (reserve_route == "" || reserve_point == "") {
			const msg =
				ck_lang == "th"
					? "โปรดระบุสายรถและจุดรับรอบเข้า"
					: "Please specify route and pickup point for arrival trip.";
			Swal.fire("Warning", msg, "warning");
			return;
		}
		if (reserve_route_out == "" || reserve_point_out == "") {
			const msg =
				ck_lang == "th"
					? "โปรดระบุสายรถและจุดส่งรอบออก"
					: "Please specify route and dropoff point for departure trip.";
			Swal.fire("Warning", msg, "warning");
			return;
		}
	}

	if (reserve_date_end < reserve_date) {
		const msg =
			ck_lang == "th"
				? "ไม่สามารถเลือกวันที่สิ้นสุดน้อยกว่าวันที่เริ่มได้"
				: "You cannot select an end date that is less than the start date.";
		Swal.fire("Warning", msg, "warning");
		return;
	}
	// --- End Validation ---

	getReserveSetting()
		.done(function (response) {
			var settings = JSON.parse(response);
			if (
				settings.length > 0 &&
				settings[0].close_reserve_setting_start &&
				settings[0].close_reserve_setting_end
			) {
				var disabledStartDate = new Date(
					settings[0].close_reserve_setting_start
				);
				var disabledEndDate = new Date(settings[0].close_reserve_setting_end);
				var userStartDate = new Date(reserve_date);
				var userEndDate = new Date(reserve_date_end);

				// Check for any overlap between the user's selected range and the disabled range
				if (
					userStartDate <= disabledEndDate &&
					userEndDate >= disabledStartDate
				) {
					let title, html;
					if (ck_lang === "th") {
						title = "ปิดการจอง";
					} else {
						title = "Reservation Closed";
					}
					html = settings[0].close_reserve_setting_note;
					Swal.fire({
						icon: "error",
						title: `<span style="color: red;">${title}</span>`,
						html: `<div style="color: red;">${settings[0].close_reserve_setting_note}</div>`,
					});
					return;
				}
			}

			// --- If validation and date check passes, show confirmation ---
			const isNight = reserve_shift === "night";
			const start_date_th = formatThaiDate(reserve_date);
			const return_date_th = isNight
				? formatThaiDate(addOneDay(reserve_date))
				: formatThaiDate(reserve_date);

			Swal.fire({
				title: ck_lang == "th" ? "ยืนยันการจองรถ" : "Confirm Reservation",
				html: `
				<div style="font-size:1.2rem; text-align:center; line-height:1.6;">
					<h3>คุณจองรถรอบ <span style="color: red;"><strong>${
						reserve_trip === "in_out"
							? "ไป-กลับ"
							: reserve_trip === "in"
							? "ขาเข้า"
							: "ขาออก"
					}</strong></span></h3><br><br>
					รับเข้า: <strong style="color: red;">${
						reserve_trip === "in" || reserve_trip === "in_out"
							? start_date_th
							: "-"
					}</strong><br>
					รับออก: <strong style="color: red;">${
						reserve_trip === "out" || reserve_trip === "in_out"
							? return_date_th
							: "-"
					}</strong><br><br>
					เวลา: ${reserve_trip === "in_out" ? round_txt : reserve_time}<br>
					<span style="font-size: 0.9rem;">(${
						isNight ? "กะกลางคืน" : "กะกลางวัน"
					} ${start_date_th})</span>
				</div>`,
				icon: "question",
				showCancelButton: true,
				confirmButtonText: ck_lang == "th" ? "ยืนยัน" : "Confirm",
				cancelButtonText: ck_lang == "th" ? "ยกเลิก" : "Cancel",
			}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
						type: "POST",
						url: "Reserve_ad/save_data_reserve",
						data: {
							reserve_date: reserve_date,
							reserve_date_end: reserve_date_end,
							reserve_route_zone: reserve_route,
							reserve_point: reserve_point,
							reserve_route_zone_out: reserve_route_out,
							reserve_point_out: reserve_point_out,
							reserve_time: reserve_time,
							reserve_trip: reserve_trip,
						},
						success: function (response) {
							var obj = JSON.parse(response);
							console.log(obj);
							if (obj.data == "reserve_less_than") {
								Swal.fire(
									"Warning",
									ck_lang == "th"
										? "ไม่สามารถทำรายการวันที่ย้อนหลังได้"
										: "You cannot select an end date that is less than the start date.",
									"warning"
								);

								
							} 
							
							if (obj.data == "reserve_less_report") {
								Swal.fire(
									"Warning",
									ck_lang == "th"
										? "ไม่สามารถทำรายการหลังจากเวลาตัดรอบได้"
										: "You cannot make a reservation after the cutoff time.",
									"warning"
								);
							} 
							
							else if (obj.data == "data_dup") {
								Swal.fire({
									title: "Warning",
									text:
										ck_lang == "th"
											? "มีข้อมูลที่ผู้ใช้กำลังทำรายการจองอยู่ ต้องการดูประวัติก่อนหรือไม่?"
											: "There is information that the user is currently making a reservation. Do you want to see the history first?",
									icon: "warning",
									showCancelButton: true,
									confirmButtonText:
										ck_lang == "th" ? "ดูประวัติ" : "See History",
									cancelButtonText: ck_lang == "th" ? "ยกเลิก" : "Cancel",
								}).then((result) => {
									if (result.isConfirmed) {
										window.location.href = "Reserve_history";
									}
								});
							} else if (obj.data == "Holiday") {
								Swal.fire(
									"Warning",
									ck_lang == "th"
										? "วันดังกล่าวเป็นวันหยุดของบริษัท โปรดทำรายการจองรถวันหยุด"
										: "The said day is a company holiday. Please make a holiday car reservation.",
									"warning"
								);
							} else if (obj.data.length > 0) {
								var tb_result_reserve = $("#tb_result_reserve").DataTable();
								tb_result_reserve.clear().draw();
								obj.data.forEach((row) => {

									if(row.trip == 'in_out'){
										row.trip = ck_lang == "th" ? "ไป-กลับ" : "round-trip";
									}else if(row.trip == 'in'){
										row.trip = ck_lang == "th" ? "รับเข้า" : "In";
									}else if(row.trip == 'out'){
										row.trip = ck_lang == "th" ? "รับออก" : "Out";
									}else{    
										row.trip = ck_lang == "th" ? "ไป-กลับ" : "round-trip";
									}

									if(row.status == 'Waiting'){
										row.status = ck_lang == "th" ? "รออนุมัติ" : "Waiting Approval";
									}else if(row.status == 'Submitted'){
										row.status = ck_lang == "th" ? "อนุมัติ" : "Approved";
									}else if(row.status == 'Cancelled'){
										row.status = ck_lang == "th" ? "ยกเลิก" : "Cancelled";
									}

									tb_result_reserve.row
										.add([row.reserve_date, row.trip, row.status])
										.draw();
								});
								$("#result_reservation").modal("show");
							} else {
								Swal.fire(
									"Warning",
									ck_lang == "th"
										? "มีสิ่งผิดปกติเกิดขึ้น"
										: "Something went wrong !!.",
									"warning"
								);
							}
						},
						error: function (error) {
							console.error(error);
							Swal.fire(
								"Error",
								ck_lang == "th"
									? "เกิดข้อผิดพลาดในการบันทึกข้อมูล"
									: "An error occurred while saving data.",
								"error"
							);
						},
					});
				}
			});
		})
		.fail(function () {
			Swal.fire(
				"Error",
				ck_lang == "th"
					? "ไม่สามารถโหลดการตั้งค่าได้"
					: "Could not load settings.",
				"error"
			);
		});
}

function historyVal() {
	window.location.href = "Reserve_history";
}

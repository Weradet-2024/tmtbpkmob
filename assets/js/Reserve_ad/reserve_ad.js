$(document).ready(function () {
	//$('#result_reservation').modal('show');
	getData();
	var today = new Date();
	var endDate = new Date();
	endDate.setDate(today.getDate() + 60);


	$("#reserve_date").datepicker({
	    format: "yyyy-mm-dd",
	    todayHighlight: true,
	    disableTouchKeyboard: true,
	    autoclose: true,
	    startDate: today,
	    endDate: endDate
	    // daysOfWeekDisabled: [0, 6] // Disable Sunday (0) and Saturday (6)
	});

	$("#reserve_date_end").datepicker({
	    format: "yyyy-mm-dd",
	    todayHighlight: true,
	    disableTouchKeyboard: true,
	    autoclose: true,
	    startDate: today,
	    endDate: endDate
	    // daysOfWeekDisabled: [0, 6] // Disable Sunday (0) and Saturday (6)
	});

	// $("#reserve_date").datepicker({
	//     format: "yyyy-mm-dd",
	//     todayHighlight: true,
	//     disableTouchKeyboard: true,
	//     autoclose: true
	// });
	// $("#reserve_date_end").datepicker({
	//     format: "yyyy-mm-dd",
	//     todayHighlight: true,
	//     disableTouchKeyboard: true,
	//     autoclose: true
	// });

	$("#tb_result_reserve").DataTable({
		paging: true, // Disable pagination
		pageLength:5,
		ordering: false, // Disable column ordering
		searching: false, // Disable search/filtering
		info: false, // Disable table information display
		bLengthChange: false, // Disable the ability to change the number of rows displayed
	});

	$('.select2').select2({
        width: '100%'
    });



});

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("round").addEventListener("change", function () {
        clearSelections([ "reserve_route", "reserve_route_out"]);
    });

    document.getElementById("reserve_time").addEventListener("change", function () {
        clearSelections(["reserve_route", "reserve_route_out"]);
    });

    function clearSelections(ids) {
        ids.forEach(id => {
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

function showLoader() {
    var overlay = document.getElementById('loading-overlay');
    if (overlay) overlay.style.display = 'flex';
  }
function backVal() {
	showLoader();
	window.location.href = "Home";
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

function chkTrip(trip) {
	console.log(trip);

	$.ajax({
		type: "POST",
		url: "Reserve_ad/chk_trip",
		data: { trip: trip },
		success: function (response) {
			console.log(response);

			json_data = JSON.parse(response);
			const arr_trip = trip.split(" ");

			if (arr_trip[0] == "in") {
				$("#route_in").show();
				$("#point_in").show();
				$("#route_out").hide();
				$("#point_out").hide();
			} else if (arr_trip[0] == "out") {
				$("#route_out").show();
				$("#point_out").show();
				$("#route_in").hide();
				$("#point_in").hide();
			} else if (arr_trip[0] == "in_out") {
				$("#route_in, #point_in, #route_out, #point_out").show();
			} else {
				$("#route_in, #point_in, #route_out, #point_out").hide();
			}

			// ---------------------------
			// ROUTE (ขาเข้า)
			// ---------------------------
			var ck_lang = getCookie("ck_emp_lang");
			var reserve_route = $("#reserve_route");
			reserve_route.empty().append(
				$("<option>", {
					value: "",
					text: ck_lang == "th" ? "-- เลือก --" : "-- Select --"
				})
			);

			json_data.route.forEach(function (row) {
				var option = $("<option>", {
					value: row.route_id,
					text: row.route_code + " " + row.route_name
				});
				if (row.route_id == json_data.emp_route_id) {
					option.attr("selected", "selected");
					$("#reserve_route_name").val(row.route_code + " " + row.route_name);
				}
				reserve_route.append(option);
			});

			// ---------------------------
			// PICKUP
			// ---------------------------
			var reserve_point = $("#reserve_point");
			reserve_point.empty().append(
				$("<option>", {
					value: "",
					text: ck_lang == "th" ? "-- เลือก --" : "-- Select --"
				})
			);

			json_data.pickup.forEach(function (row) {
				var option = $("<option>", {
					value: row.pickup_id,
					text: row.pickup_code + " " + row.pickup_name
				});
				if (row.pickup_id == json_data.emp_pick_id) {
					option.attr("selected", "selected");
					$("#reserve_point_name").val(row.pickup_code + " " + row.pickup_name);
				}
				reserve_point.append(option);
			});

			// ---------------------------
			// ROUTE OUT (ขาออก)
			// ---------------------------
			var reserve_route_out = $("#reserve_route_out");
			reserve_route_out.empty().append(
				$("<option>", {
					value: "",
					text: ck_lang == "th" ? "-- เลือก --" : "-- Select --"
				})
			);

			json_data.route_out.forEach(function (row) {
				var option = $("<option>", {
					value: row.route_out_id,
					text: row.route_out_code + " " + row.route_out_name
				});
				if (row.route_out_id == json_data.emp_route_out_id) {
					option.attr("selected", "selected");
					$("#reserve_route_out_name").val(row.route_out_code + " " + row.route_out_name);
				}
				reserve_route_out.append(option);
			});

			// ---------------------------
			// DROPOFF
			// ---------------------------
			var reserve_point_out = $("#reserve_point_out");
			reserve_point_out.empty().append(
				$("<option>", {
					value: "",
					text: ck_lang == "th" ? "-- เลือก --" : "-- Select --"
				})
			);

			json_data.dropoff.forEach(function (row) {
				var option = $("<option>", {
					value: row.dropoff_id,
					text: row.dropoff_code + " " + row.dropoff_name
				});
				if (row.dropoff_id == json_data.emp_drop_id) {
					option.attr("selected", "selected");
					$("#reserve_point_out_name").val(row.dropoff_code + " " + row.dropoff_name);
				}
				reserve_point_out.append(option);
			});
		},
	});
}


function chkRoundTrip() {

	let round_trip = document.getElementById("round").value;

	if(round_trip == ""){
		document.getElementById("round_err").style.display = "none";
	}
	
	var ck_lang = getCookie("ck_emp_lang");
	if (ck_lang == "th") {
		var trip_in = "รับเข้า";
		var trip_out = "รับออก";
		var trip_in_out = "ไป-กลับ";
		// var shift_day = "กะเช้า";
		// var shift_night = "กะกลางคืน";
	} else {
		var trip_in = "Trip In";
		var trip_out = "Trip Out";
		var trip_in_out = "Round trip";
		// var shift_day = "Morning Shift";
		// var shift_night = "Night Shift";
	}
	$.ajax({
		type: "POST",
		url: "Reserve_ad/get_time",
		data: { reserve_trip: round_trip },
		success: function (response) {
			let obj = JSON.parse(response);
			var reserve_time = document.getElementById("reserve_time");
			console.log(reserve_time);
			
			$("#reserve_time").empty();
			var option = document.createElement("option");
			option.value = "";
			if (ck_lang == "th") {
				option.text = "-- เลือก --";
			} else if (ck_lang == "en") {
				option.text = "-- Select --";
			}
			reserve_time.appendChild(option);
			obj.data.forEach(function (row, index) {

				console.log(row);
				shift_value = row.shift_value;
				shift_text = row.shift_text;


				var option = document.createElement("option");
				option.value = shift_value;

				let [tripType, timeRaw] = row.shift_value.split(" ");
				let time = timeRaw ? timeRaw.replace(/-\d+$/, "") : "";
				let inoutTime = row.shift_text.replace(/^Round-trip\s*/, "");

				console.log(tripType);

				let text = shift_text.split(' '); // splits every character into an array element

				console.log(text);

				let label = '';
				if(tripType == "in_out"){
					label = trip_in_out + " " + inoutTime;
				}else{
					label = (tripType === "in" ? trip_in : trip_out) + " " + time;
				}
				
				option.text = label;
				//one way = value = time
				//two eay = value = shift_id
				reserve_time.appendChild(option);
			});
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

function chkVal() {
	var reserve_date 		= document.getElementById("reserve_date").value;
	var reserve_date_end 	= document.getElementById("reserve_date_end").value;
	var reserve_route 		= document.getElementById("reserve_route").value;
	var reserve_point 		= document.getElementById("reserve_point").value;
	var reserve_route_out 	= document.getElementById("reserve_route_out").value;
	var reserve_point_out 	= document.getElementById("reserve_point_out").value;

	var round = document.getElementById("round").value;
	//get reserve time and reserve trip
	var trip_time = document.getElementById("reserve_time").value;
	let arr_reserve_time = trip_time.split(" ");
	let reserve_time = arr_reserve_time[1];
	let reserve_trip = arr_reserve_time[0];
	var ck_lang = getCookie("ck_emp_lang");

	//console.log("ok");

		if (reserve_date == "") {
			if (ck_lang == "th") {
				Swal.fire(
					"Warning",
					"โปรดระบุช่วงวันที่ต้องการจอง",
					"warning"
				);
			} else {
				Swal.fire(
					"Warning",
					"Please specify reservation date range.",
					"warning"
				);
			}
			return;
		} 
		
		if (reserve_date_end == "") {
			if (ck_lang == "th") {
				Swal.fire(
					"Warning",
					"โปรดระบุช่วงวันที่ต้องการจอง",
					"warning"
				);
			} else {
				Swal.fire(
					"Warning",
					"Please specify reservation date range.",
					"warning"
				);
			}

			return;

		} 
		
		if (reserve_time == "") {
			if (ck_lang == "th") {
				Swal.fire("Warning", "โปรดระบุช่วงเวลาการทำงาน", "warning");
			} else {
				Swal.fire("Warning", "Please specify working hours.", "warning");
			}

			return;

		}

	
		////// เข้าเงื่อนไข ปกติ 
		if(round == "one_way"){
			if (
				(reserve_trip == "in") &&
				reserve_route == ""
			) {
				if (ck_lang == "th") {
					Swal.fire("Warning", "สายรถรอบรับเข้าไม่ได้กำหนด", "warning");
				} else {
					Swal.fire("Warning", "The pick-up bus line is not specified.", "warning");
				}

				if (
					(reserve_trip == "in") &&
					reserve_point == ""
				) {
					if (ck_lang == "th") {
						Swal.fire("Warning", "จุดรับไม่ได้กำหนด", "warning");
					} else {
						Swal.fire("Warning", "The pick-up point is not specified.", "warning");
					}
				} 
				return;
			}

			if (
				(reserve_trip == "out") &&
				reserve_route_out == ""
			) {
				if (ck_lang == "th") {
					Swal.fire("Warning", "สายรถรอบรับออกไม่ได้กำหนด", "warning");
				} else {
					Swal.fire("Warning", "The bus route is not specified.", "warning");
				}
				return;
			}

			if (
				(reserve_trip == "out") &&
				reserve_point_out == ""
			) {
				if (ck_lang == "th") {
					Swal.fire("Warning", "จุดส่งไม่ได้กำหนด", "warning");
				} else {
					Swal.fire("Warning", "Dropoff point not specified", "warning");
				}
				return;
			}

		}else if(round == "two_way"){

			if (
				(reserve_trip == "in" || reserve_trip == "in_out") &&
				reserve_route == ""
			) {
				if (ck_lang == "th") {
					Swal.fire("Warning", "สายรถรอบรับเข้าไม่ได้กำหนด", "warning");
				} else {
					Swal.fire("Warning", "The pick-up bus line is not specified.", "warning");
				}

				if (
					(reserve_trip == "in" || reserve_trip == "in_out") &&
					reserve_point == ""
				) {
					if (ck_lang == "th") {
						Swal.fire("Warning", "จุดรับไม่ได้กำหนด", "warning");
					} else {
						Swal.fire("Warning", "The pick-up point is not specified.", "warning");
					}
				} 
				return;
			}

			if (
				(reserve_trip == "out" || reserve_trip == "in_out") &&
				reserve_route_out == ""
			) {
				if (ck_lang == "th") {
					Swal.fire("Warning", "สายรถรอบรับออกไม่ได้กำหนด", "warning");
				} else {
					Swal.fire("Warning", "The bus route is not specified.", "warning");
				}
				return;
			}

			if (
				(reserve_trip == "out" || reserve_trip == "in_out") &&
				reserve_point_out == ""
			) {
				if (ck_lang == "th") {
					Swal.fire("Warning", "จุดส่งไม่ได้กำหนด", "warning");
				} else {
					Swal.fire("Warning", "Dropoff point not specified", "warning");
				}

				return;
			}

			if (
				(reserve_trip == "out" || reserve_trip == "in_out") &&
				reserve_point_out == ""
			) {
				if (ck_lang == "th") {
					Swal.fire("Warning", "จุดส่งไม่ได้กำหนด", "warning");
				} else {
					Swal.fire("Warning", "Dropoff point not specified", "warning");
				}
				return;
			} 

		}
		
		if (trip_time == "") {
			if (ck_lang == "th") {
				Swal.fire("Warning", "รอบไม่ได้กำหนด", "warning");
			} else {
				Swal.fire("Warning", "Round not specified.", "warning");
			}
			return;
		} 	
		
		if (reserve_date_end < reserve_date) {
			if (ck_lang == "th") {
				Swal.fire(
					"Warning",
					"ไม่สามารถเลือกวันที่สิ้นสุดน้อยกว่าวันที่เริ่มได้",
					"warning"
				);
			} else {
				Swal.fire(
					"Warning",
					"Unable to make past date reservation.",
					"warning"
				);
			}

			return;
		} else {
			console.log("ok");
			console.log(reserve_date, 	
				reserve_date_end ,
				reserve_route, 	
				reserve_point, 	
				reserve_route_out, 
				reserve_point_out,
			reserve_time );

			if(reserve_trip == 'out'){
				arr_reserve_time = reserve_time.split('-');
				reserve_time = arr_reserve_time[0];
			}

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
					console.log(response);
					obj = JSON.parse(response);
					console.log(obj);
					if (obj.data == "reserve_less_than") {
						if (ck_lang == "th") {
							Swal.fire(
								"Warning",
								"ไม่สามารถทำรายการวันที่ย้อนหลังได้",
								"warning"
							);
						} else {
							Swal.fire(
								"Warning",
								"Unable to make past date reservation.",
								"warning"
							);
						}
					} else if (obj.data == "data_dup") {
						if (ck_lang == "th") {
							Swal.fire({
								title: "Warning",
								html: "มีรายการจองซ้ำในระบบ<br>โปรดตรวจสอบประวัติการจอง",
								icon: "warning",
								showCancelButton: true,
								confirmButtonText: "ดูประวัติ",
								cancelButtonText: "ยกเลิก",
							}).then((result) => {
								if (result.isConfirmed) {
									window.location.href = "Reserve_history";
								}
							});
						} else {
							Swal.fire({
								title: "Warning",
								text: "There is existing reservation. See reservation history?",
								icon: "warning",
								showCancelButton: true,
								confirmButtonText: "See History",
								cancelButtonText: "Cancel",
							}).then((result) => {
								if (result.isConfirmed) {
									window.location.href = "Reserve_history";
								}
							});
						}
					} else if (obj.data == "Holiday") {
						if (ck_lang == "th") {
							Swal.fire(
								"Warning",
								"ไม่มีรถให้บริการในวันที่เลือก",
								"warning"
							);
						} else {
							Swal.fire(
								"Warning",
								"No bus operation on selected date.",
								"warning"
							);
						}
					}else if (obj.data == "ng_max_days") {
						if (ck_lang == "th") {
							Swal.fire(
								"Warning",
								"ไม่สามารถทำรายการจองรถวันทำงานเกิน 14 วันได้ กรุณาเลือกใหม่ ",
								"warning"
							);
						} else {
							Swal.fire(
								"Warning",
								"Booking cannot exceed 14 days. Please choose a new date range.",
								"warning"
							);
						}
					} else if (obj.data == "ng_cut_of_time") {
						if (ck_lang == "th") {
							Swal.fire(
								"Warning",
								"ไม่สามารถทำการจองในระบบได้หลังเวลาตัดรอบ",
								"warning"
							);
						} else {
							Swal.fire(
								"Warning",
								"Unable to make reservation after cut-off time.",
								"warning"
							);
						}
					} else if (obj.data === false || obj.data === "false") {
						if (ck_lang == "th") {
							Swal.fire(
								"Warning",
								"มีสิ่งผิดปกติเกิดขึ้น ไม่สามารถเรียกข้อมูลเวลากะทำงานได้",
								"warning"
							);
						} else {
							Swal.fire(
								"Warning",
								"Something went wrong. The shift time data could not be retrieved.",
								"warning"
							);
						}
					} else if (obj.data == "reserve_less_report") {
						Swal.fire(
							"Unable to make reservation after cut-off time.",
							"ไม่สามารถทำการจองในระบบได้หลังเวลาตัดรอบ",
							"warning"
						);
					} else if (obj.data.length <= 0) {
						if (ck_lang == "th") {
							Swal.fire(
								"Warning",
								"โปรดตรวจสอบวันที่จอง อาจเลือกวันหยุดทั้งหมด",
								"warning"
							);
						} else {
							Swal.fire(
								"Warning",
								"Please check the booking date. All holidays may be selected.",
								"warning"
							);
						}
					} else if (obj.data.length >= 0) {
						//create reservation data
						var tb_result_reserve = $("#tb_result_reserve").DataTable();
						tb_result_reserve.clear().draw();
						let waitng_confirm = getCookie('ck_emp_lang') === "th"? "รอ Confirm" : "Waiting Confirm";
						let confirm = getCookie('ck_emp_lang') === "th"? "ยืนยันการจอง" : "Confirm";
						let text_status = '';
						
						 obj.data.forEach((row, index) => {
							let round_trip = ""
							if (row.trip == "Round-trip") {
								round_trip = "Round trip";
							} else if (row.trip == "out") {
								round_trip = "Out";
							} else if (row.trip == "in") {
								round_trip = "In";
							} else {
								round_trip = row.trip
							}
							tb_result_reserve.row
								.add([row.reserve_date, round_trip])
								.draw();
						});
						$("#result_reservation").modal("show");
					} else {
						if (ck_lang == "th") {
							Swal.fire("Warning", "มีสิ่งผิดปกติเกิดขึ้น", "warning");
						} else {
							Swal.fire("Warning", "Something went wrong !!.", "warning");
						}
					}
				},
				error: function (error) {
					console.error(error);
				},
			});
		}
		///////////


}

function historyVal() {
	window.location.href = "Reserve_history";
}

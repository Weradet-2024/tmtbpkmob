$(document).ready(function () {
	//$('#result_reservation').modal('show');
	getData();
	$("#reserve_date").datepicker({
		format: "yyyy-mm-dd",
		todayHighlight: true,
		disableTouchKeyboard: true,
		autoclose: true,
	});
	$("#tb_result_reserve").DataTable({
		paging: false, // Disable pagination
		ordering: false, // Disable column ordering
		searching: false, // Disable search/filtering
		info: false, // Disable table information display
		bLengthChange: false, // Disable the ability to change the number of rows displayed
	});
});

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("round").addEventListener("change", function () {
        clearSelections(["shift", "reserve_time", "reserve_route", "reserve_route_out"]);
    });

    document.getElementById("shift").addEventListener("change", function () {
        clearSelections(["reserve_time", "reserve_route", "reserve_route_out"]);
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

function backVal() {
	window.location.href = "Home";
}

function getData() {
	$.ajax({
		type: "POST",
		url: "Reserve_holiday_ad/get_data",
		success: function (response) {
			json_data = JSON.parse(response);
			console.log(json_data);
			document.getElementById("reserve_name").value = json_data.name;
			document.getElementById("reserve_name").readOnly = true;
			document.getElementById("reserve_date").value = json_data.today;
			// document.getElementById("reserve_date_end").value = json_data.today;
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
	$.ajax({
		type: "POST",
		url: "Reserve_holiday_ad/chk_trip",
		data: { trip: trip },
		success: function (response) {
			console.log(response);
			json_data = JSON.parse(response);
			//console.log(trip);
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

			//set data in layout
			//set route
			var ck_lang = getCookie("ck_emp_lang");
			var reserve_route = document.getElementById("reserve_route");
			$("#reserve_route").empty();
			var option = document.createElement("option");
			option.value = "";
			if (ck_lang == "th") {
				option.text = "-- เลือก --";
			} else if (ck_lang == "en") {
				option.text = "-- Select --";
			}
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

			//set pickup
			var reserve_point = document.getElementById("reserve_point");
			$("#reserve_point").empty();
			var option = document.createElement("option");
			option.value = "";
			if (ck_lang == "th") {
				option.text = "-- เลือก --";
			} else if (ck_lang == "en") {
				option.text = "-- Select --";
			}
			reserve_point.appendChild(option);
			json_data.pickup.forEach(function (row, index) {
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

			//set route_out data
			var reserve_route_out = document.getElementById("reserve_route_out");
			$("#reserve_route_out").empty();
			var option = document.createElement("option");
			option.value = "";
			if (ck_lang == "th") {
				option.text = "-- เลือก --";
			} else if (ck_lang == "en") {
				option.text = "-- Select --";
			}
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
			//set drop off
			var reserve_point_out = document.getElementById("reserve_point_out");
			$("#reserve_point_out").empty();
			var option = document.createElement("option");
			option.value = "";
			if (ck_lang == "th") {
				option.text = "-- เลือก --";
			} else if (ck_lang == "en") {
				option.text = "-- Select --";
			}
			reserve_point_out.appendChild(option);
			json_data.dropoff.forEach(function (row, index) {
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
		},error:function(xhr){
			console.error(xhr);
		}
	});
}

function chkRoundTrip(shift) {
	let round_trip = document.getElementById("round").value;

	if(round_trip == ""){
		document.getElementById("round_err").style.display = "block";
	}

	$("#reserve_time").empty();
	var ck_lang = getCookie("ck_emp_lang");
	if (ck_lang == "th") {
		var trip_in = "รับเข้า";
		var trip_out = "รับออก";
		var shift_day = "กะเช้า";
		var shift_night = "กะกลางคืน";
	} else if (ck_lang == "en") {
		var trip_in = "Trip IN";
		var trip_out = "Trip OUT";
		var shift_day = "Morning Shift";
		var shift_night = "Night Shift";
	} else {
		var trip_in = "Trip IN";
		var trip_out = "Trip OUT";
		var shift_day = "Morning Shift";
		var shift_night = "Night Shift";
	}
	$.ajax({
		type: "POST",
		url: "Reserve_holiday_ad/get_time",
		data: { reserve_trip: round_trip, shift:shift},
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
			obj.data.forEach(function (row, index) {
				shift_value = row.shift_value;
				shift_text = row.shift_text;
				var option = document.createElement("option");
				option.value = shift_value;
				option.text = shift_text;
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
		url: "Reserve_holiday_ad/chk_point",
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
		url: "Reserve_holiday_ad/chk_point_out",
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
	let reserve_date = document.getElementById("reserve_date").value;
	let reserve_route = document.getElementById("reserve_route").value;
	let reserve_point = document.getElementById("reserve_point").value;
	let reserve_route_out = document.getElementById("reserve_route_out").value;
	let reserve_point_out = document.getElementById("reserve_point_out").value;
	//get reserve time and reserve trip
	let trip_time = document.getElementById("reserve_time").value;
	let arr_reserve_time = trip_time.split(" ");
	let reserve_time = arr_reserve_time[1];
	let reserve_trip = arr_reserve_time[0];
	var ck_lang = getCookie("ck_emp_lang");

	var round = document.getElementById("round").value;

	console.log(round);

	if (reserve_date == "") {
		if (ck_lang == "th") {
			Swal.fire(
				"Warning",
				"โปรดระบุวันที่เริ่ม ( รอบรับเข้า ) รอบรับออกจะเลือกโดยอัตโนมัติ",
				"warning"
			);
		} else {
			Swal.fire(
				"Warning",
				"Please specify the start date (receipt round), the outgoing round will be selected automatically.",
				"warning"
			);
		}

		console.log("test");

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

		console.log("test");

	}
	
	if(round == "two_way"){

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

		console.log("test");

	}
	
	
	if (trip_time == "") {
		if (ck_lang == "th") {
			Swal.fire("Warning", "รอบไม่ได้กำหนด", "warning");
		} else {
			Swal.fire("Warning", "Round not specified.", "warning");
		}

		return;

		console.log("test");
	}else {

		console.log(reserve_date, 	
			reserve_route, 	
			reserve_point, 	
			reserve_route_out, 
			reserve_point_out );

		$.ajax({
			type: "POST",
			url: "Reserve_holiday_ad/save_data_reserve",
			data: {
				reserve_date: reserve_date,
				reserve_route: reserve_route,
				reserve_point: reserve_point,
				reserve_route_out: reserve_route_out,
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
						Swal.fire("Warning", "You cannot select an end date that is less than the start date.", "warning");
					}
				} else if (obj.data == "data_dup") {
					if (ck_lang == "th") {
						Swal.fire({
							title: 'Warning',
							text: 'มีข้อมูลที่ผู้ใช้กำลังทำรายการจองอยู่ ต้องการดูประวัติก่อนหรือไม่?',
							icon: 'warning',
							showCancelButton: true,
							confirmButtonText: 'ดูประวัติ',
							cancelButtonText: 'ยกเลิก'
						}).then((result) => {
							if (result.isConfirmed) {
								window.location.href = "Reserve_history";
							}
						});
					} else {
						Swal.fire({
							title: 'Warning',
							text: 'There is existing reservation. See reservation history?',
							icon: 'warning',
							showCancelButton: true,
							confirmButtonText: 'See History',
							cancelButtonText: 'Cancel'
						}).then((result) => {
							if (result.isConfirmed) {
								window.location.href = "Reserve_history";
							}
						});
					}
				}else if (obj.data == "Weekday") {
					if (ck_lang == "th") {
						Swal.fire(
							"Warning",
							"วันดังกล่าวเป็นวันทำงานของบริษัท โปรดทำรายการจองรถวันทำงาน",
							"warning"
						);
					} else {
						Swal.fire("Warning", "The said day is a company weekday. Please make a weekday car reservation.", "warning");
					}
				}else if (obj.data == "ng_cut_of_time") {
					if (ck_lang == "th") {
						Swal.fire(
							"Warning",
							"มีสิ่งผิดปกติเกิดขึ้น ไม่สามารถเรียกข้อมูลเวลาปิดระบบได้",
							"warning"
						);
					} else {
						Swal.fire("Warning", "Something went wrong. Unable to retrieve Close of time data.", "warning");
					}
				}else if (obj.data === false || obj.data === "false") {
					if (ck_lang == "th") {
						Swal.fire(
							"Warning",
							"มีสิ่งผิดปกติเกิดขึ้น ไม่สามารถเรียกข้อมูลเวลากะทำงานได้",
							"warning"
						);
					} else {
						Swal.fire("Warning", "Something went wrong. The shift time data could not be retrieved.", "warning");
					}
				}
				
				else if(obj.data == "reserve_less_report"){
					Swal.fire(
						"Warning Time to Start the Shuttle",
						"ไม่สามารถทำการจองเนื่องจาก อยู่ในช่วงการจัดรถ",
						"warning"
					);
				}
				
				else if(obj.data.length <= 0){
					if (ck_lang == "th") {
						Swal.fire(
							"Warning",
							"โปรดตรวจสอบวันที่จอง",
							"warning"
						);
					} else {
						Swal.fire("Warning", "Please check the booking date.", "warning");
					}
				}else if(obj.data === "Waiting"){

					if (ck_lang == "th") {
						Swal.fire({
							title: 'Warning',
							text: 'ขณะนี้เกินกว่าเวลา cut of time คำขอของคุณอาจไม่ถูกจัดรถ โปรดติดต่อผู้ประสานงาน',
							icon: 'warning',
							showCancelButton: true,
							confirmButtonText: 'ตกลง',
							cancelButtonText: 'ยกเลิก'
						}).then((result) => {
							if (result.isConfirmed) {
								window.location.href = "Reserve_history";
							}
						});
					} else {
						Swal.fire({
							title: 'Warning',
							text: 'As this time exceeds the cut of time, your request may not be arranged. Please contact the coordinator.',
							icon: 'warning',
							showCancelButton: true,
							confirmButtonText: 'OK',
							cancelButtonText: 'Cancel'
						}).then((result) => {
							if (result.isConfirmed) {
								window.location.href = "Reserve_history";
							}
						});
					}

				}else if(obj.data == "Submitted"){
                    if (ck_lang == "th") {
						Swal.fire({
							title: 'Success',
							text: 'คำขอจองรถสำเร็จ',
							icon: 'success',
							showCancelButton: true,
							confirmButtonText: 'ตกลง',
							cancelButtonText: 'ยกเลิก'
						}).then((result) => {
							if (result.isConfirmed) {
								window.location.href = "Reserve_history";
							}
						});
					} else {
						Swal.fire({
							title: 'Warning',
							text: 'The car reservation request was successful.',
							icon: 'success',
							showCancelButton: true,
							confirmButtonText: 'OK',
							cancelButtonText: 'Cancel'
						}).then((result) => {
							if (result.isConfirmed) {
								window.location.href = "Reserve_history";
							}
						});
					}
                }
                else{
					if (ck_lang == "th") {
						Swal.fire(
							"Warning",
							"มีสิ่งผิดปกติเกิดขึ้น",
							"warning"
						);
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
}

function historyVal(){

}

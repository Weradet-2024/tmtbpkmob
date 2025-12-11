$(document).ready(function () {
	$("#tbActRoute").DataTable({
		paging: false, // Disable pagination
		ordering: false, // Disable column ordering
		info: false, // Disable table information display
		bLengthChange: false, // Disable the ability to change the number of rows displayed
	});
	getData();
});

var lang = $('#ck_emp_lang').val();
console.log(ck_emp_lang);

function getData() {
	$.ajax({
		type: "POST",
		url: "Tracking_manual/get_data",
		success: function (response) {
			console.log(response);
			json_data = JSON.parse(response);
			// Check the language and format the date
			if (lang === "th") {
				const thaiDays = {
					"Sunday": "วันอาทิตย์",
					"Monday": "วันจันทร์",
					"Tuesday": "วันอังคาร",
					"Wednesday": "วันพุธ",
					"Thursday": "วันพฤหัสบดี",
					"Friday": "วันศุกร์",
					"Saturday": "วันเสาร์"
				};

				const thaiMonths = {
					"January": "มกราคม",
					"February": "กุมภาพันธ์",
					"March": "มีนาคม",
					"April": "เมษายน",
					"May": "พฤษภาคม",
					"June": "มิถุนายน",
					"July": "กรกฎาคม",
					"August": "สิงหาคม",
					"September": "กันยายน",
					"October": "ตุลาคม",
					"November": "พฤศจิกายน",
					"December": "ธันวาคม"
				};

				// Split the date into parts
				const dateParts = json_data.date.split(" ");
				if (dateParts.length === 4) {
					const day = thaiDays[dateParts[0]] || dateParts[0]; // Fallback to original if not found
					const date = dateParts[1];
					const month = thaiMonths[dateParts[2]] || dateParts[2]; // Fallback to original if not found
					const year = parseInt(dateParts[3], 10); // Parse the year as an integer

					if (!isNaN(year)) {

						const thaiYear = year + 543; // Convert to Buddhist calendar year
						document.getElementById("lbl_date").innerHTML = `${day}ที่ ${date} ${month} ${year}`;
						
					} else {
						console.error("Invalid year format:", dateParts[3]);
					}
				} else {
					console.error("Unexpected date format:", json_data.date);
				}
			} else {
				// For English, show the original date
				const formattedDate = json_data.date.replace("2568", "2025");
				document.getElementById("lbl_date").innerHTML = formattedDate;
			}
			// document.getElementById("lbl_date").innerHTML = json_data.date;
			//set_selection_plant(json_data.plant);
			set_selection_trip(json_data.trip);
		},
	});
}

function set_selection_plant(data) {
	var tracking_manual_plant = document.getElementById("tracking_manual_plant");
	$("#tracking_manual_plant").empty();
	//loop show data
	data.forEach(function (row, index) {
		plant_id = row.plant_id;
		plant_name = row.plant_name;
		var option = document.createElement("option");
		option.value = plant_id;
		option.text = plant_name;
		tracking_manual_plant.appendChild(option);
	});
};

function set_selection_trip(data) {
	var tracking_manual_trip = document.getElementById("tracking_manual_trip");
	var option = document.createElement("option");
	$("#tracking_manual_trip").empty();
	option.value = '';
	if (lang === "th") {
		option.text = '-- เลือก --';
	} else {
		option.text = '-- Select --';
	}
	tracking_manual_trip.appendChild(option);
	//loop show data
	data.forEach(function (row, index) {
		var option = document.createElement("option");
		if (lang == "th") {
			if (row == "in") {
				var lbl_row = "รอบรับเข้า";
			} else {
				var lbl_row = "รอบรับออก";
			}
		} else if (lang == "en") {
			if (row == "out") {
				var lbl_row = "In";
			} else {
				var lbl_row = "Out";
			}
		}
		option.value = row;
		option.text = lbl_row;
		tracking_manual_trip.appendChild(option);
	});
}

function getRouteData() {
	let trip = document.getElementById('tracking_manual_trip').value;
	$.ajax({
		type: "POST",
		url: "Tracking_manual/get_route",
		data: {
			trip: trip
		},
		success: function (response) {
			json_data = JSON.parse(response);
			console.log(json_data);
			set_selection_route(json_data.data);
		},
		error: function (error) {
			console.error(error);
		}
	});
}

function set_selection_route(route_data) {
	var tracking_manual_route = document.getElementById("tracking_manual_route");
	$("#tracking_manual_route").empty();
	var option = document.createElement("option");
	option.value = "";
	if (lang === "th") {
		option.text = "-- เลือก --";
	} else {
		option.text = "-- Select --";
	}
	tracking_manual_route.appendChild(option);
	var option = document.createElement("option");
	option.value = "all";
	option.text = "แสดงทุกสาย";
	tracking_manual_route.appendChild(option);
	//loop show data
	route_data.forEach(function (row, index) {
		route_id = row.route_id;
		route_code = row.route_code;
		route_name = row.route_name;
		var option = document.createElement("option");
		option.value = route_id;
		option.text = route_code + " " + route_name;
		tracking_manual_route.appendChild(option);
	});
};

function chkActiveRoute() {
	var route = document.getElementById('tracking_manual_route').value;
	var trip = document.getElementById('tracking_manual_trip').value;
	if (trip == "") {
		//swal('Warning', 'โปรดเลือกรอบการเดินรถ', 'warning');
		Swal.fire(
			"Warning",
			"โปรดเลือกรอบการเดินรถ",
			"warning"
		);
	} else if (route == "") {
		//swal('Warning', 'โปรดเลือกรอบการเดินรถ', 'warning');
		Swal.fire(
			"Warning",
			"โปรดเลือกสายรถ",
			"warning"
		);
	} else {
		$.ajax({
			type: "POST",
			url: "Tracking_manual/chk_active_route",
			data: { trip: trip, route: route },
			success: function (response) {

				console.log("Check :" + response);

				html_code = '';

				if (response == "Cannot") {
					Swal.fire(
						"Warning",
						"<span class='text-danger' style='font-size:18px;'>ไม่พบรถที่ให้บริการในรอบนี้ <br> หรือยังไม่ถึงเวลาเริ่มให้บริการ</span>",
						"warning"
					);

					// var tbActRoute = $('#tbActRoute').DataTable();
					// tbActRoute.clear().draw();
					// html_code += '<td colspan = "4" data-aos="fade-left" style="border-radius:5px;  padding:5px; text-align:center;">';
					// html_code += "<span class='text-danger' style='font-size:14px;'>ไม่พบรถที่ให้บริการในรอบนี้ <br> หรือยังไม่ถึงเวลาเริ่มให้บริการ</span>";
					// html_code += '</td>';
					// tbActRoute.row.add([html_code]).draw();

				} else {

					json_data = JSON.parse(response);

					var tbActRoute = $('#tbActRoute').DataTable();

					tbActRoute.clear().draw();

					// console.table(json_data);

					//loop show data
					json_data.forEach(function (row, index) {

						no = (index + 1);
						route_name = row.route_name;
						post_id = row.post_id;
						post_time = row.post_time;
						veh_license = row.veh_license;
						data_status = row.status;
						curr_date = row.curr_date;
						trip = row.trip;
						post_combined_route = row.post_combined_route;
						post_veh_id = row.post_veh_id;
						post_drv_id = row.post_drv_id;
						route_status = row.route_status;

						html_status = "";

						if (data_status == "Low_Signal") {

							if (lang === "th") {
								html_status += "<span class='text-danger'>สัญญาณอ่อน</span>";
							} else {
								html_status += "<span class='text-danger'>Low Signal</span>";
							}


						} else if (data_status == "Success") {

							if (lang === "th") {
								html_status += "<span class='text-success'>ถึงแล้ว</span>";
							} else {
								html_status += "<span class='text-success'>Arrange</span>";
							}


						} else if (data_status == "Go") {

							html_status += '<button type="button" class="btn btn-primary" style="padding: 0px 10px 0px 10px !important;" onclick="trackManualVal(\'' + curr_date + '\',\'' + post_time + '\',\'' + post_id + '\',\'' + trip + '\',\'' + post_combined_route + '\',\'' + post_veh_id + '\',\'' + post_drv_id + '\',\'' + route_status + '\')">';
							html_status += "<i class='bi bi-geo-alt'></i>";
							html_status += "</button>";

						}

						tbActRoute.row.add([route_name, post_time, veh_license,
							html_status]).draw();

					});

				}//else

			}, error: function (error) {
				console.error(error);
			}
		})
	}
};

function trackManualVal(curr_date, post_time, post_id, trip, post_combined_route, post_veh_id, post_drv_id, route_status) {
	console.log(curr_date + ' ' + post_time + ' ' + post_id + ' ' + trip + ' ' + post_combined_route + ' ' + post_veh_id + ' ' + post_drv_id + route_status);
	//set session
	sessionStorage.setItem('tracking_date', curr_date);
	sessionStorage.setItem('tracking_time', post_time);
	sessionStorage.setItem('tracking_post_id', post_id);
	sessionStorage.setItem('tracking_route', post_combined_route);
	sessionStorage.setItem('tracking_trip', trip);
	sessionStorage.setItem('tracking_veh_id', post_veh_id);
	sessionStorage.setItem('tracking_drv_id', post_drv_id);
	window.location.href = ('Tracking_info');
} 

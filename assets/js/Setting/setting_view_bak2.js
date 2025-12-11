$(document).ready(function () {
	getData();
	chk_feature_item_change_password();
	chk_feature_item_allow_passenger_change_route();
});

function getData() {
	$.ajax({
		type: "POST",
		url: "Setting/get_data",
		success: function (response) {
			json_data = JSON.parse(response);
			set_layout_data_emp(json_data);
		},
	});
}

function chk_feature_item_change_password() { 
    $.ajax({
        type: "POST",
        url: "Setting/chk_feature_item_change_password",
        data: {},
        success: function (response) {
            console.log(response);

            try {
                var obj = JSON.parse(response);
                
                if (!Array.isArray(obj) || obj.length === 0) {
                    $('#emp_edpw').css("display", "none");
                    return;
                }

                var isActive = obj.some(item => item.feature_set_item_change_password === "active");

                // ใช้ display: table-row สำหรับ <tr>
                $('#emp_edpw').css("display", isActive ? "table-row" : "none");

            } catch (e) {
                console.error("Error parsing JSON", e);
                $('#emp_edpw').css("display", "none");
            }
        },
        error: function () {
            console.error("AJAX request failed");
            $('#emp_edpw').css("display", "none");
        }
    });
}

function chk_feature_item_allow_passenger_change_route() { 
    $.ajax({
        type: "POST",
        url: "Setting/chk_feature_item_allow_passenger_change_route",
        data: {},
        success: function (response) {
            console.log("Response " + response);

                var obj = JSON.parse(response);
                
                if (!Array.isArray(obj) || obj.length === 0) {
                    $('#edRoute, #edRouteOut').hide();
                    return;
                }

                var isActive = obj.some(item => item.feature_set_item_allow_passenger_change_route === "active");

                $('#edRoute, #edRouteOut').toggle(isActive);

        },
        error: function () {
            console.error("AJAX request failed");
            $('#edRoute, #edRouteOut').hide();
        }
    });
}


function getCookie(name) {
	var value = "; " + document.cookie;
	var parts = value.split("; " + name + "=");
	if (parts.length == 2) return parts.pop().split(";").shift();
}

function set_layout_data_emp(json_data) {
	var ck_lang = getCookie("ck_emp_lang");
	//foreach data loop
	json_data.data.forEach(function (row, index) {
		//personal data
		company_name = row.company_name;
		plant_name_th = row.plant_name_th;
		plant_name_en = row.plant_name_en;
		emp_id = row.emp_id;
		emp_code = row.emp_code;
		emp_name = row.emp_name;
		emp_tel = row.emp_tel;
		emp_route_id = row.emp_route_id;
		emp_pick_id = row.emp_pick_id;
		emp_route_out_id = row.emp_route_out_id;
		emp_drop_id = row.emp_drop_id;

		arr_dept = row.arr_dept;
		arr_route_in = row.arr_route_in;
		arr_pickup = row.arr_pickup;
		arr_route_out = row.arr_route_out;
		arr_drop = row.arr_drop;
		arr_modal_route = row.arr_modal_route;
		arr_modal_pick = row.arr_modal_pick;
		arr_modal_route_out = row.arr_modal_route_out;
		arr_modal_drop = row.arr_modal_drop;

	});

	if(ck_lang == "th") { 
		var plant_name = plant_name_th;
	} else { 
		var plant_name = plant_name_en;
	}
	
	console.log("emp_route_id " + emp_route_id);
	console.log("emp_pick_id " + emp_pick_id);
	console.log("emp_route_out_id " + emp_route_out_id);
	console.log("emp_drop_id " + emp_drop_id);

	//set data in layout
	document.getElementById("lbl_com_name").innerHTML =
		'<span style="font-size:11px;">' + company_name + " (" + plant_name + ")" + "</span>";

    console.log(arr_dept[0]);

	if (arr_dept[0] != undefined) {
		document.getElementById("lbl_dept_name").innerHTML =
			'<span style="font-size:11px;">' + arr_dept[0].dept_name + "</span>";
	}

	document.getElementById("lbl_emp_code").innerHTML =
		'<span style="font-size:11px;">' + emp_code + "</span>";

	document.getElementById("lbl_emp_name").innerHTML =
		'<span style="font-size:11px;">' + emp_name + "</span>";

	if (emp_tel == null || emp_tel == "") {
		document.getElementById("lbl_emp_tel").innerHTML =
			'<span style="font-size:11px;">XXXX</span>';
	} else {
		document.getElementById("lbl_emp_tel").innerHTML =
			'<span style="font-size:11px;">' + emp_tel + "</span>";
	}


	///////////////// Comment Code
	if (ck_lang == "th") {
		document.getElementById("lbl_route_name").innerHTML =
			'<span style="font-size:11px;">รอบรับเข้า ' +
			"(" +
			arr_route_in[0].route_code +
			") " +
			arr_route_in[0].route_name +
			"</span>";

		document.getElementById("lbl_pickup_name").innerHTML =
			'<span style="font-size:11px;">จุดรับ ' +
			"(" +
			arr_pickup[0].pickup_code +
			") " +
			arr_pickup[0].pickup_name +
			"</span>";

		document.getElementById("lbl_route_out_name").innerHTML =
			'<span style="font-size:11px;">รอบรับออก ' +
			"(" +
			arr_route_out[0].route_code +
			") " +
			arr_route_out[0].route_name +
			"</span>";

		document.getElementById("lbl_dropoff_name").innerHTML =
			'<span style="font-size:11px;">จุดส่ง ' +
			"(" +
			arr_drop[0].dropoff_code +
			") " +
			arr_drop[0].dropoff_name +
			"</span>";

		document.getElementById("lbl_reset").innerHTML =
			'<span style="font-size:11px;">เปลี่ยนรหัสผ่าน</span>';
	} else {
		document.getElementById("lbl_route_name").innerHTML =
			'<span style="font-size:11px;">Trip in ' +
			"(" +
			arr_route_in[0].route_code +
			") " +
			arr_route_in[0].route_name +
			"</span>";

		document.getElementById("lbl_pickup_name").innerHTML =
			'<span style="font-size:11px;">Point ' +
			"(" +
			arr_pickup[0].pickup_code +
			") " +
			arr_pickup[0].pickup_name +
			"</span>";

		document.getElementById("lbl_route_out_name").innerHTML =
			'<span style="font-size:11px;">Trip out ' +
			"(" +
			arr_route_out[0].route_code +
			") " +
			arr_route_out[0].route_name +
			"</span>";

		document.getElementById("lbl_dropoff_name").innerHTML =
			'<span style="font-size:11px;">Point ' +
			"(" +
			arr_drop[0].dropoff_code +
			") " +
			arr_drop[0].dropoff_name +
			"</span>";

		document.getElementById("lbl_reset").innerHTML =
			'<span style="font-size:11px;">Change Password</span>';
	}


	//set data into the modal
	set_data_modal_route_in(arr_modal_route, emp_route_id);
	set_data_modal_pickup(arr_modal_pick, emp_pick_id);
	set_data_modal_route_out(arr_modal_route_out, emp_route_out_id);
	set_data_modal_dropoff(arr_modal_drop, emp_drop_id);

	document.getElementById("modal_route_emp_id").value = emp_id;
	document.getElementById("modal_route_out_emp_id").value = emp_id;
}

function set_data_modal_route_in(arr_modal_route, emp_route_id) {
	var modal_route_id = document.getElementById("modal_route_id");
	var option = document.createElement("option");
	option.value = "";
	option.text = "-- เลือก --";
	modal_route_id.appendChild(option);

	arr_modal_route.forEach(function (row, index) {
		route_id = row.route_id;
		route_code = row.route_code;
		route_name = row.route_name;

		var option = document.createElement("option");
		option.value = route_id;
		option.text = route_code + " " + route_name;

		if (emp_route_id == route_id) {
			option.setAttribute("selected", "selected");
		}
		modal_route_id.appendChild(option);
	});
}

function set_data_modal_pickup(arr_modal_pick, emp_pick_id) {
	var modal_pick_id = document.getElementById("modal_pick_id");
	var option = document.createElement("option");
	option.value = "";
	option.text = "-- เลือก --";
	modal_pick_id.appendChild(option);

	arr_modal_pick.forEach(function (row, index) {
		pickup_id = row.pickup_id;
		pickup_code = row.pickup_code;
		pickup_name = row.pickup_name;

		var option = document.createElement("option");
		option.value = pickup_id;
		option.text = pickup_code + " " + pickup_name;

		if (emp_pick_id == pickup_id) {
			option.setAttribute("selected", "selected");
		}
		modal_pick_id.appendChild(option);
	});
}

function set_data_modal_route_out(arr_modal_route_out, emp_route_out_id) {
	var modal_route_out_id = document.getElementById("modal_route_out_id");
	var option = document.createElement("option");
	option.value = "";
	option.text = "-- เลือก --";
	modal_route_out_id.appendChild(option);

	arr_modal_route_out.forEach(function (row, index) {
		route_out_id = row.route_id;
		route_out_code = row.route_code;
		route_out_name = row.route_name;

		var option = document.createElement("option");
		option.value = route_out_id;
		option.text = route_out_code + " " + route_out_name;

		if (emp_route_out_id == route_out_id) {
			option.setAttribute("selected", "selected");
		}
		modal_route_out_id.appendChild(option);
	});
}

function set_data_modal_dropoff(arr_modal_drop, emp_drop_id) {
	var modal_drop_id = document.getElementById("modal_drop_id");
	var option = document.createElement("option");
	option.value = "";
	option.text = "-- เลือก --";
	modal_drop_id.appendChild(option);

	arr_modal_drop.forEach(function (row, index) {
		dropoff_id = row.dropoff_id;
		dropoff_code = row.dropoff_code;
		dropoff_name = row.dropoff_name;

		var option = document.createElement("option");
		option.value = dropoff_id;
		option.text = dropoff_code + " " + dropoff_name;

		if (dropoff_id == emp_drop_id) {
			option.setAttribute("selected", "selected");
		}
		modal_drop_id.appendChild(option);
	});
}

function edTel() {
	$.ajax({
		type: "POST",
		url: "Setting/ed_tel",
		success: function (response) {
			json_data = JSON.parse(response);
			document.getElementById("modal_info_tel_id").value =
				json_data.data[0].emp_id;
			document.getElementById("modal_info_tel_tel").value =
				json_data.data[0].emp_tel;
			$("#modal_info_tel").modal("show");
		},
	});
}

function udTel() {
	var emp_id = document.getElementById("modal_info_tel_id").value;
	var emp_tel = document.getElementById("modal_info_tel_tel").value;

	if (emp_id == "") {
		Swal.fire({
			icon: "error",
			title: "Error",
			text: "Failed to check ID",
		});
	} else if (emp_tel == "") {
		Swal.fire({
			icon: "warning",
			title: "Warning",
			text: "Please specify your phone number",
		});
	} else {
		$.ajax({
			type: "POST",
			url: "Setting/update_tel",
			data: { emp_id: emp_id, emp_tel: emp_tel },
			success: function (response) {
				console.log(response);
				$("#modal_info_tel").modal("hide");
				if (response == "1" || response == 1) {
					Swal.fire({
						icon: "success",
						title: "Successfully",
						text: "อัพเดทเบอร์ติดต่อเรียบร้อย!",
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "OK",
					}).then(() => {
						window.location.reload();
					});
				} else {
					Swal.fire({
						icon: "error",
						title: "Error",
						text: "Failed to change your phone number",
					});
				}
			},
		});
	}
}

function edPw() {
	$.ajax({
		type: "POST",
		url: "Setting/ed_pw",
		success: function (response) {
			json_data = JSON.parse(response);

			document.getElementById("modal_info_pw_id").value =
				json_data.data[0].emp_id;
			document.getElementById("modal_info_pw_pname").value = "";
			document.getElementById("modal_info_pw_cname").value = "";

			$("#modal_info_pw").modal("show");
		},
	});
}

function udPw() {
	var emp_id = document.getElementById("modal_info_pw_id").value;
	var emp_pname = document.getElementById("modal_info_pw_pname").value;
	var emp_cname = document.getElementById("modal_info_pw_cname").value;
	if (emp_id == "") {
		Swal.fire({
			icon: "error",
			title: "Error",
			text: "Failed to check ID",
		});
	} else if (emp_pname == "") {
		Swal.fire({
			icon: "warning",
			title: "Warning",
			text: "Please specify your password",
		});
	} else if (emp_cname == "") {
		Swal.fire({
			icon: "warning",
			title: "Warning",
			text: "Please confirm your password",
		});
	} else if (emp_pname != "" && emp_cname != "" && emp_pname != emp_cname) {
		Swal.fire({
			icon: "warning",
			title: "Warning",
			text: "Please check your password",
		});
	} else {
		$.ajax({
			type: "POST",
			url: "Setting/ud_pw",
			data: { emp_id: emp_id, emp_pname: emp_pname },
			success: function (response) {
				$("#modal_info_pw").modal("hide");
				if (response == "1" || response == 1) {
					Swal.fire({
						icon: "success",
						title: "Successfully",
						text: "Change password complete!",
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "OK",
					}).then(() => {
						window.location.reload();
					});
				} else if (response == "no update") {
					Swal.fire({
						icon: "success",
						title: "Successfully",
						text: "ข้อมูลสายรถไม่มีการเปลี่ยนแปลง!",
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "OK",
					}).then(() => {
						window.location.reload();
					});
				} else {
					Swal.fire({
						icon: "error",
						title: "Error",
						text: "Failed to change your password",
					});
				}
			},
		});
	}
}

function getCloseTime() {
	const time = new Date().toLocaleTimeString(); // 11:18:48 AM
	$.ajax({
		type: "POST",
		url: "setting_chk.php",
		dataType: "json",
		data: { chk_type: "getCloseTime" },
		success: function (data) {
			if (data.status === "success") {
				if (data.result.calendar_type === "Holiday") {
					Swal.fire({
						icon: "warning",
						title: "Warning",
						text: "ไม่สามารถเปลี่ยนสายรถในวันหยุดของบริษัทได้",
					});
					return 0;
				} else if (
					time >= data.result.close_time_start &&
					time <= data.result.close_time_end
				) {
					Swal.fire({
						icon: "warning",
						title: "Warning",
						text: "ไม่สามารถเปลี่ยนสายรถ หรือจุดรับส่ง หลังระบบปิดได้",
					});
					return 0;
				}
			} else {
				Swal.fire({
					icon: "warning",
					title: "Warning",
					text: "Fail! Please try again later.",
				});
				return 0;
			}
		},
	});
}

function edRoute() {
	const now = new Date();
	const hours = now.getHours().toString().padStart(2, '0');
	const minutes = now.getMinutes().toString().padStart(2, '0');
	const seconds = now.getSeconds().toString().padStart(2, '0');
	
	const time = `${hours}:${minutes}:${seconds}`;
	console.log(time);

	$.ajax({
		type: "POST",
		url: "Setting/get_close_time",
		dataType: "json",
		success: function (data) {
			console.log(data);
			if (data.status === "success") {
				if (data.result.calendar_type === "Holiday") {
					Swal.fire({
						icon: "warning",
						title: "Warning",
						text: "ไม่สามารถเปลี่ยนสายรถในวันหยุดของบริษัทได้",
					});
					return false;
				} else if (
					time >= data.result.close_time_start &&
					time <= data.result.close_time_end
				) {
					Swal.fire({
						icon: "warning",
						title: "Warning",
						text: "ไม่สามารถเปลี่ยนสายรถ หรือจุดรับส่ง หลังระบบปิดได้",
					});
					return false;
				} else {
					$("#modal_route").modal("show");
				}
			} else {
				Swal.fire({
					icon: "warning",
					title: "Warning",
					text: "Fail! Please try again later.",
				});
				return false;
			}
		},
	});
}

function edRouteOut() {
	const time = new Date().toLocaleTimeString(); // 11:18:48 AM

	$.ajax({
		type: "POST",
		url: "Setting/get_close_time",
		dataType: "json",
		success: function (data) {
			if (data.status === "success") {
				if (data.result.calendar_type === "Holiday") {
					Swal.fire({
						icon: "warning",
						title: "Warning",
						text: "ไม่สามารถเปลี่ยนสายรถในวันหยุดของบริษัทได้",
					});
					return false;
				} else if (
					time >= data.result.close_time_start &&
					time <= data.result.close_time_end
				) {
					Swal.fire({
						icon: "warning",
						title: "Warning",
						text: "ไม่สามารถเปลี่ยนสายรถ หรือจุดรับส่ง หลังระบบปิดได้",
					});
					return false;
				} else {
					$("#modal_route_out").modal("show");
				}
			} else {
				Swal.fire({
					icon: "warning",
					title: "Warning",
					text: "Fail! Please try again later.",
				});
				return false;
			}
		},
	});
}

function edName() {
	$.ajax({
		type: "POST",
		url: "Setting/ed_name",
		success: function (response) {
			json_data = JSON.parse(response);
			json_data.data.forEach(function (row, index) {
				emp_id = row.emp_id;
				emp_name = row.emp_name;
				emp_lname = row.emp_lname;
			});
			document.getElementById("modal_ed_emp_id").value = emp_id;
			document.getElementById("modal_ed_emp_name").value = emp_name;
			document.getElementById("modal_ed_emp_lname").value = emp_lname;
			$("#modal_ed_name").modal("show");
		},
	});
}

function udName() {
	var modal_ed_emp_name = document.getElementById("modal_ed_emp_name").value;
	var modal_ed_emp_lname = document.getElementById("modal_ed_emp_lname").value;
	console.log(modal_ed_emp_name + " " + modal_ed_emp_lname);
	if (modal_ed_emp_name === "") {
		Swal.fire({
			icon: "warning",
			title: "Warning",
			text: "Please specify your name",
		});
	} else if (modal_ed_emp_lname === "") {
		Swal.fire({
			icon: "warning",
			title: "Warning",
			text: "Please specify your last name",
		});
	} else {
		$.ajax({
			type: "POST",
			url: "Setting/ud_name",
			data: {
				modal_ed_emp_name: modal_ed_emp_name,
				modal_ed_emp_lname: modal_ed_emp_lname,
			},
			success: function (response) {
				console.log(response);
				$("#modal_ed_name").modal("hide");
				if (response == "1" || response == 1) {
					Swal.fire({
						icon: "success",
						title: "Successfully",
						text: "อัพเดท ชื่อ-นามสกุล เรียบร้อย!",
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "OK",
					}).then(() => {
						window.location.reload();
					});
				} else {
					Swal.fire({
						icon: "error",
						title: "Error",
						text: "Failed to update name",
					});
				}
			},
			error: function (error) {
				console.error(error);
			},
		});
	}
}

function modalRouteChkRoute(route_id) {
	$.ajax({
		type: "POST",
		url: "Setting/modal_route_chk_route",
		data: { route_id: route_id },
		success: function (response) {
			json_data = JSON.parse(response);
			$("#modal_pick_id").empty();
			var modal_pick_id = document.getElementById("modal_pick_id");
			var option = document.createElement("option");
			option.value = "";
			option.text = "-- เลือก --";
			modal_pick_id.appendChild(option);
			json_data.data.forEach(function (row, index) {
				pickup_id = row.pickup_id;
				pickup_code = row.pickup_code;
				pickup_name = row.pickup_name;
				var option = document.createElement("option");
				option.value = pickup_id;
				option.text = pickup_code + " " + pickup_name;
				modal_pick_id.appendChild(option);
			});
		},
	});
}

function modalRouteOutChkRoute(route_out_id) {

	$.ajax({
		type: "POST",
		url: "Setting/modal_route_out_chk",
		data: { route_out_id: route_out_id },
		success: function (response) {
			json_data = JSON.parse(response);
			console.log("Trip Out XXXXXXXXXXXXXXXX: " + response);

			$("#modal_drop_id").empty();
			var modal_drop_id = document.getElementById("modal_drop_id");
			var option = document.createElement("option");
			option.value = "";
			option.text = "-- เลือก --";
			modal_drop_id.appendChild(option);

			json_data.data.forEach(function (row, index) {
				dropoff_id = row.dropoff_id;
				dropoff_code = row.dropoff_code;
				dropoff_name = row.dropoff_name;

				var option = document.createElement("option");
				option.value = dropoff_id;
				option.text = dropoff_code + " " + dropoff_name;

				modal_drop_id.appendChild(option);
			});
		},
	});
}

function udRoute() {
	document.getElementById("udRoute").disabled = true;
	var modal_route_emp_id = document.getElementById("modal_route_emp_id").value;
	var modal_route_id = document.getElementById("modal_route_id").value;
	var modal_pick_id = document.getElementById("modal_pick_id").value;

	if (modal_route_id === "") {
		Swal.fire({
			icon: "warning",
			title: "Warning",
			text: "Please select your route",
		});
	} else if (modal_pick_id === "") {
		Swal.fire({
			icon: "warning",
			title: "Warning",
			text: "Please select your pickup point",
		});
	} else {
		$.ajax({
			type: "POST",
			url: "Setting/update_route",
			data: {
				modal_route_emp_id: modal_route_emp_id,
				modal_route_id: modal_route_id,
				modal_pick_id: modal_pick_id,
			},
			success: function (response) {
				console.log(response);
				$("#modal_route").modal("hide");
				if (response == "1" || response == 1) {
					Swal.fire({
						icon: "success",
						title: "Successfully",
						text: "อัพเดทข้อมูลสายรถเรียบร้อย!",
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "OK",
					}).then(() => {
						window.location.reload();
					});
				} else if (response == "no update") {
					Swal.fire({
						icon: "success",
						title: "Successfully",
						text: "ข้อมูลสายรถไม่มีการเปลี่ยนแปลง!",
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "OK",
					}).then(() => {
						window.location.reload();
					});
				} else {
					Swal.fire({
						icon: "error",
						title: "Error",
						text: "Failed to update route detail",
					});
				}
			},
		});
	}
}

function udRouteOut() {
	document.getElementById("udRouteOut").disabled = true;
	var modal_route_out_emp_id = document.getElementById(
		"modal_route_out_emp_id"
	).value;
	var modal_route_out_id = document.getElementById("modal_route_out_id").value;
	var modal_drop_id = document.getElementById("modal_drop_id").value;

	if (modal_route_out_id === "") {
		Swal.fire({
			icon: "warning",
			title: "Warning",
			text: "Please select your route",
		});
	} else if (modal_drop_id === "") {
		Swal.fire({
			icon: "warning",
			title: "Warning",
			text: "Please select your dropoff point",
		});
	} else {
		$.ajax({
			type: "POST",
			url: "Setting/update_route_out",
			data: {
				modal_route_out_emp_id: modal_route_out_emp_id,
				modal_route_out_id: modal_route_out_id,
				modal_drop_id: modal_drop_id,
			},
			success: function (response) {
				$("#modal_route_out").modal("hide");
				if (response == 1 || response == "1") {
					Swal.fire({
						icon: "success",
						title: "Successfully",
						text: "อัพเดทข้อมูลสายรถเรียบร้อย!",
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "OK",
					}).then(() => {
						window.location.reload();
					});
				} else if (response == "no update") {
					Swal.fire({
						icon: "success",
						title: "Successfully",
						text: "ข้อมูลสายรถไม่มีการเปลี่ยนแปลง!",
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "OK",
					}).then(() => {
						window.location.reload();
					});
				} else {
					Swal.fire({
						icon: "error",
						title: "Error",
						text: "Failed to update route detail",
					});
				}
			},
			error: function (error) {
				console.error(error);
			},
		});
	}
}

function backVal() {
	window.location.href = "Home";
}

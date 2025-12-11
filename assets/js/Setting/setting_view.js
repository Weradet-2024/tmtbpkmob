$(document).ready(function () {
	getData();
	chk_feature_item_change_password();
	chk_feature_item_allow_passenger_change_route();
	
	$('#modal_route .select2').select2({
		width: '100%',
		dropdownParent: $('#modal_route')
	});
	
	$('#modal_route_out .select2').select2({
		width: '100%',
		dropdownParent: $('#modal_route_out')
	});
});

function getData() {
	$.ajax({
		type: "POST",
		url: "Setting/get_data",
		success: function (response) {
			json_data = JSON.parse(response);
			console.log(json_data);
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
					$("#emp_edpw").css("display", "none");
					return;
				}

				var isActive = obj.some(
					(item) => item.feature_set_item_change_password === "active"
				);

				// ใช้ display: table-row สำหรับ <tr>
				$("#emp_edpw").css("display", isActive ? "table-row" : "none");
			} catch (e) {
				console.error("Error parsing JSON", e);
				$("#emp_edpw").css("display", "none");
			}
		},
		error: function () {
			console.error("AJAX request failed");
			$("#emp_edpw").css("display", "none");
		},
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
				$("#edRoute, #edRouteOut").hide();
				return;
			}

			var isActive = obj.some(
				(item) =>
					item.feature_set_item_allow_passenger_change_route === "active"
			);

			$("#edRoute, #edRouteOut").toggle(isActive);
		},
		error: function () {
			console.error("AJAX request failed");
			$("#edRoute, #edRouteOut").hide();
		},
	});
}

function getCookie(name) {
	var value = "; " + document.cookie;
	var parts = value.split("; " + name + "=");
	if (parts.length == 2) return parts.pop().split(";").shift();
}

function set_layout_data_emp(json_data) {
	// ดักกรณีที่ json_data หรือ json_data.data ไม่มีข้อมูล
	if (!json_data || !json_data.data || json_data.data.length === 0) {
		console.error("ไม่มีข้อมูลพนักงาน");
		return;
	}

	var ck_lang = getCookie("ck_emp_lang");

	// console.log("test");
	console.log(json_data);

	// สมมติว่ามีข้อมูลแค่ record เดียว (หรือใช้ forEach สำหรับหลาย record)
	json_data.data.forEach(function (row) {
		// ดักจับค่า null/undefined ด้วยการใช้ || ค่า default
		var company_name = row.company_name || "";
		var plant_name_th = row.plant_name_th || "";
		var plant_name_en = row.plant_name_en || "";
		var emp_id = row.emp_id || "";
		var emp_code = row.emp_code || "";
		var emp_name = row.emp_name || "";
		//var emp_tel = row.emp_tel || "";
		var emp_route_zone_id = row.emp_route_zone_id || "";
		var emp_route_id = row.emp_route_id || "";
		var emp_pick_id = row.emp_pick_id || "";
		var emp_route_zone_out_id = row.emp_route_zone_out_id || "";
		var emp_route_out_id = row.emp_route_out_id || "";
		var emp_drop_id = row.emp_drop_id || "";

		// ดักจับ array ให้แน่ใจว่ามีค่าเป็น array ไม่ใช่ null
		var arr_dept = row.arr_dept || [];
		var arr_route_in = row.arr_route_in || [];
		var arr_pickup = row.arr_pickup || [];
		var arr_route_out = row.arr_route_out || [];
		var arr_drop = row.arr_drop || [];
		var arr_modal_route = row.arr_modal_route || [];
	var arr_modal_pick = row.arr_modal_pick || [];
		var arr_modal_route_out = row.arr_modal_route_out || [];
		var arr_modal_drop = row.arr_modal_drop || [];

		// เลือก plant name ตามภาษา
		var plant_name = ck_lang == "th" ? plant_name_th : plant_name_en;

		// แสดงข้อมูลบริษัทและ plant name
		document.getElementById("lbl_com_name").innerHTML =
			'<span style="font-size:11px;">' +
			company_name +
			" (" +
			plant_name +
			")</span>";

		// ดักจับ arr_dept ให้มีข้อมูลก่อนใช้งาน
		/* if (arr_dept.length > 0 && arr_dept[0].dept_name) {
			document.getElementById("lbl_dept_name").innerHTML =
				'<span style="font-size:11px;">' + arr_dept[0].dept_name + "</span>";
		} else {
			document.getElementById("lbl_dept_name").innerHTML =
				'<span style="font-size:11px;">N/A</span>';
		} */

		document.getElementById("lbl_emp_code").innerHTML =
			'<span style="font-size:11px;">' + emp_code + "</span>";

		document.getElementById("lbl_emp_name").innerHTML =
			'<span style="font-size:11px;">' + emp_name + "</span>";

		// กำหนดข้อความแสดงผลตามภาษาและตรวจสอบ array แต่ละตัวก่อนเข้าถึง index 0
		if (ck_lang == "th") {
			if (arr_route_in.length > 0) {
				document.getElementById("lbl_route_name").innerHTML =
					'<span style="font-size:11px;">รอบรับเข้า (' +
					(arr_route_in[0].route_code || "") +
					") " +
					(arr_route_in[0].route_name || "N/A") +
					"</span>";
			} else {
				document.getElementById("lbl_route_name").innerHTML =
					'<span style="font-size:11px;">รอบรับเข้า N/A</span>';
			}

			if (arr_pickup.length > 0) {
				document.getElementById("lbl_pickup_name").innerHTML =
					'<span style="font-size:11px;">จุดรับ (' +
					(arr_pickup[0].pickup_code || "N/A") +
					") " +
					(arr_pickup[0].pickup_name || "N/A") +
					"</span>";
			} else {
				document.getElementById("lbl_pickup_name").innerHTML =
					'<span style="font-size:11px;">จุดรับ N/A</span>';
			}

			if (arr_route_out.length > 0) {
				document.getElementById("lbl_route_out_name").innerHTML =
					'<span style="font-size:11px;">รอบรับออก (' +
					(arr_route_out[0].route_code || "") +
					") " +
					(arr_route_out[0].route_name || "N/A") +
					"</span>";
			} else {
				document.getElementById("lbl_route_out_name").innerHTML =
					'<span style="font-size:11px;">รอบรับออก N/A</span>';
			}

			if (arr_drop.length > 0) {
				document.getElementById("lbl_dropoff_name").innerHTML =
					'<span style="font-size:11px;">จุดส่ง (' +
					(arr_drop[0].dropoff_code || "") +
					") " +
					(arr_drop[0].dropoff_name || "N/A") +
					"</span>";
			} else {
				document.getElementById("lbl_dropoff_name").innerHTML =
					'<span style="font-size:11px;">จุดส่ง N/A</span>';
			}

			document.getElementById("lbl_reset").innerHTML =
				'<span style="font-size:11px;">เปลี่ยนรหัสผ่าน</span>';
		} else {
			if (arr_route_in.length > 0) {
				document.getElementById("lbl_route_name").innerHTML =
					'<span style="font-size:11px;">Trip in (' +
					(arr_route_in[0].route_code || "") +
					") " +
					(arr_route_in[0].route_name || "N/A") +
					"</span>";
			} else {
				document.getElementById("lbl_route_name").innerHTML =
					'<span style="font-size:11px;">Trip in N/A</span>';
			}

			if (arr_pickup.length > 0) {
				document.getElementById("lbl_pickup_name").innerHTML =
					'<span style="font-size:11px;">Point (' +
					(arr_pickup[0].pickup_code || "") +
					") " +
					(arr_pickup[0].pickup_name || "N/A") +
					"</span>";
			} else {
				document.getElementById("lbl_pickup_name").innerHTML =
					'<span style="font-size:11px;">Point N/A</span>';
			}

			if (arr_route_out.length > 0) {
				document.getElementById("lbl_route_out_name").innerHTML =
					'<span style="font-size:11px;">Trip out (' +
					(arr_route_out[0].route_code || "") +
					") " +
					(arr_route_out[0].route_name || "N/A") +
					"</span>";
			} else {
				document.getElementById("lbl_route_out_name").innerHTML =
					'<span style="font-size:11px;">Trip out N/A</span>';
			}

			if (arr_drop.length > 0) {
				document.getElementById("lbl_dropoff_name").innerHTML =
					'<span style="font-size:11px;">Point (' +
					(arr_drop[0].dropoff_code || "") +
					") " +
					(arr_drop[0].dropoff_name || "N/A") +
					"</span>";
			} else {
				document.getElementById("lbl_dropoff_name").innerHTML =
					'<span style="font-size:11px;">Point N/A</span>';
			}

			// document.getElementById("lbl_reset").innerHTML =
			// 	'<span style="font-size:11px;">Change Password</span>';
		}

		// กำหนดข้อมูลเข้า modal
		// ในที่นี้ set_data_modal_route_in() ส่วนอื่นๆ ควรมีการดักจับ null ภายในฟังก์ชันด้วย
		set_data_modal_route_in(emp_route_id);
		set_data_modal_pickup(arr_modal_pick, emp_pick_id);
		set_data_modal_route_out(emp_route_out_id);
		set_data_modal_dropoff(arr_modal_drop, emp_drop_id);

		// กำหนดค่าใน input ของ modal
		document.getElementById("modal_route_emp_id").value = emp_id;
		document.getElementById("modal_route_out_emp_id").value = emp_id;
		document.getElementById("modal_info_pw_id").value = emp_id;
	});
}

function set_data_modal_route_in(emp_route_id) {
	$.ajax({
		type: "POST",
		url: "Setting/get_route_in_data",
		success: function (response) {
			var json_data = JSON.parse(response);
			console.log(json_data);
			var modal_route_id = document.getElementById("modal_route_id");
			// เคลียร์ค่าเก่าใน select
			modal_route_id.innerHTML = "";

			// สร้าง option เริ่มต้น
			var defaultOption = document.createElement("option");
			defaultOption.value = "";
			defaultOption.text = "-- เลือก --";
			modal_route_id.appendChild(defaultOption);

			// วนลูปข้อมูลที่ได้จาก AJAX เพื่อสร้าง option ใหม่
			json_data.data.forEach(function (row) {
				var route_id = row.route_id;
				var route_code = row.route_code;
				var route_name = row.route_name;

				var option = document.createElement("option");
				option.value = route_id;
				option.text = route_code + " " + route_name;

				// ตั้งค่า selected หากตรงกับ emp_route_id
				if (emp_route_id == route_id) {
					option.setAttribute("selected", "selected");
				}
				modal_route_id.appendChild(option);
			});
		},
		error: function (xhr, status, error) {
			console.error("เกิดข้อผิดพลาดในการดึงข้อมูล route in: ", error);
		},
	});
}

// คงโค้ดเดิมสำหรับ Pickup (ใช้ global arr_modal_pick)
function set_data_modal_pickup(arr_modal_pick, emp_pick_id) {
	var modal_pick_id = document.getElementById("modal_pick_id");
	// เคลียร์ค่าเก่าใน select
	modal_pick_id.innerHTML = "";

	var defaultOption = document.createElement("option");
	defaultOption.value = "";
	defaultOption.text = "-- เลือก --";
	modal_pick_id.appendChild(defaultOption);

	arr_modal_pick.forEach(function (row, index) {
		var pickup_id = row.pickup_id;
		var pickup_code = row.pickup_code;
		var pickup_name = row.pickup_name;

		var option = document.createElement("option");
		option.value = pickup_id;
		option.text = pickup_code + " " + pickup_name;

		if (emp_pick_id == pickup_id) {
			option.setAttribute("selected", "selected");
		}
		modal_pick_id.appendChild(option);
	});
}

function set_data_modal_route_out(emp_route_out_id) {
	$.ajax({
		type: "POST",
		url: "Setting/get_route_out_data",
		success: function (response) {
			var json_data = JSON.parse(response);
			var modal_route_out_id = document.getElementById("modal_route_out_id");
			// เคลียร์ค่าเก่าใน select
			modal_route_out_id.innerHTML = "";

			// สร้าง option เริ่มต้น
			var defaultOption = document.createElement("option");
			defaultOption.value = "";
			defaultOption.text = "-- เลือก --";
			modal_route_out_id.appendChild(defaultOption);

			// วนลูปข้อมูลเพื่อสร้าง option ใหม่
			json_data.data.forEach(function (row) {
				var route_out_id = row.route_out_id;
				var route_out_code = row.route_out_code;
				var route_out_name = row.route_out_name;

				var option = document.createElement("option");
				option.value = route_out_id;
				option.text = route_out_code + " " + route_out_name;

				console.log(route_out_id);
				console.log(route_out_code + " " + route_out_name);

				if (emp_route_out_id == route_out_id) {
					option.setAttribute("selected", "selected");
				}
				modal_route_out_id.appendChild(option);
			});
		},
		error: function (xhr, status, error) {
			console.error("เกิดข้อผิดพลาดในการดึงข้อมูล route out: ", error);
		},
	});
}



// คงโค้ดเดิมสำหรับ Dropoff (ใช้ arr_modal_drop)
function set_data_modal_dropoff(arr_modal_drop, emp_drop_id) {
	console.log("🚀 ~ set_data_modal_dropoff ~ arr_modal_drop:", arr_modal_drop)
	console.log(arr_modal_drop);
	var modal_drop_id = document.getElementById("modal_drop_id");
	// เคลียร์ค่าเก่าใน select
	modal_drop_id.innerHTML = "";

	var defaultOption = document.createElement("option");
	defaultOption.value = "";
	defaultOption.text = "-- เลือก --";
	modal_drop_id.appendChild(defaultOption);

	arr_modal_drop.forEach(function (row, index) {
		var dropoff_id = row.dropoff_id;
		var dropoff_code = row.dropoff_code;
		var dropoff_name = row.dropoff_name;

		var option = document.createElement("option");
		option.value = dropoff_id;
		option.text = dropoff_code + " " + dropoff_name;

		if (dropoff_id == emp_drop_id) {
			option.setAttribute("selected", "selected");
		}
		modal_drop_id.appendChild(option);
	});
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

// function udPw() {
// 	var emp_id = document.getElementById("modal_info_pw_id").value;
// 	var emp_pname = document.getElementById("modal_info_pw_pname").value;
// 	var emp_cname = document.getElementById("modal_info_pw_cname").value;
// 	if (emp_id == "") {
// 		Swal.fire({
// 			icon: "error",
// 			title: "Error",
// 			text: "Failed to check ID",
// 		});
// 	} else if (emp_pname == "") {
// 		Swal.fire({
// 			icon: "warning",
// 			title: "Warning",
// 			text: "Please specify your password",
// 		});
// 	} else if (emp_cname == "") {
// 		Swal.fire({
// 			icon: "warning",
// 			title: "Warning",
// 			text: "Please confirm your password",
// 		});
// 	} else if (emp_pname != "" && emp_cname != "" && emp_pname != emp_cname) {
// 		Swal.fire({
// 			icon: "warning",
// 			title: "Warning",
// 			text: "Please check your password",
// 		});
// 	} else {
// 		$.ajax({
// 			type: "POST",
// 			url: "Setting/ud_pw",
// 			data: { emp_id: emp_id, emp_pname: emp_pname },
// 			success: function (response) {
// 				console.log(response);
// 				$("#modal_info_pw").modal("hide");
// 				if (response == "1" || response == 1) {
// 					Swal.fire({
// 						icon: "success",
// 						title: "Successfully",
// 						text: "Change password complete!",
// 						confirmButtonColor: "#DD6B55",
// 						confirmButtonText: "OK",
// 					}).then(() => {
// 						window.location.reload();
// 					});
// 				} else if (response == "no update") {
// 					Swal.fire({
// 						icon: "success",
// 						title: "Successfully",
// 						text: "ข้อมูลสายรถไม่มีการเปลี่ยนแปลง!",
// 						confirmButtonColor: "#DD6B55",
// 						confirmButtonText: "OK",
// 					}).then(() => {
// 						window.location.reload();
// 					});
// 				} else {
// 					Swal.fire({
// 						icon: "error",
// 						title: "Error",
// 						text: "Failed to change your password",
// 					});
// 				}
// 			},
// 		});
// 	}
// }
function udPw() {
	var emp_id = document.getElementById("modal_info_pw_id").value;
	var emp_pname = document.getElementById("modal_info_pw_pname").value;
	var emp_cname = document.getElementById("modal_info_pw_cname").value;

	console.log(emp_id);
	console.log(emp_pname);
	console.log(emp_cname);
	
	if (emp_id == "") {
		Swal.fire({
			icon: "error",
			title: "Error",
			text: "Failed to check ID",
			confirmButtonText: "OK",
			customClass: {
				popup: 'swal2-front' // used CCS fix.
			}
		});

	} else if (emp_pname == "") {
		Swal.fire({
			icon: "warning",
			title: "Warning",
			text: "Please specify your password",
			confirmButtonText: "OK",
			customClass: {
				popup: 'swal2-front' // used CCS fix.
			}
		});
	} else if (emp_cname == "") {
		Swal.fire({
			icon: "warning",
			title: "Warning",
			text: "Please confirm your password",
			confirmButtonText: "OK",
			customClass: {
				popup: 'swal2-front' // used CCS fix.
			}
		});
	} else if (emp_pname != "" && emp_cname != "" && emp_pname != emp_cname) {
		Swal.fire({
			icon: "warning",
			title: "Warning",
			text: "Please check your password",
			confirmButtonText: "OK",
			customClass: {
				popup: 'swal2-front' // used CCS fix.
			}
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
						text: "Change Password complete!",
						confirmButtonText: "OK",
						customClass: {
							popup: 'swal2-front' // used CCS fix.
						}
					}).then(() => {
						window.location.reload();
					});
				} else if (response == "no update") {
					Swal.fire({
						icon: "success",
						title: "Successfully",
						text: "ข้อมูลพาสเวิดไม่มีการเปลี่ยนแปลง!",
						confirmButtonText: "OK",
						customClass: {
							popup: 'swal2-front' // used CCS fix.
						}
					}).then(() => {
						window.location.reload();
					});
				} else {
					Swal.fire({
						icon: "error",
						title: "Error",
						text: "Failed to change your password",
						confirmButtonText: "OK",
						customClass: {
							popup: 'swal2-front' // used CCS fix.
						}
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
	const hours = now.getHours().toString().padStart(2, "0");
	const minutes = now.getMinutes().toString().padStart(2, "0");
	const seconds = now.getSeconds().toString().padStart(2, "0");

	const time = `${hours}:${minutes}:${seconds}`;
	console.log(time);
	$("#modal_route").modal("show");
	// $.ajax({
	// 	type: "POST",
	// 	url: "Setting/get_close_time",
	// 	dataType: "json",
	// 	success: function (data) {
	// 		console.log(data);
	// 		if (data.status === "success") {
	// 			if (data.result.calendar_type === "Holiday") {
	// 				Swal.fire({
	// 					icon: "warning",
	// 					title: "Warning",
	// 					text: "ไม่สามารถเปลี่ยนสายรถในวันหยุดของบริษัทได้",
	// 				});
	// 				return false;
	// 			} else if (
	// 				time >= data.result.close_time_start &&
	// 				time <= data.result.close_time_end
	// 			) {
	// 				Swal.fire({
	// 					icon: "warning",
	// 					title: "Warning",
	// 					text: "ไม่สามารถเปลี่ยนสายรถ หรือจุดรับส่ง หลังระบบปิดได้",
	// 				});
	// 				return false;
	// 			} else {
	// 				$("#modal_route").modal("show");
	// 			}
	// 		} else {
	// 			Swal.fire({
	// 				icon: "warning",
	// 				title: "Warning",
	// 				text: "Fail! Please try again later.",
	// 			});
	// 			return false;
	// 		}
	// 	},
	// });
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
			console.log(json_data);
			json_data.data.forEach(function (row, index) {
				emp_id = row.emp_id;
				emp_name = row.emp_name;
				emp_lname = row.emp_lname;
				document.getElementById("modal_ed_emp_id").value = emp_id;
				document.getElementById("modal_ed_emp_name").value = emp_name;
				document.getElementById("modal_ed_emp_lname").value = emp_lname;
				$("#modal_ed_name").modal("show");

			});
			
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

function showLoader() {
    var overlay = document.getElementById('loading-overlay');
    if (overlay) overlay.style.display = 'flex';
  }

function backVal() {
	showLoader();
	window.location.href = "Home";
}

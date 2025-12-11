$(document).ready(async function () {
	var tracking_date = sessionStorage.getItem("tracking_date");
	var tracking_time = sessionStorage.getItem("tracking_time");
	var tracking_trip = sessionStorage.getItem("tracking_trip");
	var tracking_route = sessionStorage.getItem("tracking_route");
	var tracking_veh_id = sessionStorage.getItem("tracking_veh_id");
	var tracking_drv_id = sessionStorage.getItem("tracking_drv_id");

	await get_data(
		tracking_date,
		tracking_time,
		tracking_trip,
		tracking_route,
		tracking_veh_id,
		tracking_drv_id
	);

	sessionStorage.removeItem("reserve");

	//map.Event.bind(longdo.EventName.Ready, function () {
		// เพิ่ม marker ที่ตำแหน่งเริ่มต้น

		get_company();

		getLocation(
			tracking_date,
			tracking_time,
			tracking_trip,
			tracking_route,
			tracking_veh_id,
			tracking_drv_id
		);

		get_point_reserve(
			tracking_date,
			tracking_time,
			tracking_trip,
			tracking_route,
			tracking_veh_id,
			tracking_drv_id
		);

		veh_marker = new longdo.Marker(
			{ lat: 0.0, lon: 0.0 },
			{
				title: "Vehicle here",
				detail: "",
				icon: {
					url: "../assets/img/img/bus.png",
					offset: { x: 15, y: 15 },
					size: { width: 32, height: 32 },
				},
			}
		);
		map.Overlays.add(veh_marker);
	});

	window.addEventListener("resize", () => {
		//console.log('หน้าจอถูกปรับขนาด!');
		//console.log(`ความกว้าง: ${window.innerWidth}, ความสูง: ${window.innerHeight}`);
		map.resize();
	});
//});

var veh_marker;
var is_reserve = false;

function get_data(
	tracking_date,
	tracking_time,
	tracking_trip,
	tracking_route,
	tracking_veh_id,
	tracking_drv_id
) {
	$.ajax({
		type: "POST",
		url: "Tracking_info/get_data",
		data: {
			tracking_date: tracking_date,
			tracking_time: tracking_time,
			tracking_trip: tracking_trip,
			tracking_route: tracking_route,
			tracking_veh_id: tracking_veh_id,
			tracking_drv_id: tracking_drv_id,
		},
		success: function (response) {
			console.log(response);
			try {
				var json_data = JSON.parse(response);

				// Update the DOM elements with the parsed data
				document.getElementById("lbl_route_name").innerHTML =
					json_data[0].route;
				document.getElementById("lbl_license_plate").innerHTML =
					json_data[0].veh_license;
				document.getElementById("lbl_drv_name").innerHTML = json_data[0].driver;
				document.getElementById("lbl_drv_tel").innerHTML =
					json_data[0].driver_tel;
				document.getElementById("dial").href = "tel:" + json_data[0].driver_tel;
			} catch (error) {
				console.error("Error parsing JSON response:", error);
				// Optional: Provide user feedback if needed
				//alert("Failed to load the data. Please try again.");
			}
		},
		error: function (xhr) {
			console.error(xhr);
		},
	});
}

function getCookie(name) {
	const cookies = document.cookie.split("; "); // Split cookies into an array
	for (let i = 0; i < cookies.length; i++) {
		const cookie = cookies[i].split("="); // Split each cookie into name and value
		if (cookie[0] === name) {
			return decodeURIComponent(cookie[1]); // Return the decoded value if the name matches
		}
	}
	return null; // Return null if the cookie is not found
}

function current_location() {
	var tracking_date = sessionStorage.getItem("tracking_date");
	var tracking_time = sessionStorage.getItem("tracking_time");
	var tracking_trip = sessionStorage.getItem("tracking_trip");
	var tracking_route = sessionStorage.getItem("tracking_route");
	var tracking_veh_id = sessionStorage.getItem("tracking_veh_id");
	var tracking_drv_id = sessionStorage.getItem("tracking_drv_id");
	$.ajax({
		type: "POST",
		url: "Tracking_info/get_bus_location",
		data: {
			tracking_date: tracking_date,
			tracking_time: tracking_time,
			tracking_trip: tracking_trip,
			tracking_route: tracking_route,
			tracking_veh_id: tracking_veh_id,
			tracking_drv_id: tracking_drv_id,
			reserve_point: reserve_point,
		},
		success: function (response) {
			var obj = response;
			obj = JSON.parse(response);
			console.log(obj);
			var new_obj = obj["location"].split(",");
			var lat = new_obj[0];
			var lon = new_obj[1];
			map.location({ lon: lon, lat: lat }, true);
		},
	});
}

function get_company() {
	var departure_route = sessionStorage.getItem("tracking_route");
	$.ajax({
		type: "POST",
		url: "Tracking_info/get_plant",
		data: {
			departure_route: departure_route,
		},
		success: function (response) {
			//console.log(response);
			try {
				var obj = JSON.parse(response);
				sessionStorage.setItem("com_lat", obj[0]["point_lat"]);
				sessionStorage.setItem("com_lon", obj[0]["point_lon"]);
				sessionStorage.setItem("com_name", obj[0]["point_name"]);

				var point_maker = new longdo.Marker(
					{
						lon: parseFloat(obj[0]["point_lon"]),
						lat: parseFloat(obj[0]["point_lat"]),
					},
					{
						title: "Point are here",
						detail: "Point are here",
						icon: {
							url: "../assets/img/img/building.png",
							offset: { x: 15, y: 10 },
							size: { width: 40, height: 50 },
						},
					}
				);
				map.Overlays.add(point_maker);
			} catch (error) {
				sessionStorage.setItem("com_lat", 0.0);
				sessionStorage.setItem("com_lon", 0.0);
				sessionStorage.setItem("com_name", "Error Company Data");
			}
		},
		error: function (error) {
			console.error(error);
		},
	});
}

function get_point_reserve(
	tracking_date,
	tracking_time,
	tracking_trip,
	tracking_route,
	tracking_veh_id,
	tracking_drv_id
) {
	$.ajax({
		type: "POST",
		url: "Tracking_info/get_point_reserve",
		data: {
			tracking_date: tracking_date,
			tracking_trip: tracking_trip,
			tracking_route: tracking_route,
			tracking_time: tracking_time,
			tracking_veh_id: tracking_veh_id,
			tracking_drv_id: tracking_drv_id,
		},
		success: function (response) {
			console.log(response);
			var obj = JSON.parse(response);
			var lang = getCookie("ck_emp_lang");
			var shuttle_start = lang == "th" ? "จุดเริ่มต้น" : "Shuttle Start";
			obj["point_lat"];
			obj["point_lon"];
			if (obj["point_lat"] === "" && obj["point_lon"] === "") {
				//ไมใ่ได้จอง เซ็ต เข้าบริษัทโดบตรง
				com_name = sessionStorage.getItem("com_name"); // ทำ company

				sessionStorage.setItem("reserve", false); // สภานะการจอง
				sessionStorage.setItem("point_lat", "");
				sessionStorage.setItem("point_lon", "");
				document.getElementById("lbl_route_start").innerHTML = shuttle_start;
				// document.getElementById("lbl_route_start").innerHTML = "Shuttle Start";
				document.getElementById("lbl_end").innerHTML = com_name;
			} else {
				// จองแล้ว
				var obj = jQuery.parseJSON(response);
				is_reserve = true;
				lat = obj["point_lat"];
				lon = obj["point_lon"];
				id = obj["point_id"];
				sessionStorage.setItem("point_lat", lat);
				sessionStorage.setItem("point_lon", lon);
				sessionStorage.setItem("point_id", id);
				sessionStorage.setItem("reserve", true);
				document.getElementById("lbl_route_start").innerHTML = shuttle_start;
				// document.getElementById("lbl_route_start").innerHTML = "Shuttle Start";
				document.getElementById("lbl_end").innerHTML = obj["point_name"];

				point_lat = sessionStorage.getItem("point_lat");
				point_lon = sessionStorage.getItem("point_lon");
				if (point_lat != "undefined" && point_lon != "undefined") {
					var point_maker = new longdo.Marker(
						{ lon: parseFloat(point_lon), lat: parseFloat(point_lat) },
						{
							title: "จุดที่ทำรายการจอง",
						}
					);
					map.Overlays.add(point_maker);
				}
			}
		},
		error: function (error) {
			console.log(error);
		},
	});
}

function getLocation(
	tracking_date,
	tracking_time,
	tracking_trip,
	tracking_route,
	tracking_veh_id,
	tracking_drv_id
) {
	$.ajax({
		type: "POST",
		url: "Tracking_info/get_location",
		data: {
			tracking_date: tracking_date,
			tracking_trip: tracking_trip,
			tracking_route: tracking_route,
			tracking_veh_id: tracking_veh_id,
			tracking_drv_id: tracking_drv_id,
		},
		success: function (response) {
			console.log(response);
			let points = [];
			try {
				var obj = JSON.parse(response);

				let points = [];

				for (var i = 0; i < obj.length; i++) {
					var res_lat = obj[i]["point_lat"];
					var res_lon = obj[i]["point_lon"];
					var point_name = obj[i]["point_name"];
					var point_no = i + 1;

					let point = { lon: parseFloat(res_lon), lat: parseFloat(res_lat) };
					points.push(point); // เก็บจุดทั้งหมดไว้ในอาร์เรย์

					// เพิ่ม Marker แสดงจุด พร้อมล็อกไม่ให้ขยับ
					var point_marker = new longdo.Marker(point, {
						title: `Point ${point_no}: ${point_name}`,
						detail: `ลำดับที่: ${point_no}`,
						draggable: false,
					});

					map.Overlays.add(point_marker);
				}

				// เพิ่มเส้นทาง (Route) เชื่อมจุดทั้งหมด
				map.Route.add(points);
				map.Route.mode(longdo.RouteMode.Cost);
				map.Route.search();

				//map.Route.search();
			} catch (error) {
				console.error("Error parsing JSON or processing data:", error);

				// Optionally, you can add user feedback here
				//alert("Failed to load the points. Please try again.");
			}
		},
		error: function (err) {
			console.error(err);
		},
	});

	//set interval
	setInterval(async function () {
		reserve_point = sessionStorage.getItem("point_id");
		$.ajax({
			type: "POST",
			url: "Tracking_info/get_bus_location",
			data: {
				tracking_date: tracking_date,
				tracking_time: tracking_time,
				tracking_trip: tracking_trip,
				tracking_route: tracking_route,
				tracking_veh_id: tracking_veh_id,
				tracking_drv_id: tracking_drv_id,
				reserve_point: reserve_point,
			},
			success: function (response) {
				var obj = response;
				obj = JSON.parse(response);
				var new_obj = obj["location"].split(",");
				var lat = new_obj[0];
				var lon = new_obj[1];

				// ลบหมุดเก่าออก
				map.Overlays.remove(veh_marker);

				veh_marker = new longdo.Marker(
					{ lat: parseFloat(lat), lon: parseFloat(lon) },
					{
						title: "Vehicle here",
						detail: "",
						icon: {
							url: "../assets/img/img/bus.png",
							offset: { x: 15, y: 15 },
							size: { width: 32, height: 32 },
						},
					}
				);

				map.Overlays.add(veh_marker);

				point_lat = sessionStorage.getItem("point_lat");
				point_lon = sessionStorage.getItem("point_lon");
				com_name = sessionStorage.getItem("com_name");
				com_lat = sessionStorage.getItem("com_lat");
				com_lon = sessionStorage.getItem("com_lon");

				console.log(com_name);

				if (
					is_reserve === true &&
					obj["car_arrage"] !== "true" &&
					obj["car_arrage"] !== true
				) {
					//มีการจองแลเวรถไม่ได้จุด
					longdoMapRouting(point_lon, point_lat, lon, lat);
				} else {
					//มีการจองแล้วรถเข้าขุดแล้ว
					//is_reserve = sessionStorage.getItem("reserve");

					console.log(is_reserve);
					// if(is_reserve){
					// 	//เข้าจุดแล้วจองแล้ว
					// 	noticeVal(0);
					// }
					var lang = getCookie("ck_emp_lang");
					var lbl_arrive = lang == "th" ? "ถึงแล้ว" : "Arrived";

					if (tracking_trip === "out") {
						//longdoMapRouting(com_lon, com_lat, lon, lat);
						document.getElementById("get_distance_km").innerHTML = "";
						document.getElementById("get_distance_time").innerHTML = "";
						document.getElementById("lbl_end").innerHTML = lbl_arrive;
					} else {
						longdoMapRouting(com_lon, com_lat, lon, lat);
						document.getElementById("lbl_end").innerHTML = com_name;
					}
				}
			},
			error: function (error) {
				console.error(error);
			},
		});
	}, 3000);
}

function longdoMapRouting(flon, flat, tlon, tlat) {
	// // Create a new Date object
	$.ajax({
		url: "https://api.longdo.com/RouteService/json/route/guide?",
		dataType: "jsonp",
		type: "GET",
		contentType: "application/json",
		data: {
			key: "e516b73dc9f6e987181a591230bbe207",
			flon: parseFloat(flon),
			flat: parseFloat(flat),
			tlon: parseFloat(tlon),
			tlat: parseFloat(tlat),
		},
		success: function (results) {
			console.log(results);
			var distance_km = results["data"][0]["distance"] / 1000;
			var distance_time = results["data"][0]["interval"] / 60;
			var new_distance_km = distance_km.toFixed(2);
			var new_distance_time = distance_time.toFixed(2);

			document.getElementById("get_distance_km").innerHTML = new_distance_km;
			document.getElementById("get_distance_time").innerHTML =
				formatMinutes(new_distance_time);

			calculated_distance(flon, flat, tlon, tlat);
			// }
		},
		error: function (xhr) {},
	});
}

function calculated_distance(flon, flat, tlon, tlat) {
	if (flon == tlon && flat == tlat) {
		// กรณีที่เดียวกัน
		noticeVal(0);
	} else {
		//ต้องคำนวณระยะห่าง
		let distance = cal_distance(flat, flon, tlat, tlon);
		if (distance <= 0.2) {
			noticeVal(distance);
			document.getElementById("get_distance_time").innerHTML = 0;

			var geom1 = new longdo.Polyline([
				{ lon: flon, lat: flat },
				{ lon: tlon, lat: tlat },
			]);
			map.Overlays.add(geom1);
		}
	}
}

function cal_distance(flat, flon, tlat, tlon) {
	//ref. http://thabot47.blogspot.com/2010/12/blog-post.html

	var R = 6371; // รัศมีของโลก หน่วยเป็นกิโลเมตร
	var duser_lat = to_rad(tlat - flat);
	var duser_lon = to_rad(tlon - flon);
	var rad_flat = to_rad(flat);
	var rad_tlat = to_rad(tlat);

	var a =
		Math.sin(duser_lat / 2) * Math.sin(duser_lat / 2) +
		Math.cos(rad_flat) *
			Math.cos(rad_tlat) *
			Math.sin(duser_lon / 2) *
			Math.sin(duser_lon / 2);

	var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
	var distance = R * c;

	return distance;
}

function to_rad(value) {
	return (value * Math.PI) / 180;
}

function noticeVal(distance = "") {
	var chk_cookie = document.getElementById("get_cookie").value;

	//var chk_session_point = localStorage.getItem("local_point");

	if (chk_cookie === "false" || chk_cookie === "") {
		// ตรวจสอบว่าเบราว์เซอร์อนุญาตให้เล่นเสียง
		var audio = new Audio("../assets/img/img/not-bad-549.mp3");

		// แสดง Swal ก่อน และเล่นเสียงหลังจากการโต้ตอบ
		Swal.fire({
			title: "รถใกล้ถึงแล้ว",
			text: "กรุณาเตรียมตัว",
			icon: "warning",
			confirmButtonText: "OK",
			confirmButtonColor: "#DD6B55",
			allowOutsideClick: false,
		}).then((result) => {
			if (result.isConfirmed) {
				document.getElementById("get_cookie").value = "true";

				sessionStorage.setItem("local_point", true);

				// เล่นเสียงหลังจากที่ผู้ใช้กดปุ่ม OK
				audio.play().catch((error) => {
					console.error("Failed to play audio:", error);
				});
			}
		});
	}
}

function formatMinutes(minutes) {
	const hrs = Math.floor(minutes / 60); // Calculate hours
	const min = Math.floor(minutes % 60); // Calculate remaining minutes
	var lang = getCookie("ck_emp_lang");
	if (lang === "th") {
		// Format in Thai
		let result = hrs > 0 ? hrs + " ชั่วโมง" : ""; // Add hours if greater than 0
		result += min > 0 ? (result ? " " : "") + "<br>" + min + " นาที" : ""; // Add minutes if greater than 0
		return result || "0 นาที"; // Default to "0 นาที" if no time
	} else {
		// Format in English
		let result = hrs > 0 ? hrs + " hour" + (hrs > 1 ? "s" : "") : ""; // Add hours with pluralization
		result +=
			min > 0
				? (result ? ", " : "") + "<br>" + min + " minute" + (min > 1 ? "s" : "")
				: ""; // Add minutes with pluralization
		return result || "0 minutes"; // Default to "0 minutes" if no time
	}
	console.log(result);
}

// function formatMinutes(minutes) {
// 	const hrs = Math.floor(minutes / 60); // ชั่วโมง
// 	const min = Math.floor(minutes % 60); // นาทีที่เหลือ

// 	let result = hrs > 0 ? hrs + " hour" + (hrs > 1 ? "s" : "") : "";
// 	result +=
// 		min > 0
// 			? (result ? ", " : "") + min + " minute" + (min > 1 ? "s" : "")
// 			: "";

// 	return result || "0 "+lbl_min; // กรณีถ้าไม่มีเวลาเลย
// }

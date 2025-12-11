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

	map.Event.bind(longdo.EventName.Ready, function () {
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
});


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
				
				document.getElementById("lbl_drv_name").innerHTML = json_data[0].driver;
				
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
			var shuttle_start = lang == "th" ? "ตำแหน่งรถ" : "Shuttle Location";
			obj["point_lat"];
			obj["point_lon"];
			if (obj["point_lat"] === "" && obj["point_lon"] === "") {
				//ไมใ่ได้จอง เซ็ต เข้าบริษัทโดบตรง
				com_name = sessionStorage.getItem("com_name"); // ทำ company

				sessionStorage.setItem("reserve", false); // สภานะการจอง
				sessionStorage.setItem("point_lat", "");
				sessionStorage.setItem("point_lon", "");
				document.getElementById("lbl_route_start").innerHTML = shuttle_start;
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

					// ฟังก์ชันแปลง SVG เป็น Base64 เพื่อให้รองรับ iOS
					function svgToBase64(svgString) {
						return (
							"data:image/svg+xml;base64," +
							btoa(unescape(encodeURIComponent(svgString)))
						);
					}

					// สร้าง SVG สำหรับ Marker โดยใช้ค่าตัวเลขจากตัวแปร `point_no`
					const svgString = `
								<svg xmlns="http://www.w3.org/2000/svg" width="37" height="51" viewBox="0 0 37 51">
									<g filter="">
										<!-- เปลี่ยนหมุดเป็นสีเหลือง -->
										<path fill-rule="evenodd" clip-rule="evenodd" d="M18.43 4.84h.05c7.01 0 12.61 5.8 12.55 12.58a12.51 12.51 0 0 1-12.5 12.44h-.12c-6.9 0-12.47-5.56-12.54-12.44A12.6 12.6 0 0 1 18.43 4.84Z" fill="#FFD700"/>
										<path d="M18.45 2a15.2 15.2 0 0 0-15.4 16.6 16.4 16.4 0 0 0 3.2 8.3l.63.86a136.1 136.1 0 0 1 7.2 10.6c.65 1.1 1.17 2.27 1.7 3.52.33.76 1.12 1.85 2.54 1.89h.2c1.47 0 2.28-1.11 2.62-1.9a30.86 30.86 0 0 1 1.7-3.5c2.17-3.7 4.69-7.15 7.24-10.66l.59-.82a16.46 16.46 0 0 0 3.18-8.27A15.2 15.2 0 0 0 18.45 2Z" fill="#FFD700" stroke="#000" stroke-width=".5" stroke-miterlimit="10"/>
										<path d="M18.42 45.9c2.42 0 4.38-.65 4.38-1.44 0-.78-1.96-1.43-4.38-1.43s-4.39.65-4.39 1.43c0 .79 1.97 1.43 4.39 1.43Z" fill="#F0A617"/>
									</g>
									<!-- วงกลมตรงกลางสีขาว -->
									<circle cx="18.5" cy="17.5" r="9" fill="white" stroke="black" stroke-width="1"/>
									
									<!-- ข้อความเลข (รองรับ iOS) -->
									<text x="50%" y="35%" text-anchor="middle" font-size="14" font-weight="bold" fill="black"
										dominant-baseline="middle" alignment-baseline="middle">
										${point_no}
									</text>
								</svg>`;

					// สร้าง Marker บนแผนที่ Longdo Map
					const point_marker = new longdo.Marker(point, {
						title: `Point ${point_no}: ${point_name}`,
						detail: `ลำดับที่: ${point_no}`,
						draggable: false, // ล็อกไม่ให้ขยับ
						icon: {
							url: svgToBase64(svgString), // ใช้ Base64 เพื่อให้ iOS แสดงผลได้
						},
					});

					map.Overlays.add(point_marker);

				}

				//map.Route.search();
			} catch (error) {
				console.error("Error parsing JSON or processing data:", error);
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

				var time = obj["time"];
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

				if (
					is_reserve === true &&
					obj["car_arrage"] !== "true" &&
					obj["car_arrage"] !== true
				) {
					//มีการจอง แล้วรถไม่ได้จุด
					longdoMapRoutingNew(point_lon, point_lat, lon, lat, time);

				} else {
					//มีการจองแล้วรถเข้าขุดแล้ว
					//is_reserve = sessionStorage.getItem("reserve");

					console.log(is_reserve);


					var lang = getCookie("ck_emp_lang");
					var lbl_arrive = lang == "th" ? "ถึงแล้ว" : "Arrived";

					if (tracking_trip === "out") {
						//longdoMapRouting(com_lon, com_lat, lon, lat);
						document.getElementById("get_distance_km").innerHTML = "";
						document.getElementById("get_distance_time").innerHTML = "";
						document.getElementById("lbl_end").innerHTML = lbl_arrive;

						//hide card
					} else {
						cal_comdistance(com_lon, com_lat, lon, lat);
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

var pre_time = null;
var pre_dis = null;

function longdoMapRouting(flon, flat, tlon, tlat) {
    $.ajax({
        url: "https://api.longdo.com/RouteService/json/route/guide?",
        dataType: "jsonp",
        type: "GET",
        contentType: "application/json",
        data: {
            key: "e516b73dc9f6e987181a591230bbe207", // ใช้ Key ของตัวเอง
            flon: parseFloat(flon),
            flat: parseFloat(flat),
            tlon: parseFloat(tlon),
            tlat: parseFloat(tlat),
        },
        success: function (results) {
            console.log(results);
            // คำนวณระยะทาง
            var distance_km = results["data"][0]["distance"] / 1000;
            var distance_time = results["data"][0]["interval"] / 60;
            var new_distance_km = distance_km.toFixed(2);
            var new_distance_time = distance_time.toFixed(2);

            document.getElementById("get_distance_km").innerHTML = new_distance_km;
            document.getElementById("get_distance_time").innerHTML = formatMinutes(new_distance_time);

			calculated_distance(flon, flat, tlon, tlat);

        },
        error: function (xhr) {
            console.error("Error fetching route:", xhr);
        }
    });
}

function parseTimeToDate(timeStr) {
    let today = new Date().toISOString().split("T")[0]; // เอาวันที่ปัจจุบัน (YYYY-MM-DD)
    return new Date(`${today}T${timeStr}`); // แปลงเป็น Date
}

var locations = [];

function longdoMapRoutingNew(flon, flat, tlon, tlat, time){

	locations.push({ lat: flat, lon: tlon, time: time });

	let distance = cal_distance(flat, flon, tlat, tlon); 

	console.log(`📍 distance: ${distance}`);


	// แสดงผลเส้นทาง
	var new_distance_km = distance.toFixed(2);

	var time_test = calculateETA(flat, flon, tlat, tlon);

	console.log('Time Test', time_test);


	calculated_distance(flon, flat, tlon, tlat); // คำนวณระยะทาง alert

	document.getElementById("get_distance_km").innerHTML = new_distance_km;


}


// คำนวณ ETA โดยใช้ความเร็วเฉลี่ย (km/h)
function calculateETA(lat1, lon1, lat2, lon2, avgSpeedKmh = 60) {
    let distance = haversine(lat1, lon1, lat2, lon2); // หา ระยะทาง (กม.)
    let etaHours = distance / avgSpeedKmh; // ETA (ชั่วโมง)
    let etaMinutes = etaHours * 60; // แปลงเป็นนาที

    console.log(`📏 ระยะทาง: ${distance.toFixed(2)} กม.`);
    console.log(`⌛ เวลาเดินทาง: ${etaMinutes.toFixed(0)} นาที`);

	document.getElementById("get_distance_time").innerHTML = etaMinutes.toFixed(0) + " นาที";

    return formatMinutes(etaMinutes);
}


function haversine(lat1, lon1, lat2, lon2) {
    const R = 6371; // รัศมีโลก (km)
    const toRad = (angle) => angle * (Math.PI / 180);

    let dLat = toRad(lat2 - lat1);
    let dLon = toRad(lon2 - lon1);
    let a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
    let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c; // ระยะทางเป็นกิโลเมตร
}


function longdoMapRoutingtoCompany(flon, flat, tlon, tlat) {
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

			if (pre_dis === null || pre_time === null) {
				pre_dis = new_distance_km;
				pre_time = new_distance_time;
			}

			document.getElementById("get_distance_km").innerHTML = new_distance_km;
			document.getElementById("get_distance_time").innerHTML = formatMinutes(new_distance_time);


			var route = results["data"][0]["guide"];
            var routePoints = [];

            // ดึงค่าพิกัดจาก Guide API เพื่อนำไปสร้างเส้น Polyline
            for (var i = 0; i < route.length; i++) {
                routePoints.push({ lon: route[i]["lon"], lat: route[i]["lat"] });
            }

            // ลบเส้นเก่าออกก่อน (ถ้ามี)
            map.Route.clear();

			// วาดเส้นทาง

			calculated_distance_com(flon, flat, tlon, tlat);
			// }
		},
		error: function (xhr) {},
	});
}


function cal_comdistance(flon, flat, tlon, tlat){

	let distance = cal_distance(flat, flon, tlat, tlon); 

	console.log(`📍 distance Company : ${distance}`);

	 //แสดงผลเวลา
	 $.ajax({
        url: "https://api.longdo.com/RouteService/json/route/guide?",
        dataType: "jsonp",
        type: "GET",
        contentType: "application/json",
        data: {
            key: "e516b73dc9f6e987181a591230bbe207", // ใช้ Key ของตัวเอง
            flon: parseFloat(flon),
            flat: parseFloat(flat),
            tlon: parseFloat(tlon),
            tlat: parseFloat(tlat),
        },
        success: function (results) {
            console.log(results);
            // คำนวณระยะทาง
            var distance_km = results["data"][0]["distance"] / 1000;
            var distance_time = results["data"][0]["interval"] / 60;
            var new_distance_km = distance_km.toFixed(2);
            var new_distance_time = distance_time.toFixed(2);

            //document.getElementById("get_distance_km").innerHTML = new_distance_km;
            document.getElementById("get_distance_time").innerHTML = formatMinutes(new_distance_time);

			//calculated_distance(flon, flat, tlon, tlat);

        },
        error: function (xhr) {
            console.error("Error fetching route:", xhr);
        }
    });

	// แสดงผลเส้นทาง
	var new_distance_km = distance.toFixed(2);

	calculated_distance_com(flon, flat, tlon, tlat); // คำนวณระยะทาง alert

	document.getElementById("get_distance_km").innerHTML = new_distance_km;


}

//for alert

function calculated_distance(flon, flat, tlon, tlat) {
	if (flon == tlon && flat == tlat) {
		// กรณีที่เดียวกัน
		noticeVal(0);
	} else {
		//ต้องคำนวณระยะห่าง
		let distance = cal_distance(flat, flon, tlat, tlon);
		if (distance <= 0.3) {
			noticeVal(distance);
			document.getElementById("get_distance_time").innerHTML = 0;
		}
	}
}

function calculated_distance_com(flon, flat, tlon, tlat) {
	if (flon == tlon && flat == tlat) {
		// กรณีที่เดียวกัน
		noticeVal(0);
	} else {
		//ต้องคำนวณระยะห่าง
		let distance = cal_distance(flat, flon, tlat, tlon);
		if (distance <= 0.2) {
			noticeValCompany(distance);
			document.getElementById("get_distance_time").innerHTML = 0;
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

	var lang = getCookie("ck_emp_lang");

	if (chk_cookie === "false" || chk_cookie === "") {
		// ตรวจสอบว่าเบราว์เซอร์อนุญาตให้เล่นเสียง
		var audio = new Audio("../assets/img/img/not-bad-549.mp3");


		if(lang == 'th'){
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
		}else{

			// แสดง Swal ก่อน และเล่นเสียงหลังจากการโต้ตอบ
			Swal.fire({
				title: "Shuttle is arriving soon.",
				text: "Please ready.",
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
}

function noticeValCompany(distance = "") {
	// ตรวจสอบว่าเบราว์เซอร์อนุญาตให้เล่นเสียง
	var audio = new Audio("../assets/img/img/not-bad-549.mp3");

	var lang = getCookie("ck_emp_lang");

	// แสดง Swal ก่อน และเล่นเสียงหลังจากการโต้ตอบ
	
	if(lang == "th") { 
		Swal.fire({
			title: "มาถึงแล้ว",
			text: "",
			icon: "warning",
			confirmButtonText: "OK",
			confirmButtonColor: "#DD6B55",
			allowOutsideClick: false,
		}).then((result) => {
			if (result.isConfirmed) {

				// // เล่นเสียงหลังจากที่ผู้ใช้กดปุ่ม OK
				audio.play().catch((error) => {
					console.error("Failed to play audio:", error);
				});

				window.location.href = "Home";
			}
		});
	} else { 
		Swal.fire({
			title: "Arrived",
			text: "",
			icon: "warning",
			confirmButtonText: "OK",
			confirmButtonColor: "#DD6B55",
			allowOutsideClick: false,
		}).then((result) => {
			if (result.isConfirmed) {

				// // เล่นเสียงหลังจากที่ผู้ใช้กดปุ่ม OK
				audio.play().catch((error) => {
					console.error("Failed to play audio:", error);
				});

				window.location.href = "Home";
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
		let result = hrs > 0 ? hrs + " hr" + (hrs > 1 ? "s" : "") : ""; // Add hours with pluralization
		result +=
			min > 0
				? (result ? ", " : "") + "<br>" + min + " mins." + (min > 1 ? "s" : "")
				: ""; // Add minutes with pluralization
		return result || "0 minutes"; // Default to "0 minutes" if no time
	}
}

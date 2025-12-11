$(document).ready(async function () {
	await getData();
	//getTrip();
	await getTicket();
    check_notification();

});

function getData() {
    $.ajax({
        type: "POST",
        url: "Home/get_data",
        success: function (response) {
            const json_data = JSON.parse(response);

            const emp = json_data.emp_data || {};
            const lang = json_data.lang;
            const isThai = lang === "th";

            const emp_code = emp.emp_code || "";
            const emp_name = `${emp.emp_name || ""} ${emp.emp_lname || ""}`.trim();

            const pickup_code = emp.pickup_code || "";
            const pickup_name = emp.pickup_name || "";
            const pickup_point_info = (!pickup_code && !pickup_name)
                ? `<span style='color:red;'>${isThai ? "มาเอง ไม่พบข้อมูลสายรถ" : "No Pick up Point data found"}</span>`
                : `${pickup_code} ${pickup_name}`;
            const pick_up = `<span style='color:black;'><b>${isThai ? "จุดขึ้นรถ" : "Pick up Point"}</b></span> ${pickup_point_info}`;

            const pick_time = `${isThai ? "เวลาขึ้นรถกะเช้า" : "Time Get Day Ship"} ${emp.pickup_timein || ""}`;
            const pick_time_out = `${isThai ? "เวลาขึ้นรถกะดึก" : "Time Get Night Ship"} ${emp.pickup_timeout || ""}`;

            const isRouteOutInvalid = !emp.route_out_code && !emp.route_out_name;
            const route_out = isRouteOutInvalid
                ? `<span style="color:black;">${isThai ? "สายรถรับออก" : "Your Route Trip out"} </span><span style="color:red;">${isThai ? "มาเอง ไม่พบข้อมูลสายรถ" : "No Route Out data found"}</span>`
                : `<span style="color:black;">${isThai ? "สายรถรับออก" : "Your Route Trip out"} </span>${emp.route_out_code || ""} ${emp.route_out_name || ""}`;

            const drop_off = isRouteOutInvalid
                ? `<span style="color:black;"><b>${isThai ? "จุดลงรถ" : "Drop off Point"}</b></span> <span style="color:red;">${isThai ? "กลับเอง ไม่พบข้อมูลสายรถ" : "No Drop off Point data found"}</span>`
                : `<span style="color:black;"><b>${isThai ? "จุดลงรถ" : "Drop off Point"}</b></span> ${emp.dropoff_code || ""} ${emp.dropoff_name || ""}`;

            const today = new Date();
            const val_day = formatDateByLang(today, isThai ? 'th-TH' : 'en-US');

            // ตรวจสอบ DOM ก่อนใช้
            if (document.getElementById("lbl_emp_code"))
                document.getElementById("lbl_emp_code").innerHTML = emp_code;
            if (document.getElementById("lbl_name"))
                document.getElementById("lbl_name").innerHTML = emp_name;
            if (document.getElementById("lbl_pickup"))
                document.getElementById("lbl_pickup").innerHTML = pick_up;
            if (document.getElementById("lbl_dropoff"))
                document.getElementById("lbl_dropoff").innerHTML = drop_off;
            if (document.getElementById("lbl_today"))
                document.getElementById("lbl_today").innerHTML = val_day;

            // เพิ่มเติม (ถ้ามี DOM element)
            // if (document.getElementById("lbl_pick_time"))
            //     document.getElementById("lbl_pick_time").innerHTML = pick_time;
            // if (document.getElementById("lbl_pick_time_out"))
            //     document.getElementById("lbl_pick_time_out").innerHTML = pick_time_out;
        }
    });
}



	// change format date 
	function formatDateByLang(date, lang) {
		let options = {
			weekday: 'long',
			day: '2-digit',
			month: 'long',
			year: 'numeric'
		};

		let formatter = new Intl.DateTimeFormat(lang, options);
		let formatted = formatter.format(date);

		// ถ้าเป็นภาษาไทย ให้แปลงปี ค.ศ. เป็น พ.ศ.
		if (lang === 'th-TH') {
			let buddhistYear = date.getFullYear() + 543;
			formatted = formatted.replace(date.getFullYear().toString(), buddhistYear.toString());
		}

		return formatted;
	}




function getCookie(cookieName) {
    let cookie = {};
    document.cookie.split(';').forEach(function (el) {
        let [key, value] = el.split('=');
        cookie[key.trim()] = value;
    })
    return cookie[cookieName];
};


function getTicket(){
         // แสดงข้อความ Loading... ก่อน
    document.getElementById("tickets_reservation").innerHTML = '<div class="text-center text-muted">Loading...</div>';
	$.ajax({
		type: "POST",
		url: "Home/get_tickets",
		success: function (response) {
            
			json_data = JSON.parse(response);

            console.log(json_data);

			create_content_reserve(json_data.data, json_data.lang);
		},
		error: function(error){
			console.error(error);
		}
	});
};

async function create_content_reserve(json_data, lang) {
    let html_code = "";

    console.log("Creating reservation content...", json_data);

    if (json_data.length > 0) {
        for (const row of json_data) {
            const {
                driver_fullname, driver_name, post_combined,
                post_drv_id, post_id, post_time, post_veh_id,
                reserve_com_id, reserve_date, reserve_id,
                reserve_point, reserve_round, reserve_route,
                reserve_status, reserve_time, reserve_trip,
                route_code, route_id, route_name,
                point_lat, point_lon, tracking_lat, tracking_lon,
                veh_license, tracking_post_status, tracking_id, time,
                point_name, sql_tracking
            } = row;

            const tripLabel = (lang === "th")
                ? `${reserve_trip === "in" ? "รับเข้า" : "ส่งออก"} เวลา ${reserve_time}`
                : `${reserve_trip === "in" ? "Trip In" : "Trip Out"} at ${reserve_time}`;

            statusBadge = reserve_status === "Waiting"
                ? `<span class="badge bg-warning"><i class="bi bi-clock"></i> ${lang === "th" ? "รอ Confirm" : "Waiting Confirm"}</span>`
                : `<span class="badge bg-success"><i class="bi bi-check-circle"></i> ${lang === "th" ? "Confirm" : "Confirm"}</span>`;

            if(reserve_status === "Waiting"){
               statusBadge =  `<span class="badge bg-warning"><i class="bi bi-clock"></i> ${lang === "th" ? "รอ Confirm" : "Waiting Confirm"}</span>`
            }else if(reserve_status === "Submitted"){
                statusBadge = `<span class="badge bg-success"><i class="bi bi-check-circle"></i> ${lang === "th" ? "Confirm" : "Confirm"}</span>`;
            }else if(reserve_status === "Cancelled"){
                statusBadge = `<span class="badge bg-danger"><i class="bi bi-x-circle"></i> ${lang === "th" ? "ยกเลิก" : "Cancelled"}</span>`;
            }else if(reserve_status === "Rejected"){
                statusBadge = `<span class="badge bg-danger"><i class="bi bi-x-circle"></i> ${lang === "th" ? "ปฏิเสธ" : "Rejected"}</span>`;
            }else if(reserve_status === "Completed"){
                statusBadge = `<span class="badge bg-success"><i class="bi bi-check-circle"></i> ${lang === "th" ? "เสร็จสิ้น" : "Completed"}</span>`;
            }else{
                statusBadge = `<span class="badge bg-secondary"><i class="bi bi-question-circle"></i> ${lang === "th" ? "ไม่ทราบสถานะ" : "Unknown Status"}</span>`;
            }   

            // จุดขึ้นรถ/จุดลงรถ label ตาม reserve_trip
            let pointLabel, pointIcon;
            if (reserve_trip === "in") {
                pointLabel = lang === "th" ? "จุดขึ้นรถ" : "Pick up Point";
                pointIcon = "bi-geo";
            } else {
                pointLabel = lang === "th" ? "จุดลงรถ" : "Drop off Point";
                pointIcon = "bi-geo-alt";
            }

            html_code += `
                <div class="card shadow-sm mt-5 border-0 p-0">
                    <div class="card-header d-flex justify-content-between align-items-center text-white bg-primary">
                        <h5 class="mb-0 text-white"><i class="bi bi-bus-front-fill"></i> ${tripLabel}</h5>
                        ${statusBadge}
                    </div>
                    <div class="card-body mt-5">
                        <p class="mb-1">
                            <strong>
                                <i class="bi bi-calendar"></i>
                                ${lang === "th" ? "วันที่" : "Date"}:
                                ${lang === "th" ? formatDateThaiEng(reserve_date, 'th') : formatDateThaiEng(reserve_date, 'en')}
                            </strong>
                        </p>
                        <p class="mb-1"><strong><i class="bi bi-signpost-2"></i> ${lang === "th" ? "เส้นทาง" : "Route"}:</strong> ${route_code} - ${route_name}</p>
                        <p class="mb-1"><strong><i class="${pointIcon}"></i> ${pointLabel}:</strong> ${reserve_point ? point_name : (lang === "th" ? "ไม่ระบุ" : "N/A")}</p>
            `;

            if(reserve_trip === "in"){
                if(time == 'Waiting'){
                    html_code += `<p class="mb-1">
                        <strong><i class="bi bi-clock"></i> ${lang === "th" ? "เวลารับ" : "Pickup Time"}:</strong>
                        ${lang === "th" ? '<span class="badge bg-secondary">รออนุมัติเวลา</span>' : '<span class="badge bg-secondary">Waiting</span>'}
                    </p>`;
                }else{
                    html_code += `<p class="mb-1">
                        <strong><i class="bi bi-clock"></i> ${lang === "th" ? "เวลารับ" : "Pickup Time"}:</strong> ${time}
                    </p>`;
                }
            }            

            if (tracking_id && tracking_post_status === "go") {
                html_code += `
                    <p class="mb-1"><strong><i class="bi bi-person-fill"></i> ${lang === "th" ? "คนขับ" : "Driver"}:</strong> ${driver_fullname}</p>
                    <p class="mb-1"><strong><i class="bi bi-car-front"></i> ${lang === "th" ? "ทะเบียนรถ" : "Vehicle"}:</strong> ${veh_license}</p>
                 
                    <button class="btn btn-outline-primary w-100 mt-2" onclick="goTracking('${reserve_date}', '${post_time}', '${post_id}', '${reserve_trip}', '${post_combined}', '${post_veh_id}', '${post_drv_id}')">
                        <i class="bi bi-geo-alt-fill"></i> ${lang === "th" ? "นำทาง" : "Tracking"}
                    </button>
                `;
            } else if (tracking_id && tracking_post_status === "success") {
                html_code += `
                    <p class="mb-1"><strong><i class="bi bi-person-fill"></i> ${lang === "th" ? "คนขับ" : "Driver"}:</strong> ${driver_fullname}</p>
                    <p class="mb-1"><strong><i class="bi bi-car-front"></i> ${lang === "th" ? "ทะเบียนรถ" : "Vehicle"}:</strong> ${veh_license}</p>
                `;
            }


            html_code += `
                  
                    <button class="btn btn-outline-secondary w-100 mt-3 d-flex jutify-content-center"
                        onclick="goToReserveInfo('${reserve_id}')">
                        <i class="bi bi-info-circle mx-2"> </i> ${lang === "th" ? " ดูรายละเอียดการจอง" : " Reservation Details"}
                    </button>
            </div>
            </div>`;
        }
    } else {
        html_code = `<div class="alert alert-warning text-center" role="alert">
            <i class="bi bi-exclamation-circle"></i> ${lang === "th" ? "ไม่มีรายการจองรถในวันนี้" : "No vehicle reservations for today."}
        </div>`;
    }

    document.getElementById("tickets_reservation").innerHTML = html_code;
}


function formatDateThaiEng(dateStr, lang = "th") {
  // dateStr รูปแบบ "YYYY-MM-DD"
  const parts = dateStr.split("-");
  if(parts.length !== 3) return dateStr;

  const year = parseInt(parts[0], 10);
  const month = parseInt(parts[1], 10);
  const day = parseInt(parts[2], 10);

  const monthsTH = [
    "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
    "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
  ];

  const monthsEN = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
  ];

  if(lang === "th") {
    return `${day} ${monthsTH[month - 1]} ${year + 543}`;
  } else {
    return `${monthsEN[month - 1]} ${day}, ${year}`;
  }
}




function goToReserveInfo(reserve_id) {
        //sessionStorage.getItem('session_feature_set_item_user_cancel');
        sessionStorage.setItem("reserve_id", reserve_id);
        showLoader();
        window.location.href = "Reserve_info";
}

function longdoMapRouting(flon, flat, tlon, tlat) {
    return new Promise((resolve, reject) => {
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
                var distance_km = results["data"][0]["distance"] / 1000;
                var distance_time = results["data"][0]["interval"] / 60;
                var new_distance_km = distance_km.toFixed(2);
                var new_distance_time = distance_time.toFixed(2);

                // Format time
                var new_time = formatMinutes(new_distance_time);

                // Resolve the promise with the formatted time
                resolve(new_time);
            },
            error: function (xhr) {
                // Reject the promise in case of error
                reject(xhr);
            },
        });
    });
}

function formatMinutes(minutes) {
	const hrs = Math.floor(minutes / 60); // ชั่วโมง
	const min = Math.floor(minutes % 60); // นาทีที่เหลือ

	let result = hrs > 0 ? hrs + " ชั่วโมง" + (hrs > 1 ? "s" : "") : "";
	result +=
		min > 0
			? (result ? ", " : "") + min + " นาที" + (min > 1 ? "s" : "")
			: "";

	return result || "0 minutes"; // กรณีถ้าไม่มีเวลาเลย
}


function goTracking(reserve_date, post_time, post_id, reserve_trip, post_combined_route, post_veh_id, post_drv_id) {
    // ทำสิ่งที่ต้องการเพิ่มเติม
	sessionStorage.setItem('tracking_date', reserve_date);
     sessionStorage.setItem('tracking_time', post_time);
     sessionStorage.setItem('tracking_post_id', post_id);
     sessionStorage.setItem('tracking_route', post_combined_route);
     sessionStorage.setItem('tracking_trip', reserve_trip);
     sessionStorage.setItem('tracking_veh_id', post_veh_id);
     sessionStorage.setItem('tracking_drv_id', post_drv_id);
     showLoader();
     window.location.href= ('Tracking_info');
};

function check_notification() {
    $.ajax({
        type: "POST",
        url: "Home/get_fcm_token_by_emp_id",
        success: function (response) {
            const json_data = JSON.parse(response);

            console.log("FCM Token:", json_data.data);

            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if (!json_data.data || json_data.data.length === 0) {
                // กำหนดข้อความตามภาษา
                const message = json_data.lang === "th" 
                    ? "คุณยังไม่ได้เปิดการแจ้งเตือน ต้องการเปิดใช้งานหรือไม่?" 
                    : "You have not enabled notifications. Would you like to enable them?";

                const confirmButtonText = json_data.lang === "th" ? "ใช่" : "Yes";
                const cancelButtonText = json_data.lang === "th" ? "ไม่" : "No";

                // แสดง SweetAlert2
                Swal.fire({
                    title: json_data.lang === "th" ? "การแจ้งเตือน" : "Notification",
                    text: message,
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: confirmButtonText,
                    cancelButtonText: cancelButtonText,
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        // ดำเนินการเมื่อผู้ใช้กด "ใช่" หรือ "Yes"
                        console.log("User wants to enable notifications.");
                        // คุณสามารถเพิ่มโค้ดสำหรับเปิดการแจ้งเตือนได้ที่นี่
                        try {
                       // alert("ขอสิทธิ์แจ้งเตือน...");

                        // ✅ เริ่มต้น Firebase
                        if (!firebase.apps.length) {
                            firebase.initializeApp(firebaseConfig);
                        }
                        const messaging = firebase.messaging();


                        // ✅ ลงทะเบียน Service Worker
                        const registration = await navigator.serviceWorker.register("/firebase-messaging-sw.js", { scope: "/" });
                        console.log("✅ ลงทะเบียน Service Worker สำเร็จ:", registration);

                        await navigator.serviceWorker.ready;
                        console.log("✅ Service Worker พร้อมใช้งาน!");

                        // ✅ ขออนุญาตการแจ้งเตือน
                        const permission = await Notification.requestPermission();
                        if (permission !== "granted") {
                            console.log("❌ Notifications ถูกปฏิเสธ!");
                            alert("การแจ้งเตือนถูกปฏิเสธ!");
                            return;
                        }

                        console.log("✅ Notification permission granted.");

                        // ✅ ขอ FCM Token
                        const currentToken = await messaging.getToken({
                            vapidKey: "BAGG8TmRsF28ipbvUpsK75VVTSND2ltxv1zU3RhocOnyvDNvvwV5kJ2YU7KaKPYj_ln_9kSeEwhIDytGhQ_RCzs",
                            serviceWorkerRegistration: registration
                        });

                        if (currentToken) {
                            console.log("✅ Token ได้รับแล้ว:", currentToken);
                            //alert("✅ Token ได้รับแล้ว:");
                            localStorage.setItem("fcmToken", currentToken);

                            try {
                                const response = await fetch("Push/keepFCMToken", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json"
                                    },
                                    body: JSON.stringify({ token: currentToken })
                                });

                                const result = await response.json(); // หรือใช้ .text() ถ้า response ไม่ใช่ JSON
                                console.log("Server response:", result);
                            } catch (error) {
                                console.error("Fetch error:", error);
                                alert("error");
                            }

                            // console.log("✅ Token ถูกส่งไป Backend สำเร็จ!");
                            // alert("✅ สมัครรับการแจ้งเตือนสำเร็จ!");

							/*
							@author Natthanicha Pothiwat
							@Modify Date : 28/06/2025
							@Update : lang
							*/

							console.log("✅ Token ถูกส่งไป Backend สำเร็จ!");

							let txt_noti = json_data.lang === "th" ? "สมัครรับการแจ้งเตือนสำเร็จ !" : "Notification enabled !";
							alert("✅ " + txt_noti);
                        } else {
                            console.error("❌ ไม่สามารถรับ Token ได้!");
                            alert("ไม่สามารถรับ Token ได้!");
                        }
                    } catch (error) {
                        console.error("❌ Error:", error);
                        alert("Error: " + error.message);
                    }
                    } else {
                        console.log("User declined to enable notifications.");
                    }
                });
            }
        },
        error: function (error) {
            console.error("Error checking notifications:", error);
        },
    });
}

function showLoader() {
    var overlay = document.getElementById('loading-overlay');
    if (overlay) overlay.style.display = 'flex';
  }



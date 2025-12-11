$(document).ready(function () {
    var path = window.location.pathname;
    var segments = path.split("/");
    var segment = segments[segments.length - 1];

    const arr_calendar = ["Calendar", "Calendar_ad"];
    const arr_tracking = ["Tracking"];
    const arr_reserve = ["Reserve", "Reserve_ad", "Reserve_info"];
    const arr_rpa = ["Rpa", "Rpa_ad", "Rpa_info"];
    const arr_arrange = ["Arrange", "Arrange_ad", "Arrange_info"];
    const arr_employee = ["Employee", "Employee_ad", "Employee_info"];
    const arr_route = ["Route", "Route_ad", "Route_info"];
    const arr_round = ["Round", "Round_ad", "Round_info"];
    const arr_vendor = ["Vendor", "Vendor_ad", "Vendor_info"];
    const arr_driver = ["Driver", "Driver_ad", "Driver_info"];
    const arr_vehicle = ["Vehicle", "Vehicle_ad", "Vehicle_info"];
    const arr_closetime = ["CloseTime","CloseTime_ad","CloseTime_info"];
    const arr_company = ["Company","Company_ad","Company_info"];
    const arr_organization = ["Org"];
    const arr_emp_type = ["Emp_type"];
    const arr_emp_level = ["Emp_level","Emp_level_ad","Emp_level_info"];
    const arr_emp_permission = ["Permission","Permission_ad","Permission_info"];
    const arr_module = ["Module", "Module_ad", "Module_info"];
    const arr_approved_change_route = ["Approved_change_route"];
    const arr_arrange_standard = ["Arrange_standard"];
    const arr_arrange_route = ["Arrange_route"];
    const arr_reservation_route_not_match = ["Reservation_route_not_match"];
    const arr_reservation_late = ["Reservation_late"];
    const arr_approval_group = ["Approval_group"];
    const arr_vehicle_type = ["Vehicle_type"];
    const arr_tracking_vehicle = ["Tracking_vehicle"];
    const arr_schedule = ["schedule"];
    const arr_configuration = ["configuration"];
    const arr_combined_manual = ["Combined_manual"];
    const arr_user_account = ["User_account"];

    if (arr_calendar.includes(segment)) {
        $('#mn_item_Calendar').attr('class', 'menu-item active');
    }
    if (arr_tracking.includes(segment)) {
        $('#mn_item_Tracking').attr('class', 'menu-item active');
    }
    if (arr_reserve.includes(segment)) {
        $('#mn_item_Reserve').attr('class', 'menu-item active');
    }
    if (arr_approved_change_route.includes(segment)) {
        $('#mn_item_Approved_change_route').attr('class', 'menu-item active');
    }
    if (arr_arrange_standard.includes(segment)) {
        $('#mn_item_Arrange_standard').attr('class', 'menu-item active');
    }
    if (arr_arrange_route.includes(segment)) {
        $('#mn_item_Arrange_route').attr('class', 'menu-item active');
    }
    if (arr_rpa.includes(segment)) {
        $('#mn_item_Rpa').attr('class', 'menu-item active');
    }
    if (arr_combined_manual.includes(segment)) {
        $('#mn_item_Combined_manual').attr('class', 'menu-item active');
    }
    if (arr_arrange.includes(segment)) {
        $('#mn_item_Arrange').attr('class', 'menu-item active');
    }
    if (arr_reservation_route_not_match.includes(segment)) {
        $('#mn_item_Reservation_route_not_match').attr('class', 'menu-item active');
    }
    if (arr_reservation_late.includes(segment)) {
        $('#mn_item_Reservation_late').attr('class', 'menu-item active');
    }
    if (arr_employee.includes(segment)) {
        $('#mn_item_Employee').attr('class', 'menu-item active');
    }
    if (arr_route.includes(segment)) {
        $('#mn_item_Route').attr('class', 'menu-item active');
    }
    if (arr_round.includes(segment)) {
        $('#mn_item_Round').attr('class', 'menu-item active');
    }
    if (arr_vendor.includes(segment)) {
        $('#mn_item_Vendor').attr('class', 'menu-item active');
    }
    if (arr_driver.includes(segment)) {
        $('#mn_item_Driver').attr('class', 'menu-item active');
    }
    if (arr_vehicle.includes(segment)) {
        $('#mn_item_Vehicle').attr('class', 'menu-item active');
    }
    if (arr_tracking_vehicle.includes(segment)) {
        $('#mn_item_Tracking_vehicle').attr('class', 'menu-item active');
    }
    if (arr_schedule.includes(segment)) {
        $('#mn_item_Schedule').attr('class', 'menu-item active');
    }
    if (arr_configuration.includes(segment)) {
        $('#mn_item_Configuration').attr('class', 'menu-item active');
    }
    // if (arr_closetime.includes(segment)) {
    //     $('#mn_item_CloseTime').attr('class', 'menu-item active');
    // }
    if (arr_company.includes(segment)) {
        $('#mn_item_Company').attr('class', 'menu-item active');
    }
    if (arr_organization.includes(segment)) {
        $('#mn_item_Org').attr('class', 'menu-item active');
    }
    if (arr_emp_type.includes(segment)) {
        $('#mn_item_Emp_type').attr('class', 'menu-item active');
    }
    if (arr_emp_level.includes(segment)) {
        $('#mn_item_Emp_level').attr('class', 'menu-item active');
    }
    if (arr_user_account.includes(segment)) {
        $('#mn_item_User_account').attr('class', 'menu-item active');
    }
    if (arr_emp_permission.includes(segment)) {
        $('#mn_item_Permission').attr('class', 'menu-item active');
    }
    if (arr_module.includes(segment)) {
        $('#mn_item_Module').attr('class', 'menu-item active');
    }
    if (arr_approval_group.includes(segment)) {
        $('#mn_item_Approval_group').attr('class', 'menu-item active');
    }
    if (arr_vehicle_type.includes(segment)) {
        $('#mn_item_Vehicle_type').attr('class', 'menu-item active');
    }

    // const arr_dashboard = ["Home"];
    // const arr_helpdesk = ["Helpdesk", "Helpdesk_ad", "Helpdesk_info"];
    // const arr_report = ["Report_summary"];
    // const arr_system = ["System"];
    // const arr_company = ["Company","Company_info"];
    // const arr_organization = ["Organization"];
    // const arr_user = ["Mem"];
    // const arr_permission = ["Permission"];

    // if (arr_dashboard.includes(segment)) {
    //     $('#mn_item_dashboard').attr('class', 'menu-item active');
    // }

    // if (arr_helpdesk.includes(segment)) {
    //     $('#mn_item_helpdesk').attr('class', 'menu-item active');
    // }

    // if (arr_report.includes(segment)) {
    //     $('#mn_item_report').attr('class', 'menu-item active open');
    //     $('#mn_item_report_summary').attr('class', 'menu-item active');
    // }

    // if (arr_system.includes(segment)) {
    //     $('#mn_item_system').attr('class', 'menu-item active');
    // }

    // if (arr_company.includes(segment)) {
    //     $('#mn_item_company').attr('class', 'menu-item active');
    // }

    // if (arr_organization.includes(segment)) {
    //     $('#mn_item_organization').attr('class', 'menu-item active');
    // }

    // if (arr_user.includes(segment)) {
    //     $('#mn_item_user').attr('class', 'menu-item active');
    // }

    // if (arr_permission.includes(segment)) {
    //     $('#mn_item_permission').attr('class', 'menu-item active');
    // }


    // var mn_home = $('#mn_link_dashboard');
    // mn_home.click(function (event) {
    //     event.preventDefault();
    //     window.location.href = 'Home';
    // });

    // var mn_helpdesk = $('#mn_link_helpdesk');
    // mn_helpdesk.click(function (event) {
    //     event.preventDefault();
    //     window.location.href = 'Helpdesk';
    // })

    // var mn_report_summary = $('#mn_link_report_summary');
    // mn_report_summary.click(function (event) {
    //     event.preventDefault();
    //     window.location.href = 'Report_summary';
    // })

    // var mn_system = $('#mn_link_system');
    // mn_system.click(function (event) {
    //     event.preventDefault();
    //     window.location.href = 'System';
    // });

    // var mn_company = $('#mn_link_company');
    // mn_company.click(function (event) {
    //     event.preventDefault();
    //     window.location.href = 'Company';
    // })

    // var mn_link_organization = $('#mn_link_organization');
    // mn_link_organization.click(function (event) {
    //     event.preventDefault();
    //     window.location.href = 'Organization';
    // })

    // var mn_user = $('#mn_link_user');
    // mn_user.click(function (event) {
    //     event.preventDefault();
    //     window.location.href = 'Mem';
    // });

    // var mn_permission = $('#mn_link_permission');
    // mn_permission.click(function (event) {
    //     event.preventDefault();
    //     window.location.href = 'Permission';
    // })
});

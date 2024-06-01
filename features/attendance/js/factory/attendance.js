/**
 * Attendance factory
 */
angular
    .module('starter')
    .factory('Attendance', function ($state, $pwaRequest) {
        var factory = {};
        factory.value_id = null;
        factory.settings = {};
  
        factory.setValueId = function (valueId) {
            factory.value_id = valueId;
            return factory;
        };

        factory.getValueId = function () {
            return factory.value_id;
        };

        factory.setSettings = function (settings) {
            factory.settings = settings;
            return factory;
        };

        factory.getSettings = function () {
            return factory.settings;
        };

        factory.findAll = function (params) {  
                   
            return $pwaRequest.post('/attendance/mobile_view/findall', {
                urlParams: {
                    value_id: factory.value_id                   
                },
                data: params,
                cache: false
            });
        };

        factory.saveStartContent = function (values) {
            return $pwaRequest.post('attendance/mobile_view/save-start', {
                data: {
                    value_id  : factory.value_id,
                    startdate : values.startdate,
                    latitude  : values.latitude,
                    longitude : values.longitude,
                    project_id: values.project_id,
                    location_id: values.location_id,
                    note: values.note
                },
                refresh: true,
                cache: false
            });
        };

        factory.saveEndContent = function (values) {
            return $pwaRequest.post('attendance/mobile_view/save-end', {
                data: {
                    value_id    : factory.value_id,
                    startdate   : values.startdate,
                    latitude    : values.latitude,
                    longitude   : values.longitude,
                    enddate     : values.enddate,
                    tracking_id : values.tracking_id,
                    totaltime   : values.tracking_time
                },
                refresh: true,
                cache: false
            });
        };

        factory.leaveSubmit = function (params) {  
            return $pwaRequest.post('/attendance/mobile_view/leave-submit', {
                urlParams: {
                    value_id: factory.value_id                   
                },
                data: params,
                cache: false,
                refresh: true
            });
        };

        factory.findLeavesById = function (id) {  
            return $pwaRequest.post('/attendance/mobile_view/find-leaves', {
                urlParams: {
                    value_id: factory.value_id,
                    id : id                 
                },
                cache: false
            });
        };

        factory.cancelLeaveById = function (id) {  
            return $pwaRequest.post('/attendance/mobile_view/cancel-leave', {
                urlParams: {
                    value_id: factory.value_id,
                    id : id                 
                },
                cache: false
            });
        };

        factory.verifyScan = function (location_id) {  
            return $pwaRequest.post('/attendance/mobile_view/verify-scan', {
                urlParams: {
                    value_id: factory.value_id,
                    location_id : location_id                 
                },
                cache: false,
                refresh: true
            });
        };

        factory.attendanceHistory = function (month, year) {  
                   
            return $pwaRequest.post('/attendance/mobile_view/attendance-history', {
                urlParams: {
                    value_id: factory.value_id, month: month, year: year                 
                },
                cache: false
            });
        };

        
        factory.saveMessage = function (data) {
            return $pwaRequest.post('attendance/mobile_view/save-message', {
                data: {
                    message: data.message,
                    value_id: this.value_id                                
                },
                refresh: true,
                cache: false
            });
        };

        factory.customerMessages = function (data) {
            return $pwaRequest.post('attendance/mobile_view/customer-messages', {
                data: {
                     value_id: this.value_id                                
                },
                refresh: true,
                cache: false
            });
        };
       
        return factory;
    });
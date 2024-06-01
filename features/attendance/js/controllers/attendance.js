/**
 * Attendance Home version 1 controllers
 */
angular.module('starter')
    .controller('AttendanceController', function (Dialog, Loader, Application , $ionicPopup, Location, $filter, Customer, GoogleMaps, $rootScope, SB, Attendance, $scope, $state, $stateParams, $translate, $interval, $timeout, $ionicModal, $cordovaBarcodeScanner) {
        $scope.value_id = Attendance.value_id = $stateParams.value_id;
        $scope.is_logged_in = Customer.isLoggedIn();
        $scope.customer = Customer.customer;
        $scope.is_loading = false;
        $scope.is_checked_in = true;
        $scope.page_title = '';
        $scope.current_time = '';
        $scope.timeinterval = null;
        $scope.settings = {};
        $scope.postValues = {};
        $scope.payout_data = {};
        $scope.current_year = null;
        $scope.action = {
        	tab: 'attendance'
        };
        $scope.checkedClock = {
            hours: '00',
            minutes: '00',
            seconds: '00',
        };

        $rootScope.$on(SB.EVENTS.AUTH.loginSuccess, function () {
            if($scope.value_id){
                $scope.loadContent();
            }
	        $scope.is_logged_in = Customer.isLoggedIn();
	        $scope.customer = Customer.customer;
	    });

	    $rootScope.$on(SB.EVENTS.AUTH.logoutSuccess, function () {
            if($scope.value_id){
                $scope.loadContent();
            }
	        $scope.loadContent();
	        $scope.is_logged_in = Customer.isLoggedIn();
	        $scope.customer = Customer.customer;
	    });

		/**
	     *login
	     */
	    $scope.login = function(){
	        Customer.loginModal($scope);
	    }


        function getTimeRemaining(startdate) {
          var t =  Date.parse(new Date()) - Date.parse(startdate);
          var seconds = Math.floor((t / 1000) % 60);
          var minutes = Math.floor((t / 1000 / 60) % 60);
          var hours = Math.floor((t / (1000 * 60 * 60)));     
          return {
            'total': t,
            'hours': hours,
            'minutes': minutes,
            'seconds': seconds
          };
        }

        function initializeClock(startdate) {
            $scope.timeinterval = $interval(function(){
                var t = getTimeRemaining(startdate); 
                $scope.checkedClock.hours = ('0' + t.hours).slice(-2);
                $scope.checkedClock.minutes = ('0' + t.minutes).slice(-2);
                $scope.checkedClock.seconds = ('0' + t.seconds).slice(-2);
            },1000);
        }

        $scope.getLocation = function() {
            
            if (Location.isEnabled) {
                Location
                    .getLocation({timeout: 30000, enableHighAccuracy: false}, true)
                    .then(function (position) {
                        $scope.postValues.latitude = position.coords.latitude;
                        $scope.postValues.longitude = position.coords.longitude;
                        $scope.loadContent();
                    }, function () {
                        $scope.postValues.latitude = 0;
                        $scope.postValues.longitude = 0;
                        $scope.requestLocation();
                    });

            } else {
                $scope.postValues.latitude = 0;
                $scope.postValues.longitude = 0;
                $scope.requestLocation();
                $scope.loadContent();
            }
        }

        $scope.requestLocation = function(){
            Location.requestLocation(function () {
                $scope.loadContent();
            },function () {
                $scope.loadContent();
            }); 
        }

	    /**
	     *Load content
	     */
	    $scope.loadContent = function(){
            if(!Customer.isLoggedIn()){
                return false;
            }
          

            $scope.is_loading = true;
            var currentDateTime = new Date();
	    	$scope.postValues.currentDateTime = currentDateTime;           
            
            Attendance.findAll($scope.postValues).success(function (data) {	           
	    		$scope.page_title = data.page_title;
                $scope.payout_data = data;
                $scope.is_checked_in = data.is_checked_in;
                Attendance.setSettings(data.settings);//Set settings
                $scope.settings = data.settings;
                
                $timeout(function () {
                    if($scope.is_checked_in) {
                        if(angular.isDefined(data.timetracking.tracking_id)){
                           $scope.tracking_id =  data.timetracking.tracking_id;
                           $scope.postValues.startdate = data.timetracking.startdate;
                           initializeClock(moment(data.timetracking.startdate));
                        }                      
                    }else{                    
                         $scope.currentTime(); // When not checked in 
                    } 
                }, 300);                   
          
            }).error(function () {
	            $scope.is_loading = false;
	        }).finally(function () {
	            $scope.is_loading = false;
            });
	    }

	    

	    /**
	     *Customer avatar
	     */
	    $scope.customer_avatar = function (image) {      
	        if (image != '' && image != null && image != "null") {
	            return IMAGE_URL + 'images/customer' + image;
	        } else {
	            return "./features/attendance/assets/media/customer-placeholder.png"
	        }
	    };

        /**
         *   date curremt
         */
        $scope.currentTime = function () {
            if($scope.is_loading) return false;
            console.log($scope.settings.timezone);
            $scope.timeinterval = $interval(function(){
                  var today = new Date(new Date().toLocaleString('en', {timeZone:  $scope.settings.timezone})); //new Date();
                 // today.toLocaleString('en-US', { timeZone: $scope.settings.timezone })
                  var hours = today.getHours() < 10 ?  '0'+today.getHours() : today.getHours();
                  var minutes = today.getMinutes() < 10 ?  '0' + today.getMinutes() : today.getMinutes();
                  var seconds = today.getSeconds() < 10 ?  '0' + today.getSeconds() : today.getSeconds();
                  $scope.current_time = hours + ":" +minutes + ":" + seconds;

           },1000);
        }

  

        /**
         *   date curremt
         */
        $scope.currentDate = function () {
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth();         
            var yyyy = today.getFullYear();
            if(dd < 10) 
            {
                dd = "0"+dd ;
            } 
            
            var weekday = new Array(7);
            weekday[0] = $translate.instant("Sun", "attendance");
            weekday[1] = $translate.instant("Mon", "attendance");
            weekday[2] = $translate.instant("Tue", "attendance");
            weekday[3] = $translate.instant("Wed", "attendance");
            weekday[4] = $translate.instant("Thu", "attendance");
            weekday[5] = $translate.instant("Fri", "attendance");
            weekday[6] = $translate.instant("Sat", "attendance");
            var days = weekday[today.getDay()];
            var months = [  $translate.instant("Jan", "attendance"), 
                            $translate.instant("Feb", "attendance"), 
                            $translate.instant("Mar", "attendance"), 
                            $translate.instant("Apr", "attendance"), 
                            $translate.instant("May", "attendance"), 
                            $translate.instant("Jun", "attendance"), 
                            $translate.instant("Jul", "attendance"), 
                            $translate.instant("Aug", "attendance"), 
                            $translate.instant("Sep", "attendance"), 
                            $translate.instant("Oct", "attendance"), 
                            $translate.instant("Nov", "attendance"), 
                            $translate.instant("Dec", "attendance")
                        ];

            return $scope.payout_data.currentDate;//days+', '+dd+' '+months[mm]+' '+yyyy;        
        }; 


        $scope.getMonthFullName = function (mm){
             var months = [  $translate.instant("January", "attendance"), 
                            $translate.instant("February", "attendance"), 
                            $translate.instant("March", "attendance"), 
                            $translate.instant("April", "attendance"), 
                            $translate.instant("May", "attendance"), 
                            $translate.instant("June", "attendance"), 
                            $translate.instant("July", "attendance"), 
                            $translate.instant("August", "attendance"), 
                            $translate.instant("September", "attendance"), 
                            $translate.instant("October", "attendance"), 
                            $translate.instant("November", "attendance"), 
                            $translate.instant("December", "attendance")
                        ];
            return months[mm];
        }
     

      /**
         * leave details
         */
        $scope.leaveDeails = function(leave_deatil) { 
            $scope.is_loading = true;
            $scope.all_leaves = [];
            $ionicModal.fromTemplateUrl('features/attendance/assets/templates/l1/modal/leave_details.html', {
                scope: $scope,
                animation: 'slide-in-right-left'
            }).then(function(modal) { 
               
                Attendance
                    .findLeavesById(leave_deatil.id)
                    .then(function (data) {
                        $scope.all_leaves = data.collections;  
                        $scope.is_loading = false;  
                    }, function (error) {
                        $scope.is_loading = false;  
                        Dialog.alert($translate.instant("Error", "attendance"), error.message, $translate.instant("OK", "attendance") , -1);
                    });

                $scope.leave_deatil = leave_deatil; console.log($scope.leave_deatil);
                $scope.LeaveDetailsModal = modal;
                $scope.LeaveDetailsModal.show();
            });
        };


      /**
         * cancel details
         */
        $scope.cancelLeave = function(id) { 
            Loader.show(); 
            Attendance
                .cancelLeaveById(id)
                .then(function (data) {
                    $scope.all_leaves = data.collections;  
                    Loader.hide(); 
                }, function (error) {
                    Loader.hide(); 
                    Dialog.alert($translate.instant("Error", "attendance"), error.message, $translate.instant("OK", "attendance") , -1);
                });            
        };

        /**
         * close details modal 
         */
        $scope.closeLeaveDetails = function (){
             $scope.LeaveDetailsModal.remove();
        }

       /**
         * Apply leave details
         */
        $scope.applyLeave = function() {
            $ionicModal.fromTemplateUrl('features/attendance/assets/templates/l1/modal/apply_leave.html', {
                scope: $scope,
                animation: 'slide-in-right-left'
            }).then(function(modal) {        
                $scope.apply_post = { remaining_leave_value : 0 }; 
               // $scope.apply_post.remaining_leave_value = 0;
                $scope.applyLeaveModal = modal;
                $scope.applyLeaveModal.show();
            });
        };

         /**
         * close details modal 
         */
        $scope.closeApplyLeaveDetails = function (){
             $scope.applyLeaveModal.remove();
        }
        
        /**
         * Attendance History
         */
        $scope.attendanceHistory = function() {
            $scope.is_loading = true;
            var today = new Date();
            $scope.current_year = today.getFullYear();
            $scope.attendance_history = {
                month: today.getMonth(),
                year : today.getFullYear(),
                history: {}
            }

            $ionicModal.fromTemplateUrl('features/attendance/assets/templates/l1/modal/history.html', {
                scope: $scope,
                animation: 'slide-in-right-left'
            }).then(function(modal) {    
                
                Attendance
                   .attendanceHistory($scope.attendance_history.month, $scope.attendance_history.year)
                    .then(function (data) {
                        $scope.is_loading = false;
                        $scope.attendance_history.history = data.collection;
                        $scope.total_month_time = data.total_month_time;

                    }, function (error) {
                        $scope.is_loading = false;
                        Dialog.alert($translate.instant("Error", "attendance"), error.message, $translate.instant("OK", "attendance") , -1);
                    })
                    .then(function () { // Finally!
                       $scope.is_loading = false;;
                });

                $scope.attendanceHistoryModal = modal;
                $scope.attendanceHistoryModal.show();
            });
        };

         /**
         * close details modal 
         */
        $scope.closeAttendanceHistoryModal = function (){
             $scope.attendanceHistoryModal.remove();
        }

         /**
         * close details modal 
         */
        $scope.dialogMessages = function (message){
             Dialog.alert($translate.instant("Note", "attendance"), message, $translate.instant("OK", "attendance"));
        }
 
         /**
         * Attendance History
         */
        $scope.attendanceHistoryFilter = function() {
           $ionicModal.fromTemplateUrl('features/attendance/assets/templates/l1/modal/history_filter.html', {
                scope: $scope,
                animation: 'slide-in-right-left'
            }).then(function(modal) {   
                $scope.attendanceHistoryFilterModal = modal;
                $scope.attendanceHistoryFilterModal.show();
            });
        };

        $scope.attendanceHistoryFilterSubmit = function() {
               Loader.show();
               Attendance
                   .attendanceHistory($scope.attendance_history.month, $scope.attendance_history.year)
                    .then(function (data) {
                        Loader.hide();
                        $scope.attendance_history.history = data.collection;
                        $scope.total_month_time = data.total_month_time;
                       $scope.closeAttendanceHistoryFilterModal();
                    }, function (error) {
                        Loader.hide();
                        Dialog.alert($translate.instant("Error", "attendance"), error.message, $translate.instant("OK", "attendance") , -1);
                    })
                    .then(function () { // Finally!
                      Loader.hide();
                });
        }

         /**
         * close details modal 
         */
        $scope.closeAttendanceHistoryFilterModal = function (){
             $scope.attendanceHistoryFilterModal.remove();
        }
        
        //Timer stop function.
        $scope.$on("$ionicView.leave", function(event, data){  
           //Cancel the Timer.
            if (angular.isDefined($scope.timeinterval)) {
                $interval.cancel($scope.timeinterval);
            }
        });

        /*Called beforeEnter for get Location*/
        $scope.$on("$ionicView.beforeEnter", function(event, data) {  
           $scope.getLocation();
        });
    

    /**
    * Save Start Content 
    */
    $scope.StartFunctionTracking = function() {
        $scope.postValues.startdate =  new Date();
        Loader.show();
        Attendance
            .saveStartContent($scope.postValues)
            .then(function (data) {
                if(data.is_checkout_enable == 1){
                    $scope.tracking_id = data.tracking_id;
                    $scope.is_checked_in = true;
                    $scope.payout_data.timetracking = data.timetracking;
                    initializeClock($scope.postValues.startdate);
                }else{
                    Dialog.alert($translate.instant("Success", "attendance"), $translate.instant("Check In successfully!", "attendance"), $translate.instant("OK", "attendance") , -1);
                }
            }, function (error) {
                Loader.hide();
                Dialog.alert($translate.instant("Error", "attendance"), error.message, $translate.instant("OK", "attendance") , -1);
            })
            .then(function () { // Finally!
               Loader.hide();
            });
    }
 
    /**
    * Save Start Content 
    */
    $scope.StartTracking = function() {
        if($scope.payout_data.settings.project_tracking_enable && $scope.payout_data.projects.length > 0) {
            $ionicModal.fromTemplateUrl('features/attendance/assets/templates/l1/modal/projects.html', {
                scope: $scope,
                animation: 'slide-in-right-left'
            }).then(function(modal) {   
                $scope.projectsModal = modal;
                $scope.projectsModal.show();
            });
        }else{
            $scope.postValues.project_id =  0;
            $scope.postValues.location_id = 0;
            $scope.StartFunctionTracking();    
        }
    }

   /**
     * close project modal 
     */
    $scope.closeProjectsModal = function (){
         $scope.projectsModal.remove();
    }

   /**
    * Save End Content 
    */
    $scope.projectTrackingTracking = function(project_id) {
        $scope.postValues.project_id =  project_id;
        $scope.postValues.location_id = 0;
        $ionicPopup.show({
                title: $translate.instant('Confirm', "attendance"),
                template: $translate.instant('Are you sure want to check In?', "attendance"),
                cssClass: 'project-list',
                scope: $scope,
                buttons: [{
                    text: $translate.instant('Cancel', "attendance"),
                    type: 'button-default',
                    onTap: function (e) {
                        return false;
                    }
                }, {
                    text: $translate.instant('OK', "attendance"),
                    type: 'button-positive',
                    onTap: function (e) {
                        return true;
                    }
                }]
            }).then(function (result) {
                if (result) {
                    $scope.StartFunctionTracking();
                    $scope.closeProjectsModal();
                }
            });
    }

   $scope.showScanCamera = function () {
            if (!Application.is_webview) {

                $cordovaBarcodeScanner.scan().then(function (barcodeData) {                   
                    if (barcodeData.text !== '') {
                        $timeout(function () {
                            var qrCode = barcodeData.text.replace('sendback:', ''); 
                            
                            if(qrCode > 0){
                                $scope.postValues.location_id = qrCode;
                                
                                //verify scan
                                Loader.show();
                                Attendance
                                    .verifyScan($scope.postValues.location_id)
                                    .then(function (data) {
                                        if(data.is_valid == 1){
                                            // Next steps wiht modal windows
                                            $ionicModal.fromTemplateUrl('features/attendance/assets/templates/l1/modal/note.html', {
                                                    scope: $scope,
                                                    animation: 'slide-in-right-left'
                                            }).then(function(modal) {   
                                                $scope.scanCheckInModal = modal;
                                                $scope.scanCheckInModal.show();
                                            });
                                      
                                        }else{
                                            Dialog.alert($translate.instant("Error", "attendance"), data.message , $translate.instant("OK", "attendance") , -1);
                                        }
                                    }, function (error) {
                                        Loader.hide();
                                        Dialog.alert($translate.instant("Error", "attendance"), error.message, $translate.instant("OK", "attendance") , -1);
                                    })
                                    .then(function () { // Finally!
                                        Loader.hide();
                                    });
                            }else{
                                 Dialog.alert($translate.instant("Error", "attendance"), $translate.instant('Invalid code.', "attendance") , $translate.instant('OK', "attendance"), -1);
                            }                            
                        });

                    }else{
                        Dialog.alert($translate.instant("Error", "attendance") , $translate.instant("Unreadable QRCode, sorry", "attendance"), $translate.instant('OK', "attendance"), -1, "attendance");
                    }
                    
                }, function (error) {
                    Dialog.alert($translate.instant("Error", "attendance"), 'An error occurred while reading the code.', $translate.instant('OK', "attendance"), -1);
                });

             } else {
                Dialog.alert($translate.instant("Info", "attendance") , $translate.instant("This will open the code scan camera on your device", "attendance"), $translate.instant("Ok", "attendance"), -1);
            }
        };
   
   /**
     * close scan modal 
     */
    $scope.closeScanCheckInModal = function (){
         $scope.scanCheckInModal.remove();
    }

    /**
    * Save scan check in 
    */
    $scope.scanCheckInSubmit = function() {
        $scope.postValues.project_id =  0;
        $scope.StartFunctionTracking();
        $scope.closeScanCheckInModal();
    }



    /**
    * Save End Content 
    */
    $scope.StopTracking = function() {
         
        $scope.postValues.enddate = new Date();
        $scope.postValues.tracking_id = $scope.tracking_id;
        $scope.postValues.tracking_time =  $scope.checkedClock.hours+':'+$scope.checkedClock.minutes+':'+$scope.checkedClock.seconds;

        Loader.show();
        Attendance
            .saveEndContent($scope.postValues)
            .then(function (data) {
                $scope.is_checked_in = false;
                //Cancel timer clock.
                if (angular.isDefined($scope.timeinterval)) {
                    $interval.cancel($scope.timeinterval);
                }
                $timeout(function () {
                    $scope.currentTime(); // When not checked in
                    $scope.checkedClock.hours = '00';
                    $scope.checkedClock.minutes = '00';
                    $scope.checkedClock.seconds = '00';
                    Loader.hide(); 
                }, 300);   
               
            }, function (error) {
                Loader.hide();
                Dialog.alert($translate.instant("Error", "attendance"), error.message, $translate.instant("OK", "attendance") , -1);
            })
            .then(function () { // Finally!
               Loader.hide();
            });
    }


     /**
    * Save End Content 
    */
    $scope.leaveSubmit = function() {
        Loader.show();
        Attendance
            .leaveSubmit($scope.apply_post)
            .then(function (data) {
                Dialog.alert($translate.instant("Success", "attendance"), data.message, $translate.instant("OK", "attendance") , -1);
                $scope.closeApplyLeaveDetails();
                Loader.hide();                 
            }, function (error) {
                Loader.hide();
                Dialog.alert($translate.instant("Error", "attendance"), error.message, $translate.instant("OK", "attendance") , -1);
            })
            .then(function () { // Finally!
               Loader.hide();
         });
   }


   $scope.selectLeaveType = function() {
       var leavetype_value = $filter('filter')($scope.payout_data.leavetype, {id: $scope.apply_post.leavetype });
       $scope.apply_post.remaining_leave_value = leavetype_value[0].remaining;
       $scope.remaining_message_warning = $translate.instant("Remaining", "attendance")+' '+leavetype_value[0].name+ ' is '+ leavetype_value[0].remaining;
   }
 
    $scope.range = function(min, max, step){
      step = step || 1;
      var input = [];
      for (var i = min; i <= max; i += step) input.push(i);
      return input;
    };  





}).controller('AttendanceMessageController', function($controller, Attendance, $state, $ionicScrollDelegate, $ionicActionSheet, $timeout, $ionicSlideBoxDelegate, $stateParams, $scope, $rootScope, $translate, Customer, Loader, Dialog, $filter, $ionicModal) {
    $scope.value_id = Attendance.value_id = $stateParams.value_id;
    $scope.is_logged_in = Customer.isLoggedIn();
    $scope.customer_id = Customer.id;
    $scope.is_loading = false;
    $scope.messages = [];
    $scope.sentmessages = [];
   
   /**
     *   date convert
     */
    $scope.convertMessageDate = function (date) {
          return $filter("moment_calendar")( (new Date(date)).getTime());
    };
 
   /**
     *   scroll bottom 
     */
    $scope._scrollBottom = function() {    
      $timeout(function() {
          $ionicScrollDelegate.$getByHandle('messageScroll').scrollBottom(true);
      }, 500);    
    }; 


     $scope.sendMessage = function(sentmessages){
        
        if(!Customer.isLoggedIn()) {
            return Customer.loginModal($scope);
        } 
        if(sentmessages.message == ''){
           Dialog.alert("Validation Error", "Please enter a messages!", "OK", -1, "attendance");
        }else{

          $scope.sending_message = true;
          Attendance.saveMessage(sentmessages).success(function (data) {
            $scope.messages = data.messages;
            $scope._scrollBottom();
            $scope.sentmessages.message = '';
            $scope.sending_message = false;
           }).error(function (error) {
                Dialog.alert($translate.instant("Error", "attendance") ,error.message , "OK", -1, "attendance");
               $scope.sending_message = false;
         });
       }       
    }

   /**
     * Translate html text
     */
    $scope.forTranslate = function (text) {
        return $translate.instant(text, "attendance");
    };


    $scope.loadContent = function(){
        $scope.is_loading = true;
        Attendance.customerMessages().success(function (data) {
            $scope.messages = data.messages;
            $scope._scrollBottom();
            $scope.sentmessages.message = '';
            $scope.sending_message = false;
            $scope.is_loading = false;
        }).error(function (error) {
              Dialog.alert($translate.instant("Error", "attendance") ,error.message , "OK", -1, "attendance");
              $scope.sending_message = false;
              $scope.is_loading = false;
         });
    }

    $scope.loadContent();
});
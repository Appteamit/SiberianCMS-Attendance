<?php

class Attendance_Form_Attendance extends Siberian_Form_Abstract
{
    
    public function init() {
        parent::init();
        
        $this
            ->setAction(__path("/attendance/application/editpost"))
            ->setAttrib("id", "form-add-attendance");
        
        self::addClass("create", $this); 

        $this->addSimpleHidden("id");
        $value_id = $this->addSimpleHidden("value_id");
        $value_id->setRequired(true);

        $timezone_identifiers = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

            $this->addSimpleSelect(
            "timezone",
            p__("attendance", "Timezone"),
            $timezone_identifiers
             )->setRequired(true);

        $this->addSimpleSelect(
            "date_format",
            p__("attendance", "Date Format"),
            [ 
                "d-m-Y" =>  'DD-MM-YYYY',
                "m-d-Y" =>  'MM-DD-YYYY',
            ])->setRequired(true);

        $this->addSimpleSelect(
            "time_format",
            p__("attendance", "Time Format"),
            [ 
                "g:i A" =>  '12 Hour',
                "H:i" =>  '24 Hour',
            ])->setRequired(true);
 
        $this->addSimpleCheckbox('holiday_enable', p__('attendance', 'Holiday Enable?'));
        

        /*Attendance Settings*/
        $this->addSimpleHtml("attendance_settings", "<div class=\"alert alert-info\">" . p__('attendance', "Attendance Settings") . "</div>" );
        $this->addSimpleCheckbox('check_in_out_enable', p__('attendance', 'Manual Check In/Out Enable'));

        $this->addSimpleSelect(
            "is_checkout_enable",
            p__("attendance", "Checkout Required?"),
            [ 
                "1" =>  p__("attendance", "Yes"),
                "0" =>  p__("attendance", "No")                
            ])->setRequired(true);

        $this->addSimpleCheckbox('check_in_history_enable', p__('attendance', 'Check In/Out History Display?'));

        $this->addSimpleCheckbox('qr_scan_enable', p__('attendance', 'QR Scan Check In/Out Enable?'));

        $this->addSimpleCheckbox('project_tracking_enable', p__('attendance', 'Project Tracking Check In/Out Enable?'));

        $this->addSimpleCheckbox('fixed_working_hours', p__('attendance', 'Fixed Working Hours'));

        $this->addSimpleHtml("fixed_working_warring",  p__('attendance', "Note : If Fixed working hours then  Min working, Start, End business time and Late entry time must be required!") );

        $this->addSimpleSlider('min_working_hours', p__('attendance', "Min working hours"), array('min' => 1, 'max' => 24, 'step' => 1, 'unit' => p__('attendance', " Hours") ), true);
        $this->addSimpleDatetimepicker('business_hour_start',p__('attendance', 'Start business time'), false, Siberian_Form_Abstract::TIMEPICKER);
        $this->addSimpleDatetimepicker('business_hour_end',p__('attendance', 'End business time'), false, Siberian_Form_Abstract::TIMEPICKER);
     
        $this->addSimpleSlider('auto_checkout_time', p__('attendance', "Auto checked out after ?"), array('min' => 1, 'max' => 24, 'step' => 1, 'unit' => p__('attendance', " Hours") ), true);

        $qr_checkin_note = $this->addSimpleTextarea('qr_checkin_note', p__('attendance','QR check-In Message'));
        $qr_checkin_note->setRichtext();
 

        /*Leave Settings*/
        $this->addSimpleHtml("leave_settings", "<div class=\"alert alert-info\">" . p__('attendance', "Leave Settings") . "</div>" );
        $this->addSimpleSelect(
            "financial_year",
            p__("attendance", "Financial Year Start"),
            [ 
                "1" => p__("attendance", "January"),
                "2" => p__("attendance", "February"),
                "3" => p__("attendance", "March"),
                "4" => p__("attendance", "April"),
                "5" => p__("attendance", "May"),
                "6" => p__("attendance", "June"),
                "7" => p__("attendance", "July"),
                "8" => p__("attendance", "August"),
                "9" => p__("attendance", "September"),
                "10" => p__("attendance", "October"),
                "11" => p__("attendance", "November"),
                "12" => p__("attendance", "December"),
            ])->setRequired(true);

        $this->addSimpleCheckbox('leave_enable', p__('attendance', 'Leave system Enable'));
        $this->addSimpleCheckbox('leave_apply_enable', p__('attendance', 'Leave Apply Enable'));
        $this->addSimpleText('admin_email', p__('attendance', 'Admin Email (For Notification)'))->setRequired(true);

        $this->addSimpleCheckbox('is_message_admin', p__('attendance', 'Message (to Admin)'));
      
        /*Designs Settings*/
        $this->addSimpleHtml("designs_settings", "<div class=\"alert alert-info\">" . p__('attendance', "Designs Settings") . "</div>" );

        $this->addSimpleHtml("designs_warring",  p__('attendance', "Note : Few Warring Message, Buttons and Counter text color manage from COLORS -> ICONS setting."));

        $this->addCssScript();
}
    
    public function setElementValueById($id, $value, $required = false) {
        $element = $this->getElement($id)->setValue($value);
        if( $required ) {
            $element->setRequired(true);
        }
    }


        /**
     *
     */
    public function addColorPicker($name, $title, $value) {

        $color_html = '<div class="row">
                        <div class="col-md-6 layout-colorpicker">
                            <div class="colorlabel"><b>'.$title.': </b></div>
                            <div class="colorpicker-block">
                                <div class="colorpicker-square"></div>
                                <input type="text" class="colorpicker-input input-flat" name="' . $name . '" id="android_push_color" value="' . $value . '" />
                            </div>
                        </div>
                    </div>';

        $this->addSimpleHtml($name, $color_html);
        return $this;
    }

    public function addCssScript() {
          $this->addSimpleHtml("css", '
                            <div class="clear" style="clear: both;"></div>
                            <style type="text/css">
                                .layout-colorpicker {
                                    margin-left: 20px;
                                }

                                .layout-colorpicker .colorpicker-square {
                                    width: 32px;
                                    height: 32px;
                                    display: block;
                                    float: left;
                                    box-shadow: 1px 1px 1px #d2d2d2;
                                    border-radius: 2px;
                                    margin-right: 10px;
                                    margin-top: 1px;
                                }

                                .layout-colorpicker .colorpicker-input {
                                    float: left;
                                    width: 70%;
                                }
                            </style>

                            <script type="text/javascript">
                                $(".layout-colorpicker .colorpicker-square, .layout-colorpicker .colorpicker-input").each(function() {
                                    var el = $(this);
                                    var parent = el.parent(".colorpicker-block");
                                    var mainColor = parent.find("input");
                                    var squareColor = parent.find("div");
                                    el.ColorPicker({
                                        color: mainColor.val(),
                                        onChange: function(hsb, hex, rgb) {
                                            mainColor.val("#" + hex);
                                            squareColor.css("backgroundColor", "#"+hex);
                                        }
                                    });

                                });

                                $(".layout-colorpicker .colorpicker-input").on("change blur", function() {
                                    $(this).ColorPickerSetColor($(this).val());
                                });

                                setTimeout(function() {
                                    $(".layout-colorpicker .colorpicker-input").trigger("change");
                                }, 10);
                            </script>
                    ');
        return $this;
    }

   
}
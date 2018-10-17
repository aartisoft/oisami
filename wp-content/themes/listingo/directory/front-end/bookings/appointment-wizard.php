<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
global $current_user;

//Get user appointment key data meta
$apointment_meta = '';
$author_id = '';
$time_slot = '';
$slot_date = '';
$apointment_meta = get_user_meta($current_user->ID, 'appointment_data', true);

if (!empty($apointment_meta)) {
    $apointment_data = explode("|", $apointment_meta);
    $author_id = !empty($apointment_data[0]) ? $apointment_data[0] : '';
    $time_slot = !empty($apointment_data[1]) ? $apointment_data[1] : '';
    $slot_date = !empty($apointment_data[2]) ? $apointment_data[2] : '';
}

$time_format = get_option('time_format');
$time = explode('-', $time_slot);
$format_time = date_i18n($time_format, strtotime('01-01-2016 ' . $time[0])).'&nbsp;-&nbsp;'.date_i18n($time_format, strtotime('01-01-2016 ' . $time[1]));

$change_format = date_i18n('j \d\e F \d\e Y', strtotime($slot_date));
$dob_format    = listingo_get_dob_format($slot_date, 'return');

$formatted_date = sprintf('%s %s%s%s %s %s', $change_format, '(',$dob_format,')', esc_html__('At', 'listingo'), $format_time);

//check if already booked
$is_available	= listingo_check_booked_slot($time_slot,$slot_date,$author_id);
?>
<div class="tg-appointmentsetting">
  <fieldset class="booking-model-contents">
    <?php if( isset( $is_available['available'] ) && $is_available['available'] === 'yes' ){?>
      <div class="tg-appointmenthead">
        <div class="tg-appointmentheading">
          <h2><?php echo esc_attr($formatted_date); ?></h2>
        </div>
      </div>

      <div class="tg-progressbox">
        <ul class="tg-formprogressbar tg-navdocappointment" role="tablist">
          <li class="tg-active"><a href="javascript:;" class="bk-step-1"><?php esc_html_e('Instructions', 'listingo'); ?></a></li>
          <li><a href="javascript:;" class="bk-step-2"><?php esc_html_e('Your Detail', 'listingo'); ?></a></li>
          <li><a href="javascript:;" class="bk-step-3"><?php esc_html_e('Code Authentication', 'listingo'); ?></a></li>
          <li><a href="javascript:;" class="bk-step-4"><?php esc_html_e('All Done', 'listingo'); ?></a></li>
        </ul>

        <div class="tab-content tg-appointmenttabcontent" data-id="<?php echo intval($author_id); ?>">
          <div class="tab-pane active step-one-contents" id="one">
            <?php listingo_get_appointment_step_one($author_id, 'echo'); ?>
          </div>

          <div class="tab-pane step-two-contents" id="two"></div>
          <div class="tab-pane step-three-contents" id="three"></div>
          <div class="tab-pane step-four-contents" id="four"></div>
          <div class="tg-btnarea">
            <button type="button" class="tg-btn bk-step-prev"><?php esc_html_e('Previous', 'listingo'); ?></button>
            <a href="javascript:;" class="tg-btn tg-btnnext bk-step-next"><?php esc_html_e('I Understand!', 'listingo'); ?></a>
            <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>" class="tg-btndontwant go-back-author"><?php esc_html_e('No I Don\'t Want', 'listingo'); ?></a>
          </div>
        </div>
      </div>

    <?php } else{?>
      <div class="tg-dashboardappointmentbox">
        <?php Listingo_Prepare_Notification::listingo_info(esc_html__('Information', 'listingo'), esc_html__('Sorry! This slot is not available for booking.', 'listingo')); ?>
			</div>
    <?php }?>
  </fieldset>
</div>
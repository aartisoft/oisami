<?php
/**
 *
 * The template part for displaying the dashboard appointment settings.
 *
 * @package   Listingo
 * @author    Themographics
 * @link      http://themographics.com/
 * @since 1.0
 */
get_header();

$days = listingo_prepare_business_hours_settings();
?>
<div id="tg-content" class="tg-content">
  <div class="tg-dashboard tg-dashboardappointmentsetting">
    <form class="tg-themeform tg-appointment-settings-form">
      <fieldset>
        <div class="tg-dashboardbox">
          <div class="tg-dashboardtitle">
            <h2><?php esc_html_e('Appointment Settings', 'listingo'); ?></h2>
          </div>

          <div class="tg-dashboardappointmentsettingbox">
            <?php
              if (!empty($days) && is_array($days)) {
                foreach ($days as $key => $day) {
            ?>
              <div class="tg-row sp-daytimeslot" data-day="<?php echo esc_attr($key); ?>">
                <div class="tg-col">
                  <div class="tg-daybox">
                    <h3><?php echo ucwords($day); ?></h3>
                    <span><a class="tg-btntext add-default-slots" href="javascript:;"><?php esc_html_e('Add Slots', 'listingo'); ?></a></span> 
                  </div>
                </div>

                <div class="tg-col">
                  <div class="tg-slots">
                    <div class="timeslots-data-area">
                      <?php listingo_get_default_slots($key, 'echo'); ?>
                    </div>
                    <div class="timeslots-form-area"></div>
                  </div>
                </div>
              </div>

            <?php }} ?>
          </div>
        </div>
      </fieldset>
    </form>
  </div>
</div>
<script type="text/template" id="tmpl-load-slots">
    <div class="tg-slots default-slot-wrap">
    <div class="tg-paddingminus">
    <!--div class="form-group">
    <div class="tg-inpuicon">
    <input type="text" name="slot_title" class="form-control" name="title" placeholder="<?php esc_attr_e('Title (Optional)', 'listingo'); ?>">
    </div>
    </div-->

    <div class="form-group"> 
    <span class="tg-select">
    <select name="start_time" class="start_time">
    <option value=""><?php esc_attr_e('Start Time', 'listingo'); ?></option>
    <option value="0000">00:00</option>
    <option value="0100">01:00</option>
    <option value="0200">02:00</option>
    <option value="0300">03:00</option>
    <option value="0400">04:00</option>
    <option value="0500">05:00</option>
    <option value="0600">06:00</option>
    <option value="0700">07:00</option>
    <option value="0800">08:00</option>
    <option value="0900">09:00</option>
    <option value="1000">10:00</option>
    <option value="1100">11:00</option>
    <option value="1200">12:00</option>
    <option value="1300">13:00</option>
    <option value="1400">14:00</option>
    <option value="1500">15:00</option>
    <option value="1600">16:00</option>
    <option value="1700">17:00</option>
    <option value="1800">18:00</option>
    <option value="1900">19:00</option>
    <option value="2000">20:00</option>
    <option value="2100">21:00</option>
    <option value="2200">22:00</option>
    <option value="2300">23:00</option>
    <option value="2400">24:00</option>
    </select>
    </span> 
    </div>

    <div class="form-group"> 
    <span class="tg-select">
    <select name="end_time" class="end_time">
    <option value=""><?php esc_attr_e('End Time', 'listingo'); ?></option>
    <option value="0000">00:00</option>
    <option value="0100">01:00</option>
    <option value="0200">02:00</option>
    <option value="0300">03:00</option>
    <option value="0400">04:00</option>
    <option value="0500">05:00</option>
    <option value="0600">06:00</option>
    <option value="0700">07:00</option>
    <option value="0800">08:00</option>
    <option value="0900">09:00</option>
    <option value="1000">10:00</option>
    <option value="1100">11:00</option>
    <option value="1200">12:00</option>
    <option value="1300">13:00</option>
    <option value="1400">14:00</option>
    <option value="1500">15:00</option>
    <option value="1600">16:00</option>
    <option value="1700">17:00</option>
    <option value="1800">18:00</option>
    <option value="1900">19:00</option>
    <option value="2000">20:00</option>
    <option value="2100">21:00</option>
    <option value="2200">22:00</option>
    <option value="2300">23:00</option>
    <option value="2400">24:00</option>
    </select>
    </span> 
    </div>

    <div class="form-group"> 
    <span class="tg-select">
    <select name="meeting_time" class="meeting_time">
    <option value=""><?php esc_attr_e('Meeting Time', 'listingo'); ?></option>
    <option value="5">5 <?php esc_attr_e('minutes', 'listingo'); ?></option>
    <option value="10">10 <?php esc_attr_e('minutes', 'listingo'); ?></option>
    <option value="15">15 <?php esc_attr_e('minutes', 'listingo'); ?></option>
    <option value="20">20 <?php esc_attr_e('minutes', 'listingo'); ?></option>
    <option value="25">25 <?php esc_attr_e('minutes', 'listingo'); ?></option>
    <option value="30">30 <?php esc_attr_e('minutes', 'listingo'); ?></option>
    <option value="35">35 <?php esc_attr_e('minutes', 'listingo'); ?></option>
    <option value="40">40 <?php esc_attr_e('minutes', 'listingo'); ?></option>
    <option value="45">45 <?php esc_attr_e('minutes', 'listingo'); ?></option>
    <option value="50">50 <?php esc_attr_e('minutes', 'listingo'); ?></option>
    <option value="55">55 <?php esc_attr_e('minutes', 'listingo'); ?></option>
    <option value="60">1 <?php esc_attr_e('hours', 'listingo'); ?></option>
    <option value="90">1 <?php esc_attr_e('hour', 'listingo'); ?>, 30 <?php esc_attr_e('minutes', 'listingo'); ?></option>
    <option value="120">2 <?php esc_attr_e('hours', 'listingo'); ?></option>
    <option value="180">3 <?php esc_attr_e('hours', 'listingo'); ?></option>
    <option value="240">4 <?php esc_attr_e('hours', 'listingo'); ?></option>
    <option value="300">5 <?php esc_attr_e('hours', 'listingo'); ?></option>
    <option value="360">6 <?php esc_attr_e('hours', 'listingo'); ?></option>
    <option value="420">7 <?php esc_attr_e('hours', 'listingo'); ?></option>
    <option value="480">8 <?php esc_attr_e('hours', 'listingo'); ?></option>
    </select>
    </span> 
    </div>

    <!--div class="form-group"> 
    <span class="tg-select">
    <select name="padding_time" class="padding_time">
    <option value=""><?php esc_attr_e('Padding/Break Time', 'listingo'); ?></option>
    <option value="5">5 <?php esc_attr_e('minutes', 'listingo'); ?></option>
    <option value="10">10 <?php esc_attr_e('minutes', 'listingo'); ?></option>
    <option value="15">15 <?php esc_attr_e('minutes', 'listingo'); ?></option>
    <option value="20">20 <?php esc_attr_e('minutes', 'listingo'); ?></option>
    <option value="30">30 <?php esc_attr_e('minutes', 'listingo'); ?></option>
    <option value="45">45 <?php esc_attr_e('minutes', 'listingo'); ?></option>
    <option value="60">1 <?php esc_attr_e('hours', 'listingo'); ?></option>
    <option value="90">1 <?php esc_attr_e('hour', 'listingo'); ?>, 30 <?php esc_attr_e('minutes', 'listingo'); ?></option>
    <option value="120">2 <?php esc_attr_e('hours', 'listingo'); ?></option>
    </select>
    </span> 
    </div-->

    <div class="form-group"> 
    <span class="tg-select">
    <select name="count" class="count">	
    <option value="1" selected="">1 <?php esc_attr_e('time slots', 'listingo'); ?></option>
    <option value="2">2 <?php esc_attr_e('time slots', 'listingo'); ?></option>
    <option value="3">3 <?php esc_attr_e('time slots', 'listingo'); ?></option>
    <option value="4">4 <?php esc_attr_e('time slots', 'listingo'); ?></option>
    <option value="5">5 <?php esc_attr_e('time slots', 'listingo'); ?></option>
    <option value="6">6 <?php esc_attr_e('time slots', 'listingo'); ?></option>
    <option value="7">7 <?php esc_attr_e('time slots', 'listingo'); ?></option>
    <option value="8">8 <?php esc_attr_e('time slots', 'listingo'); ?></option>
    <option value="9">9 <?php esc_attr_e('time slots', 'listingo'); ?></option>
    <option value="10">10 <?php esc_attr_e('time slots', 'listingo'); ?></option>
    <option value="11">11 <?php esc_attr_e('time slots', 'listingo'); ?></option>
    <option value="12">12 <?php esc_attr_e('time slots', 'listingo'); ?></option>
    <option value="13">13 <?php esc_attr_e('time slots', 'listingo'); ?></option>
    <option value="14">14 <?php esc_attr_e('time slots', 'listingo'); ?></option>
    <option value="15">15 <?php esc_attr_e('time slots', 'listingo'); ?></option>
    <option value="16">16 <?php esc_attr_e('time slots', 'listingo'); ?></option>
    <option value="17">17 <?php esc_attr_e('time slots', 'listingo'); ?></option>
    <option value="18">18 <?php esc_attr_e('time slots', 'listingo'); ?></option>
    <option value="19">19 <?php esc_attr_e('time slots', 'listingo'); ?></option>
    <option value="20">20 <?php esc_attr_e('time slots', 'listingo'); ?></option>
    </select>
    </span> 
    </div>
    
    <div class="form-group tg-btnbox">
    <button type="submit" class="tg-btn save-time-slots"><?php esc_html_e('Save', 'listingo'); ?></button>
    <button type="submit" class="tg-btn remove-slots-form"><?php esc_html_e('Cancel', 'listingo'); ?></button>
    </div>
    </div>
    </div>
</script>
<?php get_footer(); ?>
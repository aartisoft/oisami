<?php
/**
 *
 * The template part for displaying the dashboard.
 *
 * @package   Listingo
 * @author    Themographics
 * @link      http://themographics.com/
 * @since 1.0
 */
get_header();
global $current_user,$woocommerce;
$company_profile = fw_get_db_settings_option('company_profile');
$com_title = fw_get_db_settings_option('com_title');
$com_description = fw_get_db_settings_option('com_description');
$com_person_desg = fw_get_db_settings_option('com_person_desg');
$com_person_image = fw_get_db_settings_option('com_person_image');
$com_logo = fw_get_db_settings_option('com_logo');
$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
$package_id	= listingo_get_subscription_meta('subscription_id',$current_user->ID);
$package_title	= !empty( $package_id ) ? get_the_title($package_id) : esc_html__('No package purchased or package expired.','listingo');
$m_foto = 'http://www.edev.net.br/oisamicom/wp-content/uploads/2018/10/doctor.jpg';
$e_foto = 'http://www.edev.net.br/oisamicom/wp-content/uploads/2018/10/nurse.jpg';
$m_nome = $e_nome = $m_url = '';
$medico_id = get_user_meta( $current_user->ID, VM50_SAMI_META_MEDICO_FAMILIA, true );

if ( $medico_id !='' ) {
    $dados_usr = get_userdata( $medico_id );
    $m_foto    = get_avatar_url( $medico_id, array( 'default' => 'http://www.edev.net.br/oisamicom/wp-content/uploads/2018/10/doctor.jpg') );
    $m_nome    = $dados_usr->first_name . ' ' . $dados_usr->last_name;
    $m_url     = site_url('/consultas/') . $dados_usr->user_nicename;
    $enferm_id = get_user_meta( $medico_id, VM50_SAMI_META_ENFERMEIRO, true );

    if ( $enferm_id !='' ) {
        $dados_usr = get_userdata( $enferm_id );
        $e_foto    = get_avatar_url( $enferm_id, array( 'default' => 'http://www.edev.net.br/oisamicom/wp-content/uploads/2018/10/nurse.jpg') );
        $e_nome    = $dados_usr->first_name . ' ' . $dados_usr->last_name;
        $e_url     = site_url('/consultas/') . $dados_usr->user_nicename;
        $enferm_id = get_user_meta( $medico_id, VM50_SAMI_META_ENFERMEIRO, true );
    }
}
?>
<div id="tg-content" class="tg-content">
	<div class="tg-dashboard">
		<?php if (is_active_sidebar('user-dashboard-top')) {?>
		<div class="tg-banneradd">
			<figure>
				<?php dynamic_sidebar('user-dashboard-top'); ?>
			</figure>
		</div>
		<?php }?>

		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding-left: 0px;">
      <div class="tg-serviceprovider">
        <figure class="tg-featuredimg" style="width: 30%; float: left; margin-bottom: 0px;">
          <a href="<?php echo $m_url; ?>">
            <img class="searched-avatar" src="<?php echo $m_foto; ?>" alt="Listingo">
          </a>
   			</figure>

        <div class="tg-companycontent" style="width: 65%; float: left; margin-bottom: 0px; margin-left: 5%; padding: 15px 0px;">
          <div class="tg-title">
            <h3><a href="<?php echo $m_url; ?>"><?php echo $m_nome; ?></a></h3>
          </div>
          <a class="tg-btn" href="<?php echo $m_url; ?>">Agendar Consulta</a>
        </div>
      </div>
    </div>


    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding-right: 0px;">
      <div class="tg-serviceprovider">
        <figure class="tg-featuredimg" style="width: 30%; float: left; margin-bottom: 0px;">
          <a href="<?php echo $e_url; ?>">
            <img class="searched-avatar" src="<?php echo $e_foto; ?>" alt="Listingo">
          </a>
        </figure>

        <div class="tg-companycontent" style="width: 65%; float: left; margin-bottom: 0px; margin-left: 5%; padding: 42px 0px;">
          <div class="tg-title">
            <h3><a href="<?php echo $e_url; ?>"><?php echo $e_nome; ?></a></h3>
          </div>

          <!--a class="tg-btn" href="#">Agendar Consulta</a-->
        </div>
      </div>
    </div>
    
		<div class="tg-alertmessages tg-ceomessage">
			<?php if( isset( $company_profile ) && $company_profile === 'enable') {?>
			<?php if( !empty( $com_title ) && !empty( $com_description )) {?>
			<div class="alert alert-danger tg-alertmessage fade in">
				<?php if( !empty( $com_title )) {?><h2><?php echo esc_attr($com_title);?></h2><?php }?>
				<?php if( !empty( $com_description )) {?><span><?php echo esc_attr($com_description);?></span><?php }?>
				<div class="tg-ceobottom">
					<div class="tg-ceocontent">
						<?php if( !empty( $com_person_image['url'] )) {?>
							<figure>
								<img src="<?php echo esc_url($com_person_image['url']);?>" alt="<?php echo esc_attr( $blogname );?>">
							</figure>
						<?php }?>
						<?php if( !empty( $com_person_desg )) {?>
							<div class="tg-ceoinfo">
								<span><?php esc_html_e('Regards','listingo');?>,</span>
								<span><?php echo esc_attr($com_person_desg);?></span>
							</div>
						<?php }?>
					</div>
					<?php if( !empty( $com_logo['url'] )) {?>
						<strong class="tg-logo"><img src="<?php echo esc_url($com_logo['url']);?>" alt="<?php echo esc_attr( $blogname );?>"></strong>
					<?php }?>
				</div>
			</div>
			<?php }}?>
			<div class="tg-dashboardnotifications">
				<?php if ( apply_filters('listingo_get_theme_settings', 'jobs') == 'yes') {?>
					<div class="tg-dashboardnotificationholder">
						<div class="tg-dashboardnotofication tg-totaljobs">
							<i class="lnr lnr-calendar-full"></i>
							<div class="tg-dashboardinfo">
								<h3><?php esc_html_e('Total Jobs Posted','listingo');?></h3>
								<span><?php echo intval(listingo_get_total_jobs_by_user($current_user->ID)); ?>&nbsp;<?php esc_html_e('Jobs Posted','listingo');?></span>
							</div>
						</div>
					</div>
				<?php }?>
				<div class="tg-dashboardnotificationholder">
					<div class="tg-dashboardnotofication tg-currentpackege">
						<i class="lnr lnr-eye"></i>
						<div class="tg-dashboardinfo">
							<h3><?php esc_html_e('Your Current Package','listingo');?></h3>
							<span><?php echo esc_attr($package_title);?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php get_footer(); ?>
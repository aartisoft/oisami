<?php
/**
 *
 * The template part for displaying the dashboard menu
 *
 * @package   Listingo
 * @author    Themographics
 * @link      http://themographics.com/
 * @since 1.0
 */

global $current_user, $wp_roles, $userdata, $post;

$reference 		 = (isset($_GET['ref']) && $_GET['ref'] <> '') ? $_GET['ref'] : '';
$mode 			 = (isset($_GET['mode']) && $_GET['mode'] <> '') ? $_GET['mode'] : '';
$user_identity 	 = $current_user->ID;
$bk_settings	 = listingo_get_booking_settings();

$url_identity = $user_identity;
if (isset($_GET['identity']) && !empty($_GET['identity'])) {
	$url_identity = $_GET['identity'];
}

$dir_profile_page = '';
$insight_page = '';
if (function_exists('fw_get_db_settings_option')) {
	$dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
	$insight_page = fw_get_db_settings_option('insight_page', $default_value = null);
}

$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
$provider_category = listingo_get_provider_category($user_identity);

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

if( isset( $insight_page ) && $insight_page === 'enable' ){?>
	<li class="meu-time <?php echo ( $reference === 'dashboard' ? 'tg-active' : ''); ?>">
		<a href="<?php Listingo_Profile_Menu::listingo_profile_menu_link($profile_page, 'dashboard', $user_identity); ?>">
			<i class="lnr lnr-layers"></i>
			<span><?php esc_html_e('Insights', 'listingo'); ?></span>
		</a>
	</li>

  <li class="agendar">
		<a href="<?php echo $m_url; ?>">
			<i class="lnr lnr-calendar-full"></i>
			<span>Agendar Consulta</span>
		</a>
	</li>

	<li class="clientes">
		<a href="/dashboard/importacao/">
			<i class="lnr lnr-upload"></i>
			<span>Importar</span>
		</a>
	</li>

	<li class="clientes">
		<a href="/dashboard/medicos-do-cliente/">
			<i class="lnr lnr-layers"></i>
			<span>Administrar Cliente</span>
		</a>
	</li>

	<li class="pacientes">
		<a href="/dashboard/lista-usuarios/">
			<i class="lnr lnr-magnifier"></i>
			<span>Pesquisar Pacientes</span>
		</a>
	</li>

	<li class="pedidos">
		<a href="/pacotes">
			<i class="lnr lnr-plus-circle"></i>
			<span>Vouchers Adicionais</span>
			<!--?php do_action('listingo_get_tooltip','menu','menu_favorites');?-->
		</a>
	</li>

	<li class="voucher">
		<a href="/minha-conta/orders/">
			<i class="lnr lnr-tag"></i>
			<span>Saldo de Vouchers</span>
			<!--?php do_action('listingo_get_tooltip','menu','menu_favorites');?-->
		</a>
	</li>
<?php }
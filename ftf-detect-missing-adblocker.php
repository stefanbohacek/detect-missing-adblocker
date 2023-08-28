<?php
/*
    Plugin Name: Detect Missing Adblocker
    Description: Warn your website's visitors if they don't have an adblocker enabled.
    Version:     1.0.4
    Author:      Stefan Bohacek
*/

class FTF_Detect_Missing_Adblocker {
  function __construct(){
    add_action( 'init', array( $this, 'enqueue_scripts_and_styles' ) );
    add_action( 'wp_footer', array( $this, 'show_note' ) );
    add_action( 'admin_init', array( $this, 'settings_init' ) );
    add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
    add_filter('plugin_action_links_ftf-detect-missing-adblocker.php', array($this, 'settings_page_link'));
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'settings_page_link'));

    add_filter( 'plugin_action_links_ftf-detect-missing-adblocker/ftf-detect-missing-adblocker.php', array( $this, 'settings_page_link' ) );
  }

  function get_resources( $type = 'desktop' ){
    if ( $type === 'mobile' ){
      $links = array(
        array(
          'title' => 'AdBlock',
          'url' => 'https://help.getadblock.com/support/solutions/articles/6000152717-how-can-i-block-ads-on-my-mobile-device-'
        )
      );
    } elseif ( $type === 'desktop' ) {
      $links = array(
        array(
          'title' => 'uBlock Origin',
          'url' => 'https://github.com/gorhill/uBlock#installation'
        ),
        array(
          'title' => 'AdBlock',
          'url' => 'https://getadblock.com/'
        ),
        array(
          'title' => 'Ghostery',
          'url' => 'https://www.ghostery.com/'
        )
      );
    }

    return $links;
  }

  function enqueue_scripts_and_styles(){
    $js_file_path = plugin_dir_path( __FILE__ ) . 'dist/js/detect.js';
    wp_register_script( 'ftf-dma-detect-script', plugin_dir_url( __FILE__ ) . 'dist/js/detect.js', array(), filemtime( $js_file_path ));
    wp_enqueue_script( 'ftf-dma-detect-script' );

    $style = get_option( 'ftf_detect_missing_adblocker_style', 'basic' );

    if ( empty( $style ) || $style === 'basic' ){
      $css_file_path = plugin_dir_path( __FILE__ ) . 'dist/css/styles.min.css';
      wp_register_style( 'ftf-dma-styles', plugin_dir_url( __FILE__ ) . 'dist/css/styles.min.css', array(), filemtime( $css_file_path ), 'all' );
      wp_enqueue_style( 'ftf-dma-styles' );
    }
  }

  function show_note(){
    $is_mobile = wp_is_mobile();

    if ( $is_mobile ){
      $show_on_mobile = get_option( 'ftf_detect_missing_adblocker_show_on_mobile' );
      if ( !$show_on_mobile ){
        return false;
      }
    }

    $style = get_option( 'ftf_detect_missing_adblocker_style', 'basic' );
    $custom_note_header = get_option( 'ftf_detect_missing_adblocker_custom_note_header' );
    $custom_note = html_entity_decode( get_option( 'ftf_detect_missing_adblocker_custom_note' ) );

    $default_note_header = 'Adblocker not detected';

    if ( $is_mobile ){
      $default_note = '<p>Consider installing a browser extension that blocks ads and other malicious scripts in your browser to protect your privacy and security.</p>';
    } else {
      $default_note = '<p>Consider installing a browser extension that blocks ads and other malicious scripts in your browser to protect your privacy and security.</p>';
    }

    if ( !empty( $custom_note_header ) ){
      $custom_note_header = $custom_note_header;
    } else {
      $custom_note_header = $default_note_header;
    }

    if ( !empty( $custom_note ) ){
      $note_content = $custom_note;
    } else {
      $note_content = $default_note;
    }

    $links = self::get_resources( $is_mobile ? 'mobile' : 'desktop' );
    ?>
    <div id="ftf-dma-note" class="ftf-dma-note d-none ytd-j yxd-j yxd-jd aff-content-col aff-inner-col aff-item-list ark-ad-message inplayer-ad inplayer_banners in_stream_banner trafficjunky-float-right dbanner preroll-blocker happy-inside-player blocker-notice blocker-overlay exo-horizontal ave-pl bottom-hor-block brs-block advboxemb wgAdBlockMessage glx-watermark-container overlay-advertising-new header-menu-bottom-ads rkads mdp-deblocker-wrapper amp-ad-inner imggif bloc-pub bloc-pub2 hor_banner aan_fake aan_fake__video-units rps_player_ads fints-block__row full-ave-pl full-bns-block vertbars video-brs player-bns-block wps-player__happy-inside gallery-bns-bl stream-item-widget adsbyrunactive happy-under-player adde_modal_detector adde_modal-overlay ninja-recommend-block aoa_overlay message">
      <div class="ftf-dma-note-content-wrapper">
        <span onclick="" id="ftf-dma-close-btn" class="ftf-dma-close-btn">Close</span>
        <div class="ftf-dma-note-header">
          <p><?php echo $custom_note_header;?></p>
        </div>
        <div class="ftf-dma-note-content"><?php echo wpautop( $note_content ); ?></div>
        <ul><?php
        foreach ( $links as $link ) { ?>
          <li>
            <a href="<?php echo $link['url']; ?>" target="_blank"><?php echo $link['title']; ?></a>
          </li>
        <?php }
        ?></ul>
      </div>
    </div>
  <?php }

  function add_settings_page(){
    add_options_page(
      'Detect Missing Adblocker',
      'Missing Adblocker',
      'manage_options',
      'ftf-detect-missing-adblocker',
      array( $this, 'render_settings_page' )
    );
  }

  function render_settings_page(){
    ?>
    <div class="wrap">
      <h1>Detect Missing Adblocker</h1>

      <form action='options.php' method='post' >
        <?php
        settings_fields( 'ftf_detect_missing_adblocker' );
        do_settings_sections( 'ftf_detect_missing_adblocker' );
        submit_button();
        ?>
      </form>
    </div>
    <?php 
  }

  function settings_init(){
    register_setting( 'ftf_detect_missing_adblocker', 'ftf_detect_missing_adblocker_style', 'esc_attr' );
    register_setting( 'ftf_detect_missing_adblocker', 'ftf_detect_missing_adblocker_custom_note_header', 'esc_attr' );
    register_setting( 'ftf_detect_missing_adblocker', 'ftf_detect_missing_adblocker_custom_note', 'esc_attr' );
    register_setting( 'ftf_detect_missing_adblocker', 'ftf_detect_missing_adblocker_show_on_mobile', 'esc_attr' );

    add_settings_section(
      'ftf_detect_missing_adblocker_settings', 
      __( '', 'wordpress' ), 
      array( $this, 'render_settings_form' ),
      'ftf_detect_missing_adblocker'
    );
  }

  function render_settings_form(){
    $style = get_option( 'ftf_detect_missing_adblocker_style' );
    $custom_note_header = get_option( 'ftf_detect_missing_adblocker_custom_note_header' );
    $custom_note = html_entity_decode( get_option( 'ftf_detect_missing_adblocker_custom_note' ) );
    $show_on_mobile = get_option( 'ftf_detect_missing_adblocker_show_on_mobile' );
    ?>
    <p>If it appears that your site's visitor is not using an adblocker, a note will be shown at the bottom of the page.</p>
    <h3>Style</h3>
    <p>Choose from available styles:</p>
    <select name="ftf_detect_missing_adblocker_style">
      <option value="basic" <?php selected( $style, 'basic' ); ?>>Basic</option>
      <option value="none" <?php selected( $style, 'none' ); ?>>None</option>
      <!-- <option value="bootstrap4" <?php selected( $style, 'bootstrap4' ); ?>>Bootstrap 4</option> -->
    </select>
    <ul class="ul-disc">
      <li><strong>Basic</strong>: minimal styling will be applied</li>
      <li><strong>None</strong>: no styling will be applied</li>
      <!-- <li><strong>Bootstrap 4.4</strong>: classes from <a href="https://getbootstrap.com/docs/4.4/getting-started/introduction/">Bootstrap 4.4</a> will be used</li> -->
    </ul>
    <h3>Custom Note</h3>
    <p>Customize the note shown to your website's visitors if an adblocker is not being used. Links to resources will be automatically appended at the end.</p>
    <input type="text" name="ftf_detect_missing_adblocker_custom_note_header" value="<?php echo $custom_note_header;?>" placeholder="Custom title text" style="width: 100%;">
    <?php
      wp_editor( $custom_note, 'ftf_detect_missing_adblocker_custom_note', array(
          'wpautop'       => true,
          'media_buttons' => false,
          'textarea_name' => 'ftf_detect_missing_adblocker_custom_note',
          'textarea_rows' => 10,
          'teeny'         => true
      ) );
    ?>
    <h3>Show on Mobile</h3>
    <label>
      <input type="checkbox" name="ftf_detect_missing_adblocker_show_on_mobile" <?php checked( $show_on_mobile, 'on' ) ?>>
      Show the warning to people using a mobile device.
    </label>
    <h3>Resources</h3>
    <p>These are the resources shown to your visitors. Please reach out to <a href="mailto:stefan@stefanbohacek.com?subject=RE: Detect Missing Adblocker" target="_blank">stefan@stefanbohacek.com</a> with suggestions.</p>
    <p><strong>Desktop</strong></p>
    <ul class="ul-disc"><?php
    $links = self::get_resources( 'desktop' );

    foreach ( $links as $link ) { ?>
      <li>
        <a href="<?php echo $link['url']; ?>" target="_blank"><?php echo $link['title']; ?></a>
      </li>
    <?php }

    ?></ul>

    <p><strong>Mobile (if enabled)</strong></p>
    <ul class="ul-disc"><?php
    $links = self::get_resources( 'mobile' );

    foreach ( $links as $link ) { ?>
      <li>
        <a href="<?php echo $link['url']; ?>" target="_blank"><?php echo $link['title']; ?></a>
      </li>
    <?php }
    ?></ul>
  <?php }

  function settings_page_link( $links ){
    $url = esc_url( add_query_arg(
        'page',
        'ftf-detect-missing-adblocker',
        get_admin_url() . 'admin.php'
    ) );
    $settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
    array_push(
        $links,
        $settings_link
    );
    return $links;
  }
}

$ftf_detect_missing_adblocker_init = new FTF_Detect_Missing_Adblocker();

<?php
class CODEMEMSCHEN_SETTINGS{
    
    public function __construct(){
        add_action( 'admin_menu', array( $this, 'register_menu' ) );
        add_action( 'admin_init', array( $this, 'register_config_settings') );
    }

    public function register_menu(){
        if( class_exists('CODEMEMSCHEN_IMPLEMENT') ){
    
            add_menu_page(
                esc_html__( 'Codemenschen Backup', 'codemenschen' ),
                esc_html__( 'Codemenschen Backup', 'codemenschen' ),
                'manage_options',
                'wp-codemenschen-backup-settings',
                array($this,'codemenschen_settings_page'),
                'dashicons-admin-generic'
            );

            add_submenu_page( 
                'wp-codemenschen-backup-settings',
                'Backup Now', 
                'Backup Now',
                'manage_options', 
                'wp-codemenschen-backup-now',
                array($this,'codemenschen_backup_now')
            );
                        
        }
    }
    function register_config_settings() {
        register_setting( 'codemenschen-settings-configs-group', 'codemenschen_backup_enable_plugins' );
        register_setting( 'codemenschen-settings-configs-group', 'codemenschen_backup_enable_themes' );
        register_setting( 'codemenschen-settings-configs-group', 'codemenschen_backup_enable_send_mail' );
        register_setting( 'codemenschen-settings-configs-group', 'codemenschen_backup_times_backup' );
        register_setting( 'codemenschen-settings-configs-group', 'codemenschen_backup_limit_size' );
    }

    public function codemenschen_settings_page(){
        global $wp_codemenschen_helpers;
        $wp_codemenschen_helpers->remove_all_folder();
        ?>
        <div class="codemenschen-wapper">
            <div class="container">
                <div class="codemenschen-setting">
                <form method="post" action="options.php">
                        <?php settings_fields( 'codemenschen-settings-configs-group' ); ?>
                        <?php do_settings_sections( 'codemenschen-settings-configs-group' ); ?>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">Enable Backup Plugins:</th>
                                <td>
                                    <select name="codemenschen_backup_enable_plugins">
                                        <?php if(get_option( 'codemenschen_backup_enable_plugins' ) == 'yes') { ?>
                                            <option selected value="yes">Yes</option>
                                            <option value="no">No</option>
                                        <?php } else { ?>
                                            <option value="yes">Yes</option>
                                            <option selected value="no">No</option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Enable Backup Themes:</th>
                                <td>
                                    <select name="codemenschen_backup_enable_themes">
                                        <?php if(get_option( 'codemenschen_backup_enable_themes' ) == 'yes') { ?>
                                            <option selected value="yes">Yes</option>
                                            <option value="no">No</option>
                                        <?php } else { ?>
                                            <option value="yes">Yes</option>
                                            <option selected value="no">No</option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Enable Send Mail:</th>
                                <td>
                                    <select name="codemenschen_backup_enable_send_mail">
                                        <?php if(get_option( 'codemenschen_backup_enable_send_mail' ) == 'yes') { ?>
                                            <option selected value="yes">Yes</option>
                                            <option value="no">No</option>
                                        <?php } else { ?>
                                            <option value="yes">Yes</option>
                                            <option selected value="no">No</option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Times Backup:</th>
                                <td>
                                    <input type="number" id="codemenschen_backup_times_backup" name="codemenschen_backup_times_backup" value="<?php echo get_option( 'codemenschen_backup_times_backup' ); ?>">
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Limit filesize (MB):</th>
                                <td>
                                    <input type="number" id="codemenschen_backup_limit_size" name="codemenschen_backup_limit_size" value="<?php echo get_option( 'codemenschen_backup_limit_size' ); ?>">
                                </td>
                            </tr>
                        </table>
                        <?php submit_button(); ?>
                    </form>
                </div>
                <hr>
                <div class="codemenschen-list-data-show">
                    <h2>List Folder Backup</h2>
                    <table class="wp-list-table widefat fixed striped table-view-list posts">
                        <tr>
                            <th class="stt">STT</th>
                            <th>Name</th>
                            <th>Filesize</th>
                            <th>Created at</th>
                            <th>Delete</th>
                        </tr>
                        <?php 
                        $list_folders = $wp_codemenschen_helpers->show_folders();
                        if(!empty($list_folders)) {
                            $count_folders = 1;
                            foreach ($list_folders as $folder) {
                                ?>
                                <tr>
                                    <td><?php echo $count_folders; ?></td>
                                    <td><?php echo $folder['name'] ?></td>
                                    <td><?php echo $folder['filesize'] ?></td>
                                    <td><?php echo $folder['created_at'] ?></td>
                                    <td><a id-row="<?php echo $folder['path']; ?>" class="del">Delete</a></td>
                                </tr>
                                <?php
                                $count_folders++;
                            }
                        }
                        ?>
                    </table>
                </div>
                <hr>
            </div>
        </div>
        <?php 
    }


    public function codemenschen_backup_now(){
        wp_schedule_single_event( time(), 'codemenschen_backup' );
        wp_redirect(admin_url('/admin.php?page=wp-codemenschen-backup-settings'));
    }
}

new CODEMEMSCHEN_SETTINGS();

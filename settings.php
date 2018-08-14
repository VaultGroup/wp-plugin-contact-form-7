<?php
defined( 'ABSPATH' ) or die( 'No direct access.' );
if(!class_exists('VaultRE_Contact_Enquiries_Settings'))
{
	class VaultRE_Contact_Enquiries_Settings
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// register actions
                    add_action('admin_init', array(&$this, 'admin_init'));
        	    add_action('admin_menu', array(&$this, 'add_menu'));
		} // END public function __construct
		
        /**
         * hook into WP's admin_init action hook
         */
        public function admin_init()
        {
        	// register your plugin's settings
        	register_setting('vaultre_contact_enquiries-group', 'setting_apikey');
        	register_setting('vaultre_contact_enquiries-group', 'setting_accesstoken');
        	register_setting('vaultre_contact_enquiries-group', 'setting_field_firstName');
        	register_setting('vaultre_contact_enquiries-group', 'setting_field_lastName');
        	register_setting('vaultre_contact_enquiries-group', 'setting_field_email');
        	register_setting('vaultre_contact_enquiries-group', 'setting_field_mobile');
        	register_setting('vaultre_contact_enquiries-group', 'setting_field_telephone');
        	register_setting('vaultre_contact_enquiries-group', 'setting_field_message');
        	register_setting('vaultre_contact_enquiries-group', 'setting_category_id');

        	// add your settings section
        	add_settings_section(
        	    'vaultre_contact_enquiries-apisection', 
        	    'API Settings', 
        	    array(&$this, 'settings_apisection_vaultre_contact_enquiries'), 
        	    'vaultre_contact_enquiries'
        	);

        	add_settings_section(
        	    'vaultre_contact_enquiries-generalsection', 
        	    'General Settings', 
        	    array(&$this, 'settings_generalsection_vaultre_contact_enquiries'), 
        	    'vaultre_contact_enquiries'
        	);
        	
        	add_settings_section(
        	    'vaultre_contact_enquiries-formsection', 
        	    'Form Settings', 
        	    array(&$this, 'settings_formsection_vaultre_contact_enquiries'), 
        	    'vaultre_contact_enquiries'
        	);

        	// add your setting's fields
            add_settings_field(
                'vaultre_contact_enquiries-setting_apikey', 
                'API Key', 
                array(&$this, 'settings_field_input_text'), 
                'vaultre_contact_enquiries', 
                'vaultre_contact_enquiries-apisection',
                array(
                    'field' => 'setting_apikey',
                    'style' => 'width: 500px;',
                    'helptext' => 'This value is provided directly from the VaultRE support team',
                )
            );
            add_settings_field(
                'vaultre_contact_enquiries-setting_accesstoken', 
                'Access Token', 
                array(&$this, 'settings_field_input_text'), 
                'vaultre_contact_enquiries', 
                'vaultre_contact_enquiries-apisection',
                array(
                    'field' => 'setting_accesstoken',
                    'style' => 'width: 500px;',
                    'helptext' => 'Obtain this value from within your VaultRE account (Integrations -> Third-Party Access)',
                )
            );
            add_settings_field(
                'vaultre_contact_enquiries-setting_field_firstName', 
                'First Name', 
                array(&$this, 'settings_field_input_text'), 
                'vaultre_contact_enquiries', 
                'vaultre_contact_enquiries-formsection',
                array(
                    'field' => 'setting_field_firstName',
                    'style' => 'width: 200px;',
                    'placeholder' => 'e.g. your-first-name'
                )
            );
            add_settings_field(
                'vaultre_contact_enquiries-setting_field_lastName', 
                'Last Name', 
                array(&$this, 'settings_field_input_text'), 
                'vaultre_contact_enquiries', 
                'vaultre_contact_enquiries-formsection',
                array(
                    'field' => 'setting_field_lastName',
                    'style' => 'width: 200px;',
                    'placeholder' => 'e.g. your-last-name'
                )
            );
            add_settings_field(
                'vaultre_contact_enquiries-setting_field_email', 
                'Email Address', 
                array(&$this, 'settings_field_input_text'), 
                'vaultre_contact_enquiries', 
                'vaultre_contact_enquiries-formsection',
                array(
                    'field' => 'setting_field_email',
                    'style' => 'width: 200px;',
                    'placeholder' => 'e.g. your-email'
                )
            );
            add_settings_field(
                'vaultre_contact_enquiries-setting_field_mobile', 
                'Mobile Phone', 
                array(&$this, 'settings_field_input_text'), 
                'vaultre_contact_enquiries', 
                'vaultre_contact_enquiries-formsection',
                array(
                    'field' => 'setting_field_mobile',
                    'style' => 'width: 200px;',
                    'placeholder' => 'e.g. your-mobile-phone'
                )
            );
            add_settings_field(
                'vaultre_contact_enquiries-setting_field_telephone', 
                'Home Phone', 
                array(&$this, 'settings_field_input_text'), 
                'vaultre_contact_enquiries', 
                'vaultre_contact_enquiries-formsection',
                array(
                    'field' => 'setting_field_telephone',
                    'style' => 'width: 200px;',
                    'placeholder' => 'e.g. your-home-phone'
                )
            );
            add_settings_field(
                'vaultre_contact_enquiries-setting_field_message', 
                'Message', 
                array(&$this, 'settings_field_input_text'), 
                'vaultre_contact_enquiries', 
                'vaultre_contact_enquiries-formsection',
                array(
                    'field' => 'setting_field_message',
                    'style' => 'width: 200px;',
                    'placeholder' => 'e.g. your-message'
                )
            );
            add_settings_field(
                'vaultre_contact_enquiries-setting_category_id', 
                'Category ID', 
                array(&$this, 'settings_field_input_text'), 
                'vaultre_contact_enquiries', 
                'vaultre_contact_enquiries-generalsection',
                array(
                    'field' => 'setting_category_id',
                    'style' => 'width: 100px;',
                    'placeholder' => '',
                    'helptext' => 'ID of the category to which the contact should be added. Obtain this ID from VaultRE.'
                )
            );

        } // END public static function activate
        
        public function settings_apisection_vaultre_contact_enquiries()
        {
            echo 'These settings are required to capture your contact form enquiries in your VaultRE account.';
        }

        public function settings_formsection_vaultre_contact_enquiries()
        {
            echo 'These settings control which of your form fields map to fields in the VaultRE API. Enter the form field name corresponding to each field.';
        }

        public function settings_generalsection_vaultre_contact_enquiries()
        {
            echo 'Other miscellaneous settings.';
        }
        
        /**
         * This function provides text inputs for settings fields
         */
        public function settings_field_input_text($args)
        {
            $field = $args['field'];
            $value = get_option($field);
            $style = $args['style'];
            $placeholder = $args['placeholder'];
            $helptext = $args['helptext'];

            echo sprintf('<input type="text" name="%s" id="%s" value="%s" style="%s" placeholder="%s" />', $field, $field, $value, $style, $placeholder);

            if ($helptext) {
                echo sprintf('<br /><small>%s</small>', $helptext);
            }

        } // END public function settings_field_input_text($args)
        
        /**
         * add a menu
         */		
        public function add_menu()
        {
            // Add a page to manage this plugin's settings
        	add_options_page(
        	    'VaultRE Contacts Settings', 
        	    'VaultRE Contacts', 
        	    'manage_options', 
        	    'vaultre_contact_enquiries', 
        	    array(&$this, 'plugin_settings_page')
        	);
        } // END public function add_menu()
    
        /**
         * Menu Callback
         */		
        public function plugin_settings_page()
        {
        	if(!current_user_can('manage_options'))
        	{
        		wp_die(__('You do not have sufficient permissions to access this page.'));
        	}
	
        	// Render the settings template
        	include(sprintf("%s/templates/settings.php", dirname(__FILE__)));
        } // END public function plugin_settings_page()
    } // END class VaultRE_Contact_Enquiries_Settings
} // END if(!class_exists('VaultRE_Contact_Enquiries_Settings'))

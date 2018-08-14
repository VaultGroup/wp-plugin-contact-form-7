<?php
/*
Plugin Name: VaultRE Contact Form 7
Plugin URI: https://github.com/VaultRealEstate/wp-plugin-contact-form-7
Description: A simple plugin for capturing Contact Form 7 (WPCF7) enquiries in VaultRE
Version: 1.0
Author: VaultRealEstate
Author URI: https://www.vaultrealestate.com.au
License: GPL2
*/
/*
Copyright 2018  Complete Real Estate Solutions (email : info@vaultrealestate.com.au)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'ABSPATH' ) or die( 'No direct access.' );

if(!class_exists('VaultRE_Contact_Enquiries'))
{
	class VaultRE_Contact_Enquiries
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// Initialize Settings
			require_once(sprintf("%s/settings.php", dirname(__FILE__)));

			// Initialize VaultRE API
			require_once(sprintf("%s/class.vaultreapi.php", dirname(__FILE__)));

			$VaultRE_Contact_Enquiries_Settings = new VaultRE_Contact_Enquiries_Settings();

			$plugin = plugin_basename(__FILE__);
			add_filter("plugin_action_links_$plugin", array( $this, 'plugin_settings_link' ));

			// Add action hooking in to Contact Form 7
			add_action("wpcf7_before_send_mail", array( $this, 'send_contact_to_vaultre' ), 10, 1);

		} // END public function __construct

		private function remove_options()
		{
                        delete_option('setting_apikey');
                        delete_option('setting_accesstoken');
                        delete_option('setting_field_firstName');
                        delete_option('setting_field_lastName');
                        delete_option('setting_field_email');
                        delete_option('setting_field_mobile');
                        delete_option('setting_field_telephone');
                        delete_option('setting_field_message');
                        delete_option('setting_category_id');
		}

		/**
		 * Activate the plugin
		 */
		public static function activate()
		{
			// Do nothing
		} // END public static function activate

		/**
		 * Deactivate the plugin
		 */
		public static function deactivate()
		{
                        // Do nothing
		} // END public static function deactivate

		/**
		 * Uninstall the plugin
		 */
		public static function uninstall()
		{
                        self::remove_options();
		} // END public static function uninstall

		// Add the settings link to the plugins page
		function plugin_settings_link($links)
		{
			$settings_link = '<a href="options-general.php?page=vaultre_contact_enquiries">Settings</a>';
			array_unshift($links, $settings_link);
			return $links;
		}

		function send_contact_to_vaultre($contact_form)
		{

			$wpcf = WPCF7_Submission::get_instance();

			$api_key = get_option('setting_apikey');
			$access_token = get_option('setting_accesstoken');

			if (!$api_key || !$access_token) {
				error_log('Settings not defined');
				return $wpcf;
			}

			$submittedData = $wpcf->get_posted_data();

			$field_firstName = get_option('setting_field_firstName');
			$field_lastName = get_option('setting_field_lastName');
			$field_email = get_option('setting_field_email');
			$field_mobile = get_option('setting_field_mobile');
			$field_telephone = get_option('setting_field_telephone');
			$field_message = get_option('setting_field_message');
			$category_id = get_option('setting_category_id');

			$data = array();
			if ($field_firstName) {
				if ($submittedData[$field_firstName]) {
					$data['firstName'] = $submittedData[$field_firstName];
				}
			}
			if ($field_lastName) {
				if ($submittedData[$field_lastName]) {
					$data['lastName'] = $submittedData[$field_lastName];
				}
			}
			if ($field_email) {
				if ($submittedData[$field_email]) {
					$data['emails'] = array($submittedData[$field_email]);
				}
			}
			if ($field_mobile) {
				if ($submittedData[$field_mobile]) {
					$mobile = array("number" => $submittedData[$field_mobile], "typeCode" => "M");
				}
			}
			if ($field_telephone) {
				if ($submittedData[$field_telephone]) {
					$telephone = array("number" => $submittedData[$field_telephone], "typeCode" => "H");
				}
			}

			if ($mobile || $telephone) {
				$data['phoneNumbers'] = array();
				if ($mobile) {
					array_push($data['phoneNumbers'], $mobile);
				}
				if ($telephone) {
					array_push($data['phoneNumbers'], $telephone);
				}
			}

			$api = new VaultREAPI($api_key, $access_token);

			// Add contact

			$payload = json_encode($data);
			error_log('Adding contact to VaultRE...');
			list($code, $result) = $api->post('/contacts', $payload);

			if ($code != 201) {
    				error_log(sprintf("VaultRE API: HTTP %s", $code));
				return $wpcf;
			}

			$result = json_decode($result);
			if (!$result) {
				error_log('Failed to submit contact to API');
				return $wpcf;
			}

			$contact_id = $result->{"id"};
			if (!$contact_id) {
				error_log('Failed to submit contact to API');
				return $wpcf;
			}

			if ($category_id) {
				// Assign category to contact
				error_log('Assigning category...');
				$categories = array(array("id" => $category_id));
                        	$payload = json_encode(array("items" => $categories));
				list($code, $result) = $api->put("/contacts/" . $contact_id . "/categories", $payload);

				if ($code != 200) {
    					error_log(sprintf("VaultRE API: HTTP %s", $code));
					return $wpcf;
				}
			}

			// Add notes to contact
			$notes = array(array("contact" => array("id" => $contact_id), "body" => "Inserted via VaultRE Wordpress Plugin"));

			if ($field_message) {
				if ($submittedData[$field_message]) {
					array_push($notes, array("contact" => array("id" => $contact_id), "body" => $submittedData[$field_message]));
				}
			}

			error_log('Submitting notes to VaultRE...');
			$payload = json_encode(array("readOnly" => false, "notes" => $notes));
			list($code, $result) = $api->post("/contacts/notes", $payload);

			if ($code != 201) {
    				error_log(sprintf("VaultRE API: HTTP %s", $code));
			}

			return $wpcf;

		}


	} // END class VaultRE_Contact_Enquiries
} // END if(!class_exists('VaultRE_Contact_Enquiries'))

if(class_exists('VaultRE_Contact_Enquiries'))
{
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('VaultRE_Contact_Enquiries', 'activate'));
	register_deactivation_hook(__FILE__, array('VaultRE_Contact_Enquiries', 'deactivate'));
        register_uninstall_hook(__FILE__, array('VaultRE_Contact_Enquiries', 'uninstall'));

	// instantiate the plugin class
	$vaultre_contact_enquiries = new VaultRE_Contact_Enquiries();

}

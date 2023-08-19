<?php





/**


 * The plugin bootstrap file


 *


 * This file is read by WordPress to generate the plugin information in the plugin


 * admin area. This file also includes all of the dependencies used by the plugin,


 * registers the activation and deactivation functions, and defines a function


 * that starts the plugin.


 *


 * @link              https://www.smartdatainc.com


 * @since             1.0.0


 * @package           Miamimed_Telehealth


 *


 * @wordpress-plugin


 * Plugin Name:       MiamiMed Telehealth


 * Plugin URI:        https://www.smartdatainc.com


 * Description:       Plugin is used to manage MiamiMed Telehealth


 * Version:           1.0.0


 * Author:            Smartdatainc


 * Author URI:        https://www.smartdatainc.com


 * License:           GPL-2.0+


 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt


 * Text Domain:       miamimed-telehealth


 * Domain Path:       /languages


 */





// If this file is called directly, abort.


if ( ! defined( 'WPINC' ) ) {


	die;


}





/**


 * Currently plugin version.


 * Start at version 1.0.0 and use SemVer - https://semver.org


 * Rename this for your plugin and update it as you release new versions.


 */


define( 'MIAMIMED_TELEHEALTH_VERSION', '1.0.0' );





/**


 * The code that runs during plugin activation.


 * This action is documented in includes/class-miamimed-telehealth-activator.php


 */


function activate_miamimed_telehealth() {


	require_once plugin_dir_path( __FILE__ ) . 'includes/class-miamimed-telehealth-activator.php';


	Miamimed_Telehealth_Activator::activate();


}





/**


 * The code that runs during plugin deactivation.


 * This action is documented in includes/class-miamimed-telehealth-deactivator.php


 */


function deactivate_miamimed_telehealth() {


	require_once plugin_dir_path( __FILE__ ) . 'includes/class-miamimed-telehealth-deactivator.php';


	Miamimed_Telehealth_Deactivator::deactivate();


}





register_activation_hook( __FILE__, 'activate_miamimed_telehealth' );


register_deactivation_hook( __FILE__, 'deactivate_miamimed_telehealth' );





/**


 * The core plugin class that is used to define internationalization,


 * admin-specific hooks, and public-facing site hooks.


 */


require plugin_dir_path( __FILE__ ) . 'includes/class-miamimed-telehealth.php';





/**


 * Begins execution of the plugin.


 *


 * Since everything within the plugin is registered via hooks,


 * then kicking off the plugin from this point in the file does


 * not affect the page life cycle.


 *


 * @since    1.0.0


 */


function run_miamimed_telehealth() {





	$plugin = new Miamimed_Telehealth();


	$plugin->run();





}


run_miamimed_telehealth();



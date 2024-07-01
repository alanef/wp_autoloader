<?php
namespace Fullworks_WP_Autoloader;

use Composer\Plugin\PluginInterface;
use Composer\Composer;
use Composer\IO\IOInterface;

class AutoloaderPlugin implements PluginInterface
{
	public function activate(Composer $composer, IOInterface $io)
	{
		spl_autoload_register(
			function ($class_name) {

				if (false === strpos($class_name, 'Quick_Event_Manager\Plugin')) {
					return;
				}
				$file_name_no_suffix = '';
				$file_parts = explode('\\', $class_name);
				$namespace  = '';
				for ($i = count($file_parts) - 1; $i > 1; $i -- ) {

					$current = strtolower($file_parts[$i]);
					$current = str_ireplace('_', '-', $current);

					if (count($file_parts) - 1 === $i) {

						if (strpos(strtolower($file_parts[count($file_parts) - 1]), 'interface')) {

							$interface_name      = explode('_', $file_parts[count($file_parts) - 1]);
							$interface_name      = $interface_name[0];
							$file_name_no_suffix = "interface-$interface_name";
						}
						else {
							$file_name_no_suffix = "class-$current";
						}
					}
					else {
						$namespace = '/' . $current . $namespace;
					}
				}

				$filepath = rtrim(dirname(dirname(__FILE__)) . $namespace, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
				$filepath .= $file_name_no_suffix;

				if (is_readable($filepath . '.php')) {
					require_once($filepath . '.php');
					return;
				}

				if ( is_readable($filepath . '__premium_only.php')) {
					require_once($filepath . '__premium_only.php');
					return;
				}

				error_log("The system file attempting to be loaded at $filepath does not exist.");
				return;
			}
		);
	}
}
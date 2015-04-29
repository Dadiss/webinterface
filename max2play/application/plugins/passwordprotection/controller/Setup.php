<?php 

/**
 Password Protection Setup Controller
 
 @Copyright 2015 Stefan Rick
 @author Stefan Rick
 Mail: stefan@rick-software.de
 Web: http://www.netzberater.de

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License along
 with this program; if not, write to the Free Software Foundation, Inc.,
 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

class Passwordprotection_Setup extends Service {
	
	public $scriptPath = '';
	
	public function __construct(){
		parent::__construct();
		
		//Set your Pluginname
		$this->pluginname = _('Password Protection');
		
		$this->registerLocale(dirname(__FILE__).'/../locale', 'passwordprotection');
		
		$this->_getProtectionInfo();
		
		if(isset($_REQUEST['action'])){
			if($_REQUEST['action'] == 'install'){
				$this->_install();
			}
			
			if($_REQUEST['action'] == 'uninstall'){
				$this->_uninstall();
			}
		}				
				
	}
	
	private function _getProtectionInfo(){
		//Check for existing Protection
		if(shell_exec('cat /var/www/max2play/public/.htaccess | grep "##AUTHENTICATION START"') != FALSE){
			$this->view->installed = true;
		}else{
			$this->view->installed = false;
		}		
	}
	
	private function _install(){
		//edit /var/www/max2play/.htaccess
		$user = $_REQUEST['user'];
		$pass = $_REQUEST['password'];
		$htaccess_code = "##AUTHENTICATION START\nAuthType Basic\nAuthName 'Protected Max2Play Login'\nAuthUserFile /var/www/max2play/.htpasswd\nAuthGroupFile /dev/null\nRequire valid-user\n##AUTHENTICATION END";
		$script = array();
		$script[] = "/bin/sed -i \"s@RewriteEngine on@".str_replace("\n","\\\\n",$htaccess_code)."\\nRewriteEngine on@\" /var/www/max2play/public/.htaccess";		
		$script[] = '/usr/bin/htpasswd -b -c /var/www/max2play/.htpasswd "'.$user.'" "'.$pass.'"';
		$script[] = 'echo "'.$htaccess_code.'" > /var/www/max2play/public/.htaccess_add';
		$this->writeDynamicScript($script);
		$this->view->message[] = _t('Password Protection is now installed!');
		$this->_getProtectionInfo();
		return true;
	}
	
	private function _uninstall(){
		//edit /var/www/max2play/.htaccess		
		$script[] = '/bin/sed -i "/##AUTHENTICATION START/,/##AUTHENTICATION END/ { /##AUTHENTICATION END/"\'!\'" d; }" /var/www/max2play/public/.htaccess';
		$script[] = '/bin/rm /var/www/max2play/.htpasswd;/bin/rm /var/www/max2play/public/.htaccess_add';
		$this->writeDynamicScript($script);
		$this->view->message[] = _t('Password Protection is now removed!');
		$this->_getProtectionInfo();
	}
		
}

//Create an instance of your Class
$pp = new Passwordprotection_Setup();

//This Line includes the View-Script -> it should have the same name as your class
include_once(dirname(__FILE__).'/../view/setup.php');


<?php
class optionsViewScs extends viewScs {
	private $_news = array();
	public function getNewFeatures() {
		$res = array();
		$readmePath = SCS_DIR. 'readme.txt';
		if(file_exists($readmePath)) {
			$readmeContent = @file_get_contents($readmePath);
			if(!empty($readmeContent)) {
				$matchedData = '';
				if(preg_match('/= '. SCS_VERSION. ' =(.+)=.+=/isU', $readmeContent, $matches)) {
					$matchedData = $matches[1];
				} elseif(preg_match('/= '. SCS_VERSION. ' =(.+)/is', $readmeContent, $matches)) {
					$matchedData = $matches[1];
				}
				$matchedData = trim($matchedData);
				if(!empty($matchedData)) {
					$res = array_map('trim', explode("\n", $matchedData));
				}
			}
		}
		return $res;
	}
    public function getAdminPage() {
		$tabs = $this->getModule()->getTabs();
		$activeTab = $this->getModule()->getActiveTab();
		$content = 'No tab content found - ERROR';
		if(isset($tabs[ $activeTab ]) && isset($tabs[ $activeTab ]['callback'])) {
			//frameScs::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('tab.'. $activeTab);
			$content = call_user_func($tabs[ $activeTab ]['callback']);
		}
		$activeParentTabs = array();
		foreach($tabs as $tabKey => $tab) {
			if($tabKey == $activeTab && isset($tab['child_of'])) {
				$activeTab = $tab['child_of'];
				//$activeParentTabs[] = $tab['child_of'];
			}
		}
		frameScs::_()->addJSVar('adminOptionsScs', 'scsActiveTab', $activeTab);
		$this->assign('tabs', $tabs);
		$this->assign('activeTab', $activeTab);
		$this->assign('content', $content);
		$this->assign('mainUrl', $this->getModule()->getTabUrl());
		$this->assign('activeParentTabs', $activeParentTabs);
		$this->assign('breadcrumbs', frameScs::_()->getModule('admin_nav')->getView()->getBreadcrumbs());
		$this->assign('mainLink', frameScs::_()->getModule('supsystic_promo')->getMainLink());
		
        parent::display('optionsAdminPage');
    }
	public function sortOscsSet($a, $b) {
		if($a['weight'] > $b['weight'])
			return -1;
		if($a['weight'] < $b['weight'])
			return 1;
		return 0;
	}
	public function getTabContent() {
		frameScs::_()->addScript('admin.mainoptions', $this->getModule()->getModPath(). 'js/admin.mainoptions.js');
		return parent::getContent('optionsAdminMain');
	}
	public function serverSettings() {
		$this->assign('systemInfo', array(
            'Operating System' => array('value' => PHP_OS),
            'PHP Version' => array('value' => PHP_VERSION),
            'Server Software' => array('value' => $_SERVER['SERVER_SOFTWARE']),
            'MySQL' => array('value' => function_exists('mysql_get_server_info') ? @mysql_get_server_info() : __('Undefined', SCS_LANG_CODE)),
            'PHP Safe Mode' => array('value' => ini_get('safe_mode') ? 'Yes' : 'No', 'error' => ini_get('safe_mode')),
            'PHP Allow URL Fopen' => array('value' => ini_get('allow_url_fopen') ? 'Yes' : 'No'),
            'PHP Memory Limit' => array('value' => ini_get('memory_limit')),
            'PHP Max Post Size' => array('value' => ini_get('post_max_size')),
            'PHP Max Upload Filesize' => array('value' => ini_get('upload_max_filesize')),
            'PHP Max Script Execute Time' => array('value' => ini_get('max_execution_time')),
            'PHP EXIF Support' => array('value' => extension_loaded('exif') ? 'Yes' : 'No'),
            'PHP EXIF Version' => array('value' => phpversion('exif')),
            'PHP XML Support' => array('value' => extension_loaded('libxml') ? 'Yes' : 'No', 'error' => !extension_loaded('libxml')),
            'PHP CURL Support' => array('value' => extension_loaded('curl') ? 'Yes' : 'No', 'error' => !extension_loaded('curl')),
        ));
		return parent::display('_serverSettings');
	}
	public function getSettingsTabContent() {
		frameScs::_()->addScript('admin.settings', $this->getModule()->getModPath(). 'js/admin.settings.js');
		frameScs::_()->addStyle('admin.settings', $this->getModule()->getModPath(). 'css/admin.settings.css');
		frameScs::_()->getModule('templates')->loadJqueryUi();
		
		$options = frameScs::_()->getModule('options')->getAll();
		$this->assign('options', $options);
		return parent::getContent('optionsSettingsTabContent');
	}
}
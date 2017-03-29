<?php

$routers = array();
$routers['/'] = array('CampaignBundle\Page', 'index');
$routers['/test1'] = array('CampaignBundle\Page', 'test');
$routers['/api/sms'] = array('CampaignBundle\Page', 'sms');
$routers['/api/submit'] = array('CampaignBundle\Page', 'submit');
$routers['/jssdk'] = array('CampaignBundle\Page', 'jssdkConfigJs');



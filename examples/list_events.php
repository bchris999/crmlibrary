<?php
require 'vendor/autoload.php';

$connector = new \Bostonspike\CrmLibrary\CrmIntegration(new \Bostonspike\CrmLibrary\CrmConnection(
    '***url***', 
    '***username***', 
    '***password***'
));

// List events
$campaign_list = $connector->getCampaignList();
foreach ($campaign_list as $value)
{
    echo "id:   ".$value['id']."\r\n";
    echo "name: ".$value['eventName']."\r\n";
    echo "date: ".$value['date']."\r\n\r\n";
}

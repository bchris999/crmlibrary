<?php
require 'vendor/autoload.php';

$ud = new \Bostonspike\CrmLibrary\UserData(
    "email@domain.com",
    "Mr",
    "First Name",
    "Last Name",
    "Address 1",
    "Address 2",
    "Address 3",
    "City",
    "State",
    "Postcode",
    "Job Title",
    "00000 0000000",
    "Company Name"
);
$campaign_id = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx";

$connector = new \Bostonspike\CrmLibrary\CrmIntegration(new \Bostonspike\CrmLibrary\CrmConnection(
    '***url***', 
    '***username***', 
    '***password***'
));

// Register User
$responseData = new \Bostonspike\CrmLibrary\CampaignResponseData($campaign_id);
$campaign_data = $connector->getCampaign($campaign_id);

$contacts = $connector->findContactByEmailAndName($ud->email, $ud->fname, $ud->lname);
if (count($contacts) == 1) { // Found existing contact
    $contact = $contacts[0];
    if ($ud->address1 != $contact['address1Line1']) $responseData->reviewFlag = true;
    if ($ud->telephone != $contact['telephone1']) $responseData->reviewFlag = true;
    if ($ud->jobtitle != $contact['jobTitle']) $responseData->reviewFlag = true;
    $contact_id = $contact['id'];
} else {  // Create new contact
    die("No contact found");
    $organizations = $connector->findOrganizationByName($ud->organization);
    if (count($organizations) == 1) // Organization match found
    {
        $ud->organizationId = $organizations[0]['id'];
    } else {
        $responseData->reviewFlag = true;
    }
    $contact_id = $connector->createContact($ud);
    $contact_created = true;
}

if ($campaign_data['statusCode'] == 2) $responseData->responseCode = 200000; // Registered
if ($campaign_data['statusCode'] == 200000) $responseData->responseCode = 200002; // Wait Listed
$responseData->setUserData($ud, $contact_id);
$campaign_response_id = $connector->createCampaignResponse($responseData);

// RESULTS
echo "Campaign id:         $campaign_id\r\n";
if ($contact_created == true) {
    echo "Contact created:     $contact_id\r\n";
} else {
    echo "Contact found:       $contact_id\r\n";
}
echo "Registered activity: $campaign_response_id\r\n";
echo "Contact review:      $responseData->reviewFlag\r\n";

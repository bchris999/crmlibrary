<?php
namespace Bostonspike\CrmLibrary;
/**
 * CrmIntegration
 *
 * @package CrmLibrary
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Chris Brazier <chrisb@bostonspike.com>
 */
class CrmIntegration {
    private $crmConnection;

    private $campaignFilter = "StateCode/Value eq 0 and MSA_PublishEventDetailsonWeb eq true and (TypeCode/Value eq 3 or TypeCode/Value eq 200001)";

    public function __construct(CrmConnection $connection)
    {
        $this->crmConnection = $connection;
    }

    public function getCampaignList()
    {
        $campaign_list = $this->crmConnection->doQuery(
            "CampaignSet",
            $this->campaignFilter,
            "MSA_EndDateTime"
        );
        $result = [];
        foreach ($campaign_list->d->results as $key => $value)
        {
            $result[] = [
                'id' => $value->CampaignId,
                'eventName' => $value->MSA_EventName,
                'date' => $value->CpCS_Eventdatenotime,
            ];
        }
        return $result;
    }

    public function getCampaign($guid)
    {
        $campaign_data = ($this->crmConnection->doFetch("CampaignSet", $guid))->d;
        return [
            'id' => $campaign_data->CampaignId,
            'statusCode' => $campaign_data->StatusCode->Value,
        ];
    }

    public function findContactByEmailAndName($email, $fname, $lname)
    {
        $contacts = $this->crmConnection->doQuery(
            "ContactSet",
            "((EMailAddress1 eq '$email') or (EMailAddress2 eq '$email') or (EMailAddress3 eq '$email') and FirstName eq '$fname' and LastName eq '$lname')"
        );
        $result = [];
        foreach ($contacts->d->results as $key => $value)
        {
            $result[] = [
                'id' => $value->ContactId,
                'address1Line1' => $value->Address1_Line1,
                'telephone1' => $value->Telephone1,
                'jobTitle' => $value->JobTitle,
            ];
        }
        return $result;
    }

    public function findOrganizationByName($name)
    {
        $org = $this->crmConnection->doQuery(
            "AccountSet",
            "startswith(Name,'$name')"
        );
        $result = [];
        foreach ($org->d->results as $key => $value)
        {
            $result[] = [
                'id' => $value->AccountId,
            ];
        }
        return $result;
    }

    public function createContact(UserData $data)
    {
        $contact = $this->crmConnection->doCreate('ContactSet', $data->getNewContactData());
        $contact_id = $contact->d->ContactId;
        return $contact_id;
    }

    public function createCampaignResponse(CampaignResponseData $data)
    {
        $response = $this->crmConnection->doCreate('CampaignResponseSet', $data->getNewCampaignResponseData());
        $response_id = $response->d->ActivityId;
        return $response_id;
    }
}

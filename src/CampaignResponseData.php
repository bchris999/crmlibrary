<?php
namespace Bostonspike\CrmLibrary;
/**
 * CampaignResponseData
 *
 * @package CrmLibrary
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Chris Brazier <chrisb@bostonspike.com>
 */
class CampaignResponseData {
    public $campaignId;
    public $reviewFlag = false;
    public $responseCode = 1;

    private $userData;
    private $contactId;

    public function __construct($campaignId = null)
    {
        $this->campaignId = $campaignId;
    }

    public function setUserData(UserData $data, $contactId = null)
    {
        $this->userData = $data;
        $this->contactId = $contactId;
    }

    public function getNewCampaignResponseData()
    {
        $result = $this->userData->getNewCampaignResponseData();
        $result['RegardingObjectId'] = [
            'Id' => $this->campaignId,
            'LogicalName' => 'campaign',
        ];
        $result['ReceivedOn'] = date('D M d Y H:i:s O');
        $result['sclevf_ContactDetailsneedReviewCorrection'] = $this->reviewFlag;
        $result['ResponseCode'] = [
            'Value' => $this->responseCode,
        ];
        // https://docs.microsoft.com/en-us/dynamics365/customer-engagement/developer/activityparty-entity
        if ($this->contactId) {
            $result['campaignresponse_activity_parties'] = [
                [
                    'PartyId' => [
                        'Id' => $this->contactId,
                        'LogicalName' => 'contact',
                    ],
                    'ParticipationTypeMask' => [
                        'Value' => 11,
                    ],
                ],
            ];
        }
        return $result;
    }
}

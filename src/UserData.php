<?php
namespace Bostonspike\CrmLibrary;
/**
 * UserData
 *
 * @package CrmLibrary
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Chris Brazier <chrisb@bostonspike.com>
 */
class UserData {
    private $prefixTable = [
        'Dr' => 1,
        'Miss' => 2,
        'Mr' => 3,
        'Mrs' => 4,
        'Ms' => 5,
        'Prof' => 6,
    ];

    public $organizationId = null;

    public function __construct(
        $email = null,
        $prefix = null,
        $fname = null,
        $lname = null,
        $address1 = null,
        $address2 = null,
        $address3 = null,
        $city = null,
        $state = null,
        $postcode = null,
        $jobtitle = null,
        $telephone = null,
        $organization = null)
    {
        $this->email = $email;
        $this->prefix = $prefix;
        $this->fname = $fname;
        $this->lname = $lname;
        $this->address1 = $address1;
        $this->address2 = $address2;
        $this->address3 = $address3;
        $this->city = $city;
        $this->state = $state;
        $this->postcode = $postcode;
        $this->jobtitle = $jobtitle;
        $this->telephone = $telephone;
        $this->organization = $organization;
    }

    public function getNewContactData()
    {
        $result = [
            'EMailAddress1' => $this->email,
            'Salutation' => $this->prefix,
            'FirstName' => $this->fname,
            'LastName' => $this->lname,
            'Address1_Line1' => $this->address1,
            'Address1_Line2' => $this->address2,
            'Address1_Line3' => $this->address3,
            'Address1_City' => $this->city,
            'Address1_StateOrProvince' => $this->state,
            'Address1_PostalCode' => $this->postcode,
            'JobTitle' => $this->jobtitle,
            'Telephone1' => $this->telephone,
        ];
        if ($this->organizationId !== null)
        {
            $result['ParentCustomerId']['Id'] = $this->organizationId;
            $result['ParentCustomerId']['LogicalName'] = "account";
        }
        return $result;
    }

    public function getNewCampaignResponseData()
    {
        return [
            'EMailAddress' => $this->email,
            'MSA_Prefix' => ['Value' => $this->prefixTable[$this->prefix]],
            'FirstName' => $this->fname,
            'LastName' => $this->lname,
            'MSA_StreetAddress1' => $this->address1,
            'MSA_StreetAddress2' => $this->address2,
            'MSA_StreetAddress3' => $this->address3,
            'MSA_City' => $this->city,
            'MSA_State' => $this->state,
            'MSA_PostalCode' => $this->postcode,
            'MSA_JobTitle' => $this->jobtitle,
            'Telephone' => $this->telephone,
            'CompanyName' => $this->organization,
        ];
    }
}

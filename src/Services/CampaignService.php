<?php

namespace Msg91\Services;

use Exception;
use GuzzleHttp\Client;

class CampaignService {
    protected $baseUrl = 'https://control.msg91.com/api/v5/campaign/api/';

    public function __construct($authKey) {
        $this->authKey = $authKey;
    }

    // PARAM INPUT DATA  {CAMPAIGN_SLUG, DATA(MAX:1000)}
    public function runCampaign($campaignSlug, $inputData) {
        try {
            // VERIFY INPUT
            $this->verifyInputData($inputData);

            // CHECK CAMPAIGN VALIDATION
            $mappingData = $this->verifyAndGetCampaignMappings($campaignSlug);

            // IF VALID CREATE SEND-TO BODY AND LAUNCH CAMPAIGN
            $sendCampaign['data'] = $this->createSendToBody($inputData, $mappingData);

            // RUN CAMPAIGN
            $response = $this->sendCampaign($campaignSlug, $sendCampaign);
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage(), 1);
        }
        return [
            "message" => "Campaign Run Successfully",
            "request_id" => $response['request_id']
        ];
    }

    private function verifyInputData($inputData) {

        if (empty($inputData)) {
            throw new Exception("Must require a record to Run Campaign", 1);
        }

        if (sizeof($inputData) > 1000) {
            throw new Exception("Record data limit exceeded : total limit 1000", 1);
        }
    }

    private function verifyAndGetCampaignMappings($campaignSlug) {
        $mappingData = [];
        $operation = "campaigns/$campaignSlug/fields?source=launchCampaign";
        $campaignMappings = $this->makeApiCall($operation);

        // THROW ERROR IF CAMPAIGN INVALID
        if (empty($campaignMappings['mapping'])) {
            throw new Exception("Invalid Campaign or no Node in Campaign", 1);
        }

        // GET THE MAPPINGS
        foreach ($campaignMappings['mapping'] as $mapping) {
            $mappingData['mappings'][] = $mapping['name'];
        }

        // GET THE VARIABLES
        if (!empty($campaignMappings['variables'])) {
            $mappingData['variables'] = $campaignMappings['variables'];
        }

        return $mappingData;
    }

    private function createSendToBody($inputData, $mappingData) {
        $sendCampaign = ['sendTo' => []];
        $mappings = $mappingData['mappings'];
        $variables = $mappingData['variables'];

        foreach ($inputData['data'] as $data) {
            $temp = [];

            foreach ($mappings as $map) {
                if (isset($data[$map])) {
                    // EMAIL MAPPINGS 
                    if ($map == 'to') {
                        // VALIDATE EMAIL
                        if (filter_var($data[$map], FILTER_VALIDATE_EMAIL)) {
                            $temp[$map][0]['email'] = $data[$map];
                        }
                    }

                    // Mobiles mappings
                    $regex = '/^\+?[0-9]{7,14}$/';
                    if ($map == 'mobiles' && preg_match($regex, $data[$map])) {
                        $temp['to'][0]['mobiles'] = $data[$map];
                    }

                    // CC BCC MAPPINGS
                    if ($map == 'cc' || $map == 'bcc') {
                        // VALIDATE EMAIL
                        if (filter_var($data[$map], FILTER_VALIDATE_EMAIL)) {
                            $temp[$map][0]['email'] = $data[$map];
                        }
                    }

                    // EMAIL FROM NAME
                    if ($map == 'from_name') {
                        $temp[$map] = $data[$map];
                    }

                    // FROM SENDER EMAIL
                    if ($map == 'from_email') {
                        // VALIDATE EMAIL
                        if (filter_var($data[$map], FILTER_VALIDATE_EMAIL)) {
                            $temp[$map] = $data[$map];
                        }
                    }
                }
            }

            // SENDER NAME
            if (array_key_exists('to', $data) && array_key_exists('name', $data) && !empty($data['name'])) {
                $temp['to'][0]['name'] = $data['name'];
            }

            // VARIABLES MAPPINGS
            $tempVariables = [];
            if (!empty($data['variables'])) {
                foreach ($variables as $var) {
                    if (isset($data['variables'][$var])) {
                        $tempVariables[$var] = $data['variables'][$var];
                    }
                }
            }

            $temp['variables'] = $tempVariables;

            // ADD TO SEND TO
            $sendCampaign['sendTo'][] = $temp;

            if (in_array('to', $mappings)) {

                // ADD REPLY TP 
                if (isset($inputData['reply_to']))
                    $sendCampaign['reply_to'] = $inputData['reply_to'];

                // ADD ATTACHMENTS
                if (isset($inputData['attachments']))
                    $sendCampaign['attachments'] = $inputData['attachments'];
            }
        }

        return $sendCampaign;
    }

    private function sendCampaign($campaignSlug, $data) {
        $operation = 'campaigns/' . $campaignSlug . '/run';
        return $this->makeApiCall($operation, $data, 'POST');
    }

    private function makeApiCall($operation, $inputData = [], $method = 'GET') {
        try {
            $client = new Client();
            $endpoint = $this->baseUrl . $operation;

            $options = [
                'headers' => [
                    'authkey' => $this->authKey
                ],
                'json' => $inputData
            ];
            $response = $client->{$method}($endpoint, $options);
            $statusCode = $response->getStatusCode();
            var_dump($statusCode);
            $response = json_decode($response->getBody()->getContents(), true);
            return $response['data'];
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                throw new \Exception(($e->getResponse()->getBody()->getContents()), 1);
            }
            throw new \Exception($e->getMessage(), 1);
        }
    }
}

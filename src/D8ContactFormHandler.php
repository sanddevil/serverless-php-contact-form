<?php

namespace Raines\Serverless;

require 'vendor/autoload.php';

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;

class D8ContactFormHandler implements Handler
{
    protected $client_config = [
        'region' => 'eu-west-1',
        'version' => '2010-12-01',
        'credentials.cache' => TRUE,
        'validation' => FALSE,
    ];

    /**
     * {@inheritdoc}
     */
    public function handle(array $event, Context $context)
    {
        $logger = $context->getLogger();
        // uncomment for debugging
        //$logger->notice('Got event', $event);

        // Set up AWS SDK
        $this->client_config['credentials'] = \Aws\Credentials\CredentialProvider::env();
        $SesClient = new SesClient($this->client_config);

        $sender_email = $event['sender_email'];
        $recipient_emails[] = $event['recipient_email'];

        // Process the submitted form.
        // *TODO* This could do with more validation.
        $fields = [];
        parse_str($event['body'], $fields);

        if (!isset($fields['name'])) $fields['name'] = '{unknown name}';
        if (!isset($fields['mail'])) $fields['mail'] = '{unknown email address}';
        if (!isset($fields['subject'][0]['value'])) $fields['subject'][0]['value'] = '{unknown subject}';
        if (!isset($fields['message'][0]['value'])) $fields['message'][0]['value'] = '{unknown message}';

        $subject = '[badzilla.co.uk website feedback] '.$fields['subject'][0]['value'];
        $plaintext_body = 'From: '.$fields['name'].'  '.$fields['mail'].
            'Subject: '.$fields['subject'][0]['value'].
            ' Message: '. $fields['message'][0]['value'];
        $html_body =  '<h1>'.$fields['subject'][0]['value'].'</h1>'.
            '<h2>'.'From: '.$fields['name'].'  <a href="mailto:"'.$fields['mail'].'">'.$fields['mail'].'</a>'.'</h2>'.
            '<p>'.$fields['message'][0]['value'].'</p>';
        $char_set = 'UTF-8';

        try {
            $result = $SesClient->sendEmail([
                'Destination' => [
                    'ToAddresses' => $recipient_emails,
                ],
                'ReplyToAddresses' => [$sender_email],
                'Source' => $sender_email,
                'Message' => [
                    'Body' => [
                        'Html' => [
                            'Charset' => $char_set,
                            'Data' => $html_body,
                        ],
                        'Text' => [
                            'Charset' => $char_set,
                            'Data' => $plaintext_body,
                        ],
                    ],
                    'Subject' => [
                        'Charset' => $char_set,
                        'Data' => $subject,
                    ],
                ],
            ]);
            $messageId = $result['MessageId'];
        } catch (AwsException $e) {
            // output error message if fails
            $logger->notice('Message', $e->getMessage());
            $logger->notice('AWS Message', $e->getAwsErrorMessage());
        }

        return [
            'headers' => ['Location' => $event['headers']['Referer']],
            'statusCode' => 307,
        ];
    }
}

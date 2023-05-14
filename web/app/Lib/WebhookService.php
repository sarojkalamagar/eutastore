<?php

namespace App\Lib;

use Shopify\Clients\Graphql;

class WebhookService
{
    /**
     *--------------------------------------------------------------------------
     * Subacribe to a web hook
     *--------------------------------------------------------------------------
     *
     */

    public static function subscribe($shopName)
    {
        $accessToken = StoreService::getAccessToken($shopName);
        $client = new Graphql($shopName, $accessToken);

        $variables = [
            "topic" => "APP_UNINSTALLED",
            "webhookSubscription" => [
                "callbackUrl" => route('webhook.appUninstalled'),
                "format" => "JSON",
            ],
        ];

        $topic = 'APP_UNINSTALLED';
        $webhookSubscription = json_encode([
            "callbackUrl" => route('webhook.appUninstalled'),
            "format" => "JSON",
        ]);

        $query = <<<QUERY
        mutation webhookSubscriptionCreate($topic: WebhookSubscriptionTopic!, $webhookSubscription: WebhookSubscriptionInput!) {
            webhookSubscriptionCreate(topic: $topic, webhookSubscription: $webhookSubscription) {
            webhookSubscription {
                id
                topic
                format
                endpoint {
                __typename
                ... on WebhookHttpEndpoint {
                    callbackUrl
                }
                }
            }
            }
        }
        QUERY;

        $response = $client->query(["query" => $query, "variables" => $variables]);
        $content = $response->getBody();
        logger($content);
        return json_decode($content, true);
    }
}

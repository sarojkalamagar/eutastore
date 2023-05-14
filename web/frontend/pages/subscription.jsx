import {
    Card,
    Page,
    Layout,
    TextContainer,
    Image,
    Stack,
    Heading,
    ChoiceList,
} from "@shopify/polaris";
import { TitleBar } from "@shopify/app-bridge-react";

import { billingImage } from "../assets";
import Axios from "axios";
import { useEffect, useState } from "react";
import { useAuthenticatedFetch } from "../hooks";
import createApp from '@shopify/app-bridge';
import { Redirect } from '@shopify/app-bridge/actions';
import { useParams } from 'react-router-dom';

const config = {
    // The client ID provided for your application in the Partner Dashboard.
    apiKey: "992d3b4de307884b6401f224479f3139",
    // The host of the specific shop that's embedding your app. This value is provided by Shopify as a URL query parameter that's appended to your application URL when your app is loaded inside the Shopify admin.
    host: new URLSearchParams(location.search).get("host"),
    forceRedirect: true
};

const app = createApp(config);
const redirect = Redirect.create(app);

export default function Subscription() {

    const { requestingShop } = useParams();

    /*
    |--------------------------------------------------------------------------
    | Fetch
    |--------------------------------------------------------------------------
    |
    */

    const fetch = useAuthenticatedFetch();

    /*
    |--------------------------------------------------------------------------
    | Flag: is loading?
    |--------------------------------------------------------------------------
    |
    */

    const [isLoading, setIsLoading] = useState(false);

    /*
    |--------------------------------------------------------------------------
    | Subscription plans
    |--------------------------------------------------------------------------
    |
    */

    const [subscriptionPlans, setSubscriptionPlans] = useState([]);

    /*
    |--------------------------------------------------------------------------
    | Subscription plan choices
    |--------------------------------------------------------------------------
    |
    */

    const [subscriptionPlanChoices, setSubscriptionPlanChoices] = useState([]);

    /*
    |--------------------------------------------------------------------------
    | Subscription details
    |--------------------------------------------------------------------------
    |
    */

    const [subscriptionPlanId, setSubscriptionPlanId] = useState([]);

    /*
    |--------------------------------------------------------------------------
    | Fetch subscription plans
    |--------------------------------------------------------------------------
    |
    */

    const fetchSubscriptionPlans = () => {
        const url = import.meta.env.VITE_BASE_URL + "/api/subscription-plans";
        const headers = {
            accept: "application/json"
        };

        Axios.get(
            url,
            { headers: headers }
        )
            .then((response) => setSubscriptionPlans(response.data))
            .catch((error) => console.log(error))
    }

    /*
    |--------------------------------------------------------------------------
    | Handle subscription
    |--------------------------------------------------------------------------
    |
    */

    const handleSubscription = async () => {
        setIsLoading(true);
        const url = import.meta.env.VITE_BASE_URL + "/api/subscribe";
        const headers = {
            accept: "application/json"
        };
        const data = {
            requestingShop: requestingShop,
            subscriptionPlanId: subscriptionPlanId[0][0]
        };

        Axios.post(
            url,
            data,
            { headers: headers }
        )
            .then((response) => {
                let confirmationUrl = response.data.data.confirmationUrl;
                redirect.dispatch(Redirect.Action.REMOTE, confirmationUrl);
            })
            .catch((error) => { })
            .finally(() => setIsLoading(false));
    };

    useEffect(() => {
        fetchSubscriptionPlans();
    }, []);

    useEffect(() => {
        let newSubscriptionPlanChoices = [];
        subscriptionPlans.map(subscriptionPlan => newSubscriptionPlanChoices.push({ label: `${subscriptionPlan.name} $${subscriptionPlan.amount}`, value: [subscriptionPlan.id] }));
        setSubscriptionPlanChoices(newSubscriptionPlanChoices);
    }, [subscriptionPlans]);

    return (
        <Page narrowWidth>
            <TitleBar title="Subscription" primaryAction={null} />
            <Layout>
                <Layout.Section>
                    <Card
                        sectioned
                        primaryFooterAction={{
                            content: "Subscribe now",
                            onAction: handleSubscription,
                            loading: isLoading,
                        }}
                    >
                        <Stack
                            wrap={false}
                            spacing="extraTight"
                            distribution="trailing"
                            alignment="center"
                        >
                            <Stack.Item fill>
                                <TextContainer spacing="loose">
                                    <Heading>Please select your subscription plan to continue using the app.</Heading>
                                    <p>
                                        Subscription is available in both monthly as well as annually. You can upgrade or downgrade
                                        any time.
                                    </p>
                                    <ChoiceList
                                        title="Subscription plans"
                                        choices={subscriptionPlanChoices}
                                        selected={subscriptionPlanId}
                                        onChange={selectedChoice => setSubscriptionPlanId(selectedChoice)}
                                    />
                                </TextContainer>
                            </Stack.Item>
                            <Stack.Item>
                                <div style={{ padding: "0 20px" }}>
                                    <Image
                                        source={billingImage}
                                        alt="Subscription"
                                        width={120}
                                    />
                                </div>
                            </Stack.Item>
                        </Stack>
                    </Card>
                </Layout.Section>
            </Layout>
        </Page>
    );
}
# Eutastore - A Simple Shopify App

# Before setting up the project
-   Create partner account in Shopify at `https://accounts.shopify.com/signup`
-   Create development store
-   Create an app manually
-   Set up 'Distribution' by selecting `Shopify app store` in app's Overview section in order for the billing to work.
# Setup
-   Clone https://github.com/sarojkalamagar/eutastore.git
-   cd [PROJECT PATH]/web
-   Run command `composer install`
-   Create database
-   `cp .env.example .env`
-   Add database config to  `.env` file
-   Run command `php artisan migrate --seed`
-   `php artisan key:generate`
-   `npm install`
-   `cd ..`
-   `npm install`
-   Update .env file with CLIENT_ID, CLIENT SECRET from app's client's credentials section
-   `npm run dev`
-   Answer questions in the prompt
-   Create ngrok account `https://dashboard.ngrok.com/signup` and install ngrok
-   `ngrok config add-authtoken [NGROK AUTH TOKEN]`
-   Update web/.env and web/frontend/.env with ngrok URL dispayed on the terminal

# To use the app in the development store
-   Go to Apps sections in partner's dashboard
-   In Overview section, go to `Select store` in Test your app section
-   Click on the `Install app` link on the store you want to install the app. You will be prompted to authorization page.
-   Click on `Install unlisted app` button. You will be redirected to billing section.
-   Click on `Approve` to approve the subscription. You will be redirected to the app.

# What happens when you uninstall?
-   All data related your store will be reset.
<?php
namespace App\Services;

include_once './src/services/api_connector/responses/AffiseApiResponse.php';
include_once './src/services/ApiConnector.php';

use App\Src\Services\ApiConnector\AffiseApiResponse;

class AffiseConnector extends ApiConnector
{
    const AFFISE_API_DOMAIN = 'http://api.example.affise.com';

    // заменить массив на отдельные константы
    const AFFISE_API_URL_MAP = [
        // statistics
        'get_conversions' => '/3.0/stats/conversions',
        'get_clicks' => '/3.0/stats/clicks',
        'get_affiliate_postbacks_stats' => '/3.0/stats/affiliatepostbacks',
        'get_trafficbacks' => '/3.0/stats/getbytrafficback',
        'edit_conversions' => '/3.0/admin/conversion/edit',

        // affiliates
        'get_affiliate' => '/3.0/admin/partner/{ID}',
        'get_affiliates' => '/3.0/admin/partners',
        'add_affiliate' => '/3.0/admin/partner',
        'edit_affiliate' => '/3.0/admin/partner/{ID}',
        'edit_affiliates' => '/3.0/admin/partners/mass-update',
        'get_affiliate_postbacks' => '/3.0/admin/postbacks',
        'add_affiliate_postbacks' => '/3.0/partner/postback',
        'edit_affiliate_postback' => '/3.0/partner/postback/{ID}',
        'delete_affiliate_postback' => '/3.0/partner/postback/{ID}/remove',
        'delete_affiliate_local_postbacks' => '/3.0/partner/postbacks/by-offers',
        'get_affiliate_balance' => '/3.0/balance',
        'get_affiliate_referrals' => '/3.0/admin/partner/{ID}/referrals',
        'change_affiliate_api_key' => '/3.1/partner/api-key',
        'disable_offers_from_affiliate' => '/3.0/admin/affiliate/{ID}/disable-offers',

        // affiliates news
        'get_news' => '/3.0/news',
        'get_news_by_id' => '/3.0/news/{ID}',

        // offers
        'get_offers' => '/3.0/offers',
        'get_offer' => '/3.0/offer/{ID}',
        'get_categories' => '/3.0/offer/categories',
        'add_offer' => '/3.0/admin/offer',
        'edit_offer' => '/3.0/admin/offer/{ID}',
        'get_affiliates_on_offer' => '/3.1/offers/{ID}/privacy',
        'get_sources' => '/3.0/admin/offer/sources',
        'enable_affiliate_on_offers' => '/3.0/offer/enable-affiliate',
        'disable_affiliate_on_offers' => '/3.0/offer/disable-affiliate',
        'disable_affiliates_on_offer' => '/3.0/admin/offer/{ID}/disable-affiliates',
        'update_offer_status' => '/3.0/admin/offer/mass-update',

        // advertisers
        'get_advertisers' => '/3.0/admin/advertisers',
        'get_advertiser' => '/3.0/admin/advertiser/{ID}',

        // billing
        'get_invoices' => '/3.0/admin/advertiser-invoices',

        // users
        'get_user' => '/3.0/admin/user/{ID}',
        'get_users' => '/3.0/admin/users',
        'add_user' => '/3.0/admin/user',
        'edit_user' => '/3.0/admin/user/{ID}',
        'change_user_api_key' => '/3.0/admin/user/api_key/{ID}',
        'update_user_permissions' => '/3.1/user/{ID}/permissions',
        'public_auth' => '/3.1/pub-auth',

        // entities
        'get_devices' => '/3.1/devices',
        'get_payment_systems' => '/3.0/admin/payment_systems',
        'get_custom_fields_for_affiliates' => '/3.0/admin/custom_fields',
        'get_tracking_domains' => '/3.0/admin/domains',
        'get_countries' => '/3.1/countries',
        'get_oses' => '/3.1/oses'
    ];

    const HTTP_HEADER = [
        'API-Key: afpo871kovmmwk6movwm43kr0k03',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'
    ];

    public function getAffiliates(array $query, int $page = 1): AffiseApiResponse
    {
        $query['page'] = $page;

        $url = $this->prepareUrl('get_affiliates', $query);

        return new AffiseApiResponse($this->requestWithRetrying($url));
    }

    public function getConversions(array $query, int $page = 1): AffiseApiResponse
    {
        $query['page'] = $page;

        $url = $this->prepareUrl('get_conversions', $query);

        return new AffiseApiResponse($this->requestWithRetrying($url));
    }

    public function editConversions(array $data): AffiseApiResponse
    {
        $url = $this->prepareUrl('edit_conversions');

        return new AffiseApiResponse($this->requestWithRetrying($url, $data));
    }

    public function getOffer(int $id): AffiseApiResponse
    {
        $macros = ['{ID}' => $id];

        $url = $this->prepareUrl('get_offer', null, $macros);

        return new AffiseApiResponse($this->requestWithRetrying($url));
    }

    public function getOffers(array $query, int $page = 1): AffiseApiResponse
    {
        $query['page'] = $page;

        $url = $this->prepareUrl('get_offers', $query);

        return new AffiseApiResponse($this->requestWithRetrying($url));
    }

    public function editOffer(int $id, array $data): AffiseApiResponse
    {
        $macros = ['{ID}' => $id];

        $url = $this->prepareUrl('edit_offer', null, $macros);

        return new AffiseApiResponse($this->requestWithRetrying($url, $data));
    }

    protected function prepareUrl(string $method, array $query = null, array $macros = null): string
    {
        $url = self::AFFISE_API_DOMAIN . self::AFFISE_API_URL_MAP[$method];

        if (isset($query)) {
            $url .= '?' . http_build_query($query);
        }

        if (isset($macros)) {
            $url = $this->replaceMacrosInUrl($url, $macros);
        }

        return $url;
    }

    private function replaceMacrosInUrl(string $url, array $macros): string
    {
        foreach ($macros as $macro => $value) {
            if ($urlReplaced = str_replace($macro, $value, $url)) {
                $url = $urlReplaced;
            }
        }

        return $url;
    }
}

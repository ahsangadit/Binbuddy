<?php

/**
 * Copyright (c) 2015-present, Facebook, Inc. All rights reserved.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Facebook.
 *
 * As with any software that integrates with the Facebook platform, your use
 * of this software is subject to the Facebook Developer Principles and
 * Policies [http://developers.facebook.com/policy/]. This copyright notice
 * shall be included in all copies or substantial portions of the software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 */
namespace PYS_PRO_GLOBAL\FacebookAds\Object;

use PYS_PRO_GLOBAL\FacebookAds\ApiRequest;
use PYS_PRO_GLOBAL\FacebookAds\Cursor;
use PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface;
use PYS_PRO_GLOBAL\FacebookAds\TypeChecker;
use PYS_PRO_GLOBAL\FacebookAds\Object\Fields\ApplicationFields;
use PYS_PRO_GLOBAL\FacebookAds\Object\Values\AdNetworkAnalyticsSyncQueryResultAggregationPeriodValues;
use PYS_PRO_GLOBAL\FacebookAds\Object\Values\AdNetworkAnalyticsSyncQueryResultBreakdownsValues;
use PYS_PRO_GLOBAL\FacebookAds\Object\Values\AdNetworkAnalyticsSyncQueryResultMetricsValues;
use PYS_PRO_GLOBAL\FacebookAds\Object\Values\AdNetworkAnalyticsSyncQueryResultOrderingColumnValues;
use PYS_PRO_GLOBAL\FacebookAds\Object\Values\AdNetworkAnalyticsSyncQueryResultOrderingTypeValues;
use PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationAnPlatformsValues;
use PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationLoggingSourceValues;
use PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationLoggingTargetValues;
use PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationMutationMethodValues;
use PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationPlatformValues;
use PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationPostMethodValues;
use PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationRequestTypeValues;
use PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationScoreTypeValues;
use PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationSortOrderValues;
use PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationSupportedPlatformsValues;
use PYS_PRO_GLOBAL\FacebookAds\Object\Values\DACheckConnectionMethodValues;
use PYS_PRO_GLOBAL\FacebookAds\Object\Values\EventTypeValues;
/**
 * This class is auto-generated.
 *
 * For any issues or feature requests related to this class, please let us know
 * on github and we'll fix in our codegen framework. We'll not be able to accept
 * pull request for this class.
 *
 */
class Application extends \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject
{
    /**
     * @return ApplicationFields
     */
    public static function getFieldsEnum()
    {
        return \PYS_PRO_GLOBAL\FacebookAds\Object\Fields\ApplicationFields::getInstance();
    }
    protected static function getReferencedEnums()
    {
        $ref_enums = array();
        $ref_enums['SupportedPlatforms'] = \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationSupportedPlatformsValues::getInstance()->getValues();
        $ref_enums['AnPlatforms'] = \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationAnPlatformsValues::getInstance()->getValues();
        $ref_enums['Platform'] = \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationPlatformValues::getInstance()->getValues();
        $ref_enums['RequestType'] = \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationRequestTypeValues::getInstance()->getValues();
        $ref_enums['MutationMethod'] = \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationMutationMethodValues::getInstance()->getValues();
        $ref_enums['PostMethod'] = \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationPostMethodValues::getInstance()->getValues();
        $ref_enums['ScoreType'] = \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationScoreTypeValues::getInstance()->getValues();
        $ref_enums['SortOrder'] = \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationSortOrderValues::getInstance()->getValues();
        $ref_enums['LoggingSource'] = \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationLoggingSourceValues::getInstance()->getValues();
        $ref_enums['LoggingTarget'] = \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationLoggingTargetValues::getInstance()->getValues();
        return $ref_enums;
    }
    public function deleteAccounts(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('type' => 'type_enum', 'uid' => 'int');
        $enums = array('type_enum' => array('test-users'));
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_DELETE, '/accounts', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getAccounts(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('type' => 'type_enum');
        $enums = array('type_enum' => array('test-users'));
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/accounts', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createAccount(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('installed' => 'bool', 'minor' => 'bool', 'name' => 'string', 'owner_access_token' => 'string', 'permissions' => 'list<Permission>', 'type' => 'type_enum', 'uid' => 'int');
        $enums = array('type_enum' => array('test-users'));
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/accounts', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createActivity(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('advertiser_id' => 'string', 'advertiser_tracking_enabled' => 'bool', 'anon_id' => 'string', 'app_user_id' => 'string', 'application_tracking_enabled' => 'bool', 'attribution' => 'string', 'auto_publish' => 'bool', 'bundle_id' => 'string', 'bundle_short_version' => 'string', 'bundle_version' => 'string', 'click_id' => 'string', 'consider_views' => 'bool', 'custom_events' => 'list<Object>', 'custom_events_file' => 'file', 'data_processing_options' => 'list<string>', 'data_processing_options_country' => 'unsigned int', 'data_processing_options_state' => 'unsigned int', 'device_token' => 'string', 'event' => 'event_enum', 'extinfo' => 'Object', 'include_dwell_data' => 'bool', 'include_video_data' => 'bool', 'install_referrer' => 'string', 'install_timestamp' => 'unsigned int', 'installer_package' => 'string', 'limited_data_use' => 'bool', 'migration_bundle' => 'string', 'page_id' => 'unsigned int', 'page_scoped_user_id' => 'unsigned int', 'receipt_data' => 'string', 'ud' => 'map', 'url_schemes' => 'list<string>', 'user_id' => 'string', 'user_id_type' => 'user_id_type_enum', 'windows_attribution_id' => 'string');
        $enums = array('event_enum' => array('CUSTOM_APP_EVENTS', 'DEFERRED_APP_LINK', 'MOBILE_APP_INSTALL'), 'user_id_type_enum' => array('INSTANT_GAMES_PLAYER_ID'));
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/activities', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getAdNetworkAnalytics(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('aggregation_period' => 'aggregation_period_enum', 'breakdowns' => 'list<breakdowns_enum>', 'filters' => 'list<map>', 'limit' => 'unsigned int', 'metrics' => 'list<metrics_enum>', 'ordering_column' => 'ordering_column_enum', 'ordering_type' => 'ordering_type_enum', 'since' => 'datetime', 'until' => 'datetime');
        $enums = array('aggregation_period_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\AdNetworkAnalyticsSyncQueryResultAggregationPeriodValues::getInstance()->getValues(), 'breakdowns_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\AdNetworkAnalyticsSyncQueryResultBreakdownsValues::getInstance()->getValues(), 'metrics_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\AdNetworkAnalyticsSyncQueryResultMetricsValues::getInstance()->getValues(), 'ordering_column_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\AdNetworkAnalyticsSyncQueryResultOrderingColumnValues::getInstance()->getValues(), 'ordering_type_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\AdNetworkAnalyticsSyncQueryResultOrderingTypeValues::getInstance()->getValues());
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/adnetworkanalytics', new \PYS_PRO_GLOBAL\FacebookAds\Object\AdNetworkAnalyticsSyncQueryResult(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\AdNetworkAnalyticsSyncQueryResult::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createAdNetworkAnalytic(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('aggregation_period' => 'aggregation_period_enum', 'breakdowns' => 'list<breakdowns_enum>', 'filters' => 'list<Object>', 'limit' => 'int', 'metrics' => 'list<metrics_enum>', 'ordering_column' => 'ordering_column_enum', 'ordering_type' => 'ordering_type_enum', 'since' => 'datetime', 'until' => 'datetime');
        $enums = array('aggregation_period_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\AdNetworkAnalyticsSyncQueryResultAggregationPeriodValues::getInstance()->getValues(), 'breakdowns_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\AdNetworkAnalyticsSyncQueryResultBreakdownsValues::getInstance()->getValues(), 'metrics_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\AdNetworkAnalyticsSyncQueryResultMetricsValues::getInstance()->getValues(), 'ordering_column_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\AdNetworkAnalyticsSyncQueryResultOrderingColumnValues::getInstance()->getValues(), 'ordering_type_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\AdNetworkAnalyticsSyncQueryResultOrderingTypeValues::getInstance()->getValues());
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/adnetworkanalytics', new \PYS_PRO_GLOBAL\FacebookAds\Object\Application(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\Application::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getAdNetworkAnalyticsResults(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('query_ids' => 'list<string>');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/adnetworkanalytics_results', new \PYS_PRO_GLOBAL\FacebookAds\Object\AdNetworkAnalyticsAsyncQueryResult(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\AdNetworkAnalyticsAsyncQueryResult::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getAgencies(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array();
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/agencies', new \PYS_PRO_GLOBAL\FacebookAds\Object\Business(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\Business::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createAggregateRevenue(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('ecpms' => 'list<string>', 'query_ids' => 'list<string>', 'request_id' => 'string');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/aggregate_revenue', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getAndroidDialogConfigs(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array();
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/android_dialog_configs', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getAppEventTypes(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array();
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/app_event_types', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createAppIndexing(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('app_version' => 'string', 'device_session_id' => 'string', 'extra_info' => 'string', 'platform' => 'platform_enum', 'request_type' => 'request_type_enum', 'tree' => 'map');
        $enums = array('platform_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationPlatformValues::getInstance()->getValues(), 'request_type_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationRequestTypeValues::getInstance()->getValues());
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/app_indexing', new \PYS_PRO_GLOBAL\FacebookAds\Object\Application(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\Application::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createAppIndexingSession(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('device_session_id' => 'string', 'extinfo' => 'string');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/app_indexing_session', new \PYS_PRO_GLOBAL\FacebookAds\Object\Application(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\Application::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getAppInsights(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('aggregateBy' => 'aggregateBy_enum', 'breakdowns' => 'list<string>', 'ecosystem' => 'ecosystem_enum', 'event_name' => 'string', 'intervals_to_aggregate' => 'int', 'metric_key' => 'string', 'period' => 'period_enum', 'since' => 'datetime', 'until' => 'datetime');
        $enums = array('aggregateBy_enum' => array('AVERAGE_JOURNEY_LENGTH', 'CONVERTED_JOURNEY_PERCENT', 'COUNT', 'COUNT_IDENTIFIED_USERS', 'COUNT_PER_USER', 'DAU', 'EVENT_LATEST_FIRE_TIME', 'EVENT_SOURCE_IDS', 'JOURNEY_CHANNEL_INCLUSION', 'JOURNEY_INCLUSION', 'MAU', 'MEDIAN_JOURNEY_LENGTH', 'MEDIAN_VALUE', 'MEDIAN_VALUE_PER_USER', 'OVERLAP', 'PERCENTILES_COUNT', 'PERCENTILES_USD_VALUE', 'PERCENTILES_VALUE', 'SCORE', 'SESSIONS_PER_JOURNEY', 'SESSION_BOUNCE_RATE', 'SUM', 'SUM_IDENTIFIED_USERS', 'SUM_PER_EVENT', 'TOPK', 'UNKNOWN_USERS', 'USD_SUM', 'USD_SUM_IDENTIFIED_USERS', 'USD_SUM_PER_EVENT', 'USD_SUM_PER_USER', 'USD_VALUE_PER_USER', 'USERS', 'USER_PROPERTY_USER_COUNT', 'VALUE_PER_USER', 'WAU'), 'ecosystem_enum' => array('GAME', 'NON_GAME'), 'period_enum' => array('daily', 'days_28', 'days_60', 'days_90', 'hourly', 'lifetime', 'mins_15', 'monthly', 'range', 'weekly'));
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/app_insights', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getAppInstalledGroups(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('group_id' => 'string');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/app_installed_groups', new \PYS_PRO_GLOBAL\FacebookAds\Object\Group(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\Group::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createAppPushDeviceToken(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('device_id' => 'string', 'device_token' => 'string', 'platform' => 'platform_enum');
        $enums = array('platform_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationPlatformValues::getInstance()->getValues());
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/app_push_device_token', new \PYS_PRO_GLOBAL\FacebookAds\Object\Application(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\Application::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getAppAssets(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array();
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/appassets', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createAsset(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('asset' => 'file', 'comment' => 'string', 'type' => 'string');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/assets', new \PYS_PRO_GLOBAL\FacebookAds\Object\Application(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\Application::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getAuthorizedAdAccounts(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('business' => 'string');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/authorized_adaccounts', new \PYS_PRO_GLOBAL\FacebookAds\Object\AdAccount(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\AdAccount::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function deleteBanned(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('uids' => 'list<int>');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_DELETE, '/banned', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getButtonAutoDetectionDeviceSelection(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('device_id' => 'string');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/button_auto_detection_device_selection', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createButtonIndexing(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('app_version' => 'string', 'device_id' => 'string', 'extinfo' => 'string', 'indexed_button_list' => 'list<map>');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/button_indexing', new \PYS_PRO_GLOBAL\FacebookAds\Object\Application(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\Application::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createCodelessEventMapping(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('mappings' => 'list<map>', 'mutation_method' => 'mutation_method_enum', 'platform' => 'platform_enum', 'post_method' => 'post_method_enum');
        $enums = array('mutation_method_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationMutationMethodValues::getInstance()->getValues(), 'platform_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationPlatformValues::getInstance()->getValues(), 'post_method_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationPostMethodValues::getInstance()->getValues());
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/codeless_event_mappings', new \PYS_PRO_GLOBAL\FacebookAds\Object\Application(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\Application::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getDaChecks(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('checks' => 'list<string>', 'connection_method' => 'connection_method_enum');
        $enums = array('connection_method_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\DACheckConnectionMethodValues::getInstance()->getValues());
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/da_checks', new \PYS_PRO_GLOBAL\FacebookAds\Object\DACheck(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\DACheck::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getEvents(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('include_canceled' => 'bool', 'type' => 'type_enum');
        $enums = array('type_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\EventTypeValues::getInstance()->getValues());
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/events', new \PYS_PRO_GLOBAL\FacebookAds\Object\Event(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\Event::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getInsightsPushSchedule(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array();
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/insights_push_schedule', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createInsightsPushSchedule(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('ad_account_ids' => 'list', 'breakdowns' => 'list<string>', 'date_preset' => 'string', 'level' => 'level_enum', 'metrics' => 'list<string>', 'object_id' => 'string', 'owner_id' => 'int', 'schedule' => 'schedule_enum', 'status' => 'status_enum', 'time_increment' => 'unsigned int', 'time_start' => 'datetime', 'time_stop' => 'datetime');
        $enums = array('level_enum' => array('ACCOUNT', 'AD', 'ADSET', 'CAMPAIGN'), 'schedule_enum' => array('DAILY', 'FINE_15_MIN', 'FINE_5_MIN', 'MONTHLY', 'WEEKLY'), 'status_enum' => array('ACTIVE', 'DISABLED', 'ERROR'));
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/insights_push_schedule', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getIosDialogConfigs(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array();
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/ios_dialog_configs', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createLeaderboardsCreate(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('context_id' => 'string', 'decimal_offset' => 'unsigned int', 'name' => 'string', 'score_type' => 'score_type_enum', 'sort_order' => 'sort_order_enum', 'unit' => 'string');
        $enums = array('score_type_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationScoreTypeValues::getInstance()->getValues(), 'sort_order_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationSortOrderValues::getInstance()->getValues());
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/leaderboards_create', new \PYS_PRO_GLOBAL\FacebookAds\Object\Application(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\Application::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createLeaderboardsDeleteEntry(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('name' => 'string', 'player_id' => 'string');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/leaderboards_delete_entry', new \PYS_PRO_GLOBAL\FacebookAds\Object\Application(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\Application::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createLeaderboardsReset(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('name' => 'string', 'reset_time' => 'datetime');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/leaderboards_reset', new \PYS_PRO_GLOBAL\FacebookAds\Object\Application(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\Application::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createLeaderboardsSetScore(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('extra_data' => 'string', 'name' => 'string', 'player_id' => 'string', 'score' => 'unsigned int');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/leaderboards_set_score', new \PYS_PRO_GLOBAL\FacebookAds\Object\Application(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\Application::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createMmpAuditing(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('advertiser_id' => 'string', 'attribution' => 'string', 'attribution_model' => 'string', 'auditing_token' => 'string', 'click_attr_window' => 'unsigned int', 'custom_events' => 'list<Object>', 'decline_reason' => 'string', 'event' => 'string', 'event_reported_time' => 'unsigned int', 'fb_ad_id' => 'unsigned int', 'fb_click_time' => 'unsigned int', 'fb_view_time' => 'unsigned int', 'is_fb' => 'bool', 'view_attr_window' => 'unsigned int');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/mmp_auditing', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getMobileSdkGk(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('device_id' => 'string', 'extinfo' => 'Object', 'os_version' => 'string', 'platform' => 'platform_enum', 'sdk_version' => 'string');
        $enums = array('platform_enum' => array('ANDROID', 'IOS'));
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/mobile_sdk_gk', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createOccludesPopup(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('flash' => 'bool', 'unity' => 'bool');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/occludespopups', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createPageActivity(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('advertiser_tracking_enabled' => 'bool', 'application_tracking_enabled' => 'bool', 'custom_events' => 'list<Object>', 'logging_source' => 'logging_source_enum', 'logging_target' => 'logging_target_enum', 'page_id' => 'unsigned int', 'page_scoped_user_id' => 'unsigned int');
        $enums = array('logging_source_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationLoggingSourceValues::getInstance()->getValues(), 'logging_target_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationLoggingTargetValues::getInstance()->getValues());
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/page_activities', new \PYS_PRO_GLOBAL\FacebookAds\Object\Application(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\Application::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function deletePaymentCurrencies(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('currency_url' => 'string');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_DELETE, '/payment_currencies', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createPaymentCurrency(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('currency_url' => 'string');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/payment_currencies', new \PYS_PRO_GLOBAL\FacebookAds\Object\Application(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\Application::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getPermissions(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('android_key_hash' => 'string', 'ios_bundle_id' => 'string', 'permission' => 'list<Permission>', 'proxied_app_id' => 'int', 'status' => 'list<status_enum>');
        $enums = array('status_enum' => array('live', 'unapproved'));
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/permissions', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getProducts(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('product_ids' => 'list<string>');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/products', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getPurchases(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('is_premium' => 'bool');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/purchases', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getRoles(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array();
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/roles', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getSubscribedDomains(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array();
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/subscribed_domains', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createSubscribedDomain(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('subscribe' => 'list<string>', 'unsubscribe' => 'list<string>');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/subscribed_domains', new \PYS_PRO_GLOBAL\FacebookAds\Object\Application(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\Application::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getSubscribedDomainsPhishing(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array();
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/subscribed_domains_phishing', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createSubscribedDomainsPhishing(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('subscribe' => 'list<string>', 'unsubscribe' => 'list<string>');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/subscribed_domains_phishing', new \PYS_PRO_GLOBAL\FacebookAds\Object\Application(), 'EDGE', \PYS_PRO_GLOBAL\FacebookAds\Object\Application::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function deleteSubscriptions(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('fields' => 'list<string>', 'object' => 'string');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_DELETE, '/subscriptions', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createSubscription(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('callback_url' => 'string', 'fields' => 'list<string>', 'include_values' => 'bool', 'object' => 'string', 'verify_token' => 'string');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/subscriptions', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createUpload(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('file_length' => 'unsigned int', 'file_name' => 'Object', 'file_type' => 'Object', 'session_type' => 'session_type_enum');
        $enums = array('session_type_enum' => array('attachment'));
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/uploads', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createUserProperty(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('data' => 'list<Object>', 'limited_data_use' => 'bool');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/user_properties', new \PYS_PRO_GLOBAL\FacebookAds\Object\AbstractCrudObject(), 'EDGE', array(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getSelf(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('advertiser_id' => 'string');
        $enums = array();
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_GET, '/', new \PYS_PRO_GLOBAL\FacebookAds\Object\Application(), 'NODE', \PYS_PRO_GLOBAL\FacebookAds\Object\Application::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function updateSelf(array $fields = array(), array $params = array(), $pending = \false)
    {
        $this->assureId();
        $param_types = array('an_platforms' => 'list<an_platforms_enum>', 'android_class_name' => 'string', 'android_key_hashes' => 'list<string>', 'android_package_name' => 'string', 'android_sso' => 'bool', 'app_domains' => 'list<string>', 'app_name' => 'string', 'app_type' => 'bool', 'auth_dialog_headline' => 'string', 'auth_dialog_perms_explanation' => 'string', 'auth_referral_default_activity_privacy' => 'string', 'auth_referral_enabled' => 'bool', 'auth_referral_extended_perms' => 'list<string>', 'auth_referral_friend_perms' => 'list<string>', 'auth_referral_response_type' => 'string', 'auth_referral_user_perms' => 'list<string>', 'canvas_fluid_height' => 'bool', 'canvas_fluid_width' => 'bool', 'canvas_url' => 'string', 'contact_email' => 'string', 'deauth_callback_url' => 'string', 'ios_bundle_id' => 'list<string>', 'mobile_web_url' => 'string', 'namespace' => 'string', 'page_tab_default_name' => 'string', 'privacy_policy_url' => 'string', 'restrictions' => 'string', 'secure_canvas_url' => 'string', 'secure_page_tab_url' => 'string', 'server_ip_whitelist' => 'list<string>', 'terms_of_service_url' => 'string', 'url_scheme_suffix' => 'string', 'user_support_email' => 'string', 'user_support_url' => 'string', 'website_url' => 'string');
        $enums = array('an_platforms_enum' => \PYS_PRO_GLOBAL\FacebookAds\Object\Values\ApplicationAnPlatformsValues::getInstance()->getValues());
        $request = new \PYS_PRO_GLOBAL\FacebookAds\ApiRequest($this->api, $this->data['id'], \PYS_PRO_GLOBAL\FacebookAds\Http\RequestInterface::METHOD_POST, '/', new \PYS_PRO_GLOBAL\FacebookAds\Object\Application(), 'NODE', \PYS_PRO_GLOBAL\FacebookAds\Object\Application::getFieldsEnum()->getValues(), new \PYS_PRO_GLOBAL\FacebookAds\TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
}

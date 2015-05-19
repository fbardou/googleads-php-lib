<?php
/**
 * This example updates an activity group's companies. To determine which
 * activity groups exist, run GetAllActivityGroups.php.
 *
 * Tags: ActivityGroupService.getActivityGroupsByStatement
 * Tags: ActivityGroupService.updateActivityGroups
 *
 * PHP version 5
 *
 * Copyright 2014, Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package    GoogleApiAdsDfp
 * @subpackage v201505
 * @category   WebServices
 * @copyright  2014, Google Inc. All Rights Reserved.
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License,
 *             Version 2.0
 * @author     Vincent Tsao
 */
error_reporting(E_STRICT | E_ALL);

// You can set the include path to src directory or reference
// DfpUser.php directly via require_once.
// $path = '/path/to/dfp_api_php_lib/src';
$path = dirname(__FILE__) . '/../../../../src';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'Google/Api/Ads/Dfp/Lib/DfpUser.php';
require_once 'Google/Api/Ads/Dfp/Util/v201505/StatementBuilder.php';
require_once dirname(__FILE__) . '/../../../Common/ExampleUtils.php';

// Set the ID of the activity group to update.
$activityGroupId = 'INSERT_ACTIVITY_GROUP_ID_HERE';

// Set the ID of the company to associate with the activity group.
$advertiserCompanyId = 'INSERT_ADVERTISER_COMPANY_ID_HERE';

try {
  // Get DfpUser from credentials in "../auth.ini"
  // relative to the DfpUser.php file's directory.
  $user = new DfpUser();

  // Log SOAP XML request and response.
  $user->LogDefaults();

  // Get the ActivityGroupService.
  $activityGroupService = $user->GetService('ActivityGroupService', 'v201505');

  // Create a statement to select a single activity group by ID.
  $statementBuilder = new StatementBuilder();
  $statementBuilder->Where('id = :id')
      ->OrderBy('id ASC')
      ->Limit(1)
      ->WithBindVariableValue('id', $activityGroupId);

  // Get the activity group.
  $page = $activityGroupService->getActivityGroupsByStatement(
      $statementBuilder->ToStatement());
  $activityGroup = $page->results[0];

  // Update the companies.
  $activityGroup->companyIds[] = $advertiserCompanyId;

  // Update the activity group on the server.
  $activityGroups =
      $activityGroupService->updateActivityGroups(array($activityGroup));

  foreach ($activityGroups as $updatedActivityGroup) {
    printf("Activity group with ID %d, and name '%s' was updated.\n",
        $updatedActivityGroup->id, $updatedActivityGroup->name);
  }
} catch (OAuth2Exception $e) {
  ExampleUtils::CheckForOAuth2Errors($e);
} catch (ValidationException $e) {
  ExampleUtils::CheckForOAuth2Errors($e);
} catch (Exception $e) {
  printf("%s\n", $e->getMessage());
}

<?php

namespace Botble\JobBoard\Facades;

use Botble\JobBoard\Supports\JobBoardHelper as JobBoardHelperSupport;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool isGuestApplyEnabled()
 * @method static bool isRegisterEnabled()
 * @method static int jobExpiredDays()
 * @method static bool isEnabledCreditsSystem()
 * @method static bool isEnabledJobApproval()
 * @method static string getThousandSeparatorForInputMask()
 * @method static string getDecimalSeparatorForInputMask()
 * @method static array getJobDisplayQueryConditions()
 * @method static array postedDateRanges()
 * @method static string getAssetVersion()
 * @method static string viewPath(string $view)
 * @method static \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application view(string $view, array $data = [])
 * @method static \Illuminate\Http\Response|string scope(string $view, array $data = [])
 * @method static string|null getJobsPageURL()
 * @method static string|null getJobCategoriesPageURL()
 * @method static string|null getJobCompaniesPageURL()
 * @method static string|null getJobCandidatesPageURL()
 * @method static array getJobFilters(\Illuminate\Http\Request|array $inputs)
 * @method static array getCompanyFilterParams(\Illuminate\Http\Request|array $inputs)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterCandidates(array $params)
 * @method static array getSortByParams()
 * @method static array getPerPageParams()
 * @method static bool isEnabledReview()
 * @method static bool isDisabledPublicProfile()
 * @method static array getMapCenterLatLng()
 * @method static bool isZipCodeEnabled()
 * @method static bool hideCompanyEmailEnabled()
 * @method static int getJobMaxPrice()
 * @method static void clearJobMaxPriceCache()
 * @method static \Illuminate\Database\Eloquent\Collection jobCategoriesForFilter(array $data = [])
 * @method static \Illuminate\Database\Eloquent\Collection jobTypesForFilter(array $data = [])
 * @method static \Illuminate\Database\Eloquent\Collection jobExperiencesForFilter(array $data = [])
 * @method static \Illuminate\Database\Eloquent\Collection jobSkillsForFilter(array $data = [])
 * @method static array dataForFilter(array $data = [])
 * @method static string getMapTileLayer()
 * @method static bool isEnabledCustomFields()
 * @method static bool employerCreateMultipleCompanies()
 * @method static bool employerManageCompanyInfo()
 * @method static void useCategoryIconImage()
 * @method static bool isEnabledEmailVerification()
 *
 * @see \Botble\JobBoard\Supports\JobBoardHelper
 */
class JobBoardHelper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return JobBoardHelperSupport::class;
    }
}

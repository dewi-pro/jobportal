<?php

namespace Botble\PluginManagement\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Breadcrumb;
use Botble\PluginManagement\Events\RenderingPluginListingPage;
use Botble\PluginManagement\Services\MarketplaceService;
use Botble\PluginManagement\Services\PluginService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PluginManagementController extends BaseController
{
    public function __construct(protected PluginService $pluginService)
    {
    }

    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('packages/plugin-management::plugin.plugins'), route('plugins.index'))
            ->add(trans('packages/plugin-management::plugin.installed_plugins'), route('plugins.index'));
    }

    public function index(): View
    {
        $this->pageTitle(trans('packages/plugin-management::plugin.installed_plugins'));

        Assets::addScriptsDirectly('vendor/core/packages/plugin-management/js/plugin.js');

        RenderingPluginListingPage::dispatch();

        $plugins = [];

        if (File::exists(plugin_path('.DS_Store'))) {
            File::delete(plugin_path('.DS_Store'));
        }

        $paths = BaseHelper::scanFolder(plugin_path());

        if (! empty($paths)) {
            $installed = get_active_plugins();
            foreach ($paths as $path) {
                $pluginPath = plugin_path($path);

                if (File::exists($pluginPath . '/.DS_Store')) {
                    File::delete($pluginPath . '/.DS_Store');
                }

                if (! File::isDirectory($pluginPath) || ! File::exists($pluginPath . '/plugin.json')) {
                    continue;
                }

                $content = BaseHelper::getFileData($pluginPath . '/plugin.json');

                if (! empty($content)) {
                    $content = [
                        ...$content,
                        'status' => in_array($path, $installed),
                        'path' => $path,
                        'image' => null,
                    ];

                    $screenshot = 'vendor/core/plugins/' . $path . '/screenshot.png';

                    if (File::exists(public_path($screenshot))) {
                        $content['image'] = asset($screenshot);
                    } elseif (File::exists($pluginPath . '/screenshot.png')) {
                        $content['image'] = 'data:image/png;base64,' . base64_encode(File::get($pluginPath . '/screenshot.png'));
                    }

                    $plugins[] = (object) $content;
                }
            }
        }

        return view('packages/plugin-management::index', compact('plugins'));
    }

    public function update(Request $request): BaseHttpResponse
    {
        $plugin = strtolower($request->input('name'));

        if (! $this->pluginService->validatePlugin($plugin)) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('packages/plugin-management::plugin.invalid_plugin'));
        }

        try {
            $activatedPlugins = get_active_plugins();

            if ($status = (! in_array($plugin, $activatedPlugins))) {
                $result = $this->pluginService->activate($plugin);
            } else {
                $result = $this->pluginService->deactivate($plugin);
            }

            if ($result['error']) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage($result['message']);
            }

            return $this
                ->httpResponse()
                ->setData(['status' => $status ? 'activated' : 'deactivated'])
                ->setMessage(trans('packages/plugin-management::plugin.update_plugin_status_success'));
        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function destroy(string $plugin): BaseHttpResponse
    {
        $plugin = strtolower($plugin);

        try {
            $result = $this->pluginService->remove($plugin);

            if ($result['error']) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage($result['message']);
            }

            return $this
                ->httpResponse()
                ->setMessage(trans('packages/plugin-management::plugin.remove_plugin_success'));
        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function checkRequirement(Request $request, MarketplaceService $marketplaceService): BaseHttpResponse
    {
        $name = strtolower($request->input('name'));

        $requiredPlugins = $this->pluginService->getDependencies($name);

        if (! empty($requiredPlugins)) {
            $content = $this->pluginService->getPluginInfo($name);

            $data = $marketplaceService->callApi('POST', '/products/check-update', [
                'products' => collect($requiredPlugins)->mapWithKeys(fn ($item) => [$item => '0.0.0'])->toArray(),
            ])->json('data');

            $existingPluginsOnMarketplace = collect($data)->pluck('id')->all();

            if (empty($existingPluginsOnMarketplace)) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(trans('packages/plugin-management::plugin.missing_required_plugins', [
                        'plugins' => implode(',', $requiredPlugins),
                    ]));
            }

            return $this
                ->httpResponse()
                ->setError()
                ->setData([
                    'pluginName' => $content['id'],
                    'existing_plugins_on_marketplace' => $existingPluginsOnMarketplace,
                ])
                ->setMessage(__('packages/plugin-management::plugin.requirement_not_met', [
                    'plugin' => "<strong>{$content['name']}</strong>",
                    'required_plugins' => '<strong>' . implode(', ', $requiredPlugins) . '</strong>',
                ]));
        }

        return $this->httpResponse();
    }
}

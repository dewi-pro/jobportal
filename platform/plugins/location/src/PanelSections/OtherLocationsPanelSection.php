<?php

namespace Botble\Location\PanelSections;

use Botble\Base\PanelSections\PanelSection;
use Botble\Base\PanelSections\PanelSectionItem;

class OtherLocationsPanelSection extends PanelSection
{
    public function setup(): void
    {
        $this
            ->setId('others.location')
            ->setTitle(trans('plugins/location::location.name'))
            ->withPriority(1100)
            ->withItems([
                PanelSectionItem::make('location.country')
                    ->setTitle(trans('plugins/location::country.panel.title'))
                    ->withIcon('ti ti-world')
                    ->withDescription(trans('plugins/location::country.panel.description'))
                    ->withPriority(10)
                    ->withRoute('country.index'),
                PanelSectionItem::make('location.state')
                    ->setTitle(trans('plugins/location::state.panel.title'))
                    ->withIcon('ti ti-world')
                    ->withDescription(trans('plugins/location::state.panel.description'))
                    ->withPriority(20)
                    ->withRoute('state.index'),
                PanelSectionItem::make('location.city')
                    ->setTitle(trans('plugins/location::city.panel.title'))
                    ->withIcon('ti ti-world')
                    ->withDescription(trans('plugins/location::city.panel.description'))
                    ->withPriority(30)
                    ->withRoute('city.index'),
                PanelSectionItem::make('location.bulk-import')
                    ->setTitle(trans('plugins/location::bulk-import.panel.title'))
                    ->withIcon('ti ti-file-import')
                    ->withDescription(trans('plugins/location::bulk-import.panel.description'))
                    ->withPriority(40)
                    ->withRoute('location.bulk-import.index'),
                PanelSectionItem::make('location.export_location')
                    ->setTitle(trans('plugins/location::export.panel.title'))
                    ->withIcon('ti ti-file-export')
                    ->withDescription(trans('plugins/location::export.panel.description'))
                    ->withPriority(50)
                    ->withRoute('location.export.index'),
            ]);
    }
}

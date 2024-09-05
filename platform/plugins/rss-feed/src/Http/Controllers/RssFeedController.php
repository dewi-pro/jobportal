<?php

namespace Botble\RssFeed\Http\Controllers;

use Botble\Base\Facades\BaseHelper;
use Botble\Blog\Models\Post;
use Botble\Media\Facades\RvMedia;
use Botble\RssFeed\Facades\RssFeed;
use Botble\RssFeed\FeedItem;
use Botble\Theme\Http\Controllers\PublicController;

class RssFeedController extends PublicController
{
    public function getPostFeeds()
    {
        if (! is_plugin_active('blog')) {
            abort(404);
        }

        $data = Post::query()
            ->wherePublished()
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        $feedItems = collect();

        foreach ($data as $item) {
            if (! $item instanceof Post) {
                continue;
            }

            $imageURL = RvMedia::getImageUrl($item->image, null, false, RvMedia::getDefaultImage());

            $category = $item->categories()->value('name');

            $author = (string) $item->author?->name;

            $feedItem = FeedItem::create()
                ->id($item->getKey())
                ->title(BaseHelper::clean($item->name))
                ->summary(BaseHelper::clean($item->description ?: $item->name))
                ->updated($item->updated_at)
                ->enclosure($imageURL)
                ->enclosureType(RvMedia::getMimeType(RvMedia::getRealPath($item->image ?: RvMedia::getDefaultImage())))
                ->enclosureLength(RssFeed::remoteFilesize($imageURL))
                ->when($category, fn (FeedItem $feedItem) => $feedItem->category($category))
                ->link((string) $item->url)
                ->when(! empty($author), function (FeedItem $feedItem) use ($item, $author) {
                    if (method_exists($feedItem, 'author')) {
                        return $feedItem->author($author);
                    }

                    return $feedItem
                        ->authorName($author)
                        ->authorEmail((string) $item->author?->email);
                });

            $feedItems[] = $feedItem;
        }

        return RssFeed::renderFeedItems($feedItems, 'Posts feed', sprintf('Latest posts from %s', theme_option('site_title')));
    }
}

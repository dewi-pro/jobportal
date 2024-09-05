<?php

use Botble\Widget\AbstractWidget;

class NewsletterWidget extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => __('Newsletter form'),
            'description' => __('Display Newsletter form footer'),
            'title' => null,
            'background_image' => null,
            'image_left' => null,
            'image_right' => null,
        ]);
    }
}

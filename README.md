# wordpress-owlslider
Wordpress-Owlslider is a simple plugin adaptation of OwlCarousel for Wordpress.
OwlCarousel is a touch enabled jQuery plugin that lets you create beautiful responsive carousel slider.
[Visit Owl Carousel landing page](http://owlgraphic.com/owlcarousel)

This plugin comes with some simple features :

###Features:
* Custom post type to set your slide groups
* Shortcode with parameters
* Call of the plugin function directly from your theme
* Customize some defaults values in the slider-groups taxonomy options panel

### OwlCarousel ###
>v.1.3.3


## Manual Installation

1. Download the plugin file from this page and unzip the contents
1. Upload the `wordpress-owlcarousel` folder to the `/wp-content/plugins/` directory
1. Activate the `wordpress-owlcarousel` plugin through the 'Plugins' menu in WordPress

### Once Activated

In your Wordpress admin area :
1. Create a new slide group in the new `Slides` post type section
2. Add some Slides to the slide group category, give it a title and upload a Featured Image for each.
3. *Optional:* You can add content to your post if you plan to use the `post` display option. The featured image will be then used as a background of your content if specified.
3. Place the `[owlcarousel]` shortcode in a Page or Post or use the function in some page of your theme

### Shortcode Options
As of version 0.1, nearly all of the current options can be set in the slider group taxonomy page details. However, if you'd like different settings for different carousels, you can override these by using shortcode options :

* `slider_page` _(must be specify)_
    * This is the only mandatory value. You have to specify an existing slide group, as you named it in the Wordpress administration.
    * `[owlslider slider_page="slideGroup"]`


* `slider_type` _(default post)_
    * Whether to display the full post content or only the featured image. `post` or `image`.
    * `[owlslider slider_page="slideGroup" slider_type="post"]`


* `transition_style` _(default null)_
    * Use transition_style option to set transition. As part of the [owl-carousel css transitions](http://owlgraphic.com/owlcarousel/demos/transitions.html) there are four predefined transitions:  `fade`, `backSlide`, `goDown` or `scaleUp`. You can also build your own transition styles easily. For example by adding "YourName" value transition_style: "YourName", owlCarousel will add .owl-YourName-out class to previous slide and .owl-YourName-in to next slide. All transitions require "in" and "out" styles. Look into owl.carousel.css source for details.
    * `[owlslider slider_type="image" transition_style="backSlide" slider_page="slideGroup" limit="3" items="20" slide_speed="300"]`


* `limit`  _(default 20)_
    * How much slide must be displayed at max. You can change it if you have more than 20 slides to display. But be carefull with loading issues.
    * `[owlslider slider_page="slideGroup" limit="100"]`


* `single_item` _(default true)_ and `items` _(default 5)_
    * Use of owl-carousel as a slider (only one item at a time) or as a carousel (with a maximum amount of items displayed at a time with the widest browser width).
    * `[owlslider slider_page="slideGroup" slider_type="post" single_item="false" items="4"]`


* `pagination` _(default true)_
    * Whether to display the pagination or not.
    * `[owlslider slider_page="slideGroup" pagination="false"]`


* `navigation` _(default true)_
    * Whether to display the navigation or not.
    * `[owlslider slider_page="slideGroup" navigation="false"]`

* `autoplay` _(default false)_
    * Change to any integrer for example autoPlay : 5000 to play every 5 seconds. If you set autoPlay: true default speed will be 5 seconds.
    * `[owlslider slider_page="slideGroup" autoplay="7000"]`
    * `[owlslider slider_page="slideGroup" autoplay="true"]`


## Call the plugin from your theme
Instead of using a shortcode, it is possible to call the plugin directly for your template pages, using this code :

```php
    <?php
    $homeSlider = new OwlSlider(array(
        'slider_type'=> 'post',
        'slider_page'=> 'sliderHome',
        'transition_style'=> 'fade',
        ), true);
    ​?>
```


### 4. For more details about owl-carousel, please visit [OwlCarousel landing page](http://owlgraphic.com/owlcarousel)

Credits & disclamer
-------------------
This plugin is still in progress. Fell free to use it... at your own risks. Currently, no support will be provided. It was was written by Jérôme Poslednik (@washaweb) and Thomas Montagnoni (@Thomawws). Courtesy of Bartosz Wojciechowski (@aOwlFonk) for the owl-slider code.

License
-------
The MIT License (MIT)
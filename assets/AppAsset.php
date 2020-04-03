<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/styles.css',
        'css/owl.theme.default.min.css',
        'css/owl.carousel.min.css',
        'css/green-audio-player.min.css',
    ];
    public $js = [
        'js/owl.carousel.js',
        'js/owl.autoplay.js',
        'js/green-audio-player.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
        'xtetis\bootstrap4glyphicons\assets\GlyphiconAsset',
    ];
}

<?php
namespace Serverfireteam\Panel;

use Illuminate\Database\Eloquent\Model;

class Link extends Model {

    protected $fillable = ['url', 'display', 'show_menu'];

    protected $table = 'links';
    
    static $cache = [];
    public static function getAllLinks($forceRefresh = false) // allCached(
    {
        if (!isset(self::$cache['all']) || $forceRefresh) {
            self::$cache['all'] = Link::all();
        }
        return self::$cache['all'];
    }
    public static function getUrls($forceRefresh = false) // returnUrls(
    {
        if (!isset(self::$cache['all_urls']) || $forceRefresh) {
            $configs = Link::getAllLinks($forceRefresh);
            self::$cache['all_urls'] =  $configs->pluck('url')->toArray();
        }
        return self::$cache['all_urls'];
    }
    public static function getMainUrls($forceRefresh = false)
    {
        if (!isset(self::$cache['main_urls']) || $forceRefresh) {
            $configs = Link::where('main', '=', true)->get(['url']);
            self::$cache['main_urls'] = $configs->pluck('url')->toArray();
        }
        return self::$cache['main_urls'];
    }
    public function addNewLink($url, $label, $visibility, $checkExistence = false) // getAndSave(
    {
        if ($checkExistence && $this->isLinkExist($url, $label))
        {
            return;
        }
        $this->url = $url;
        $this->display = $label;
        $this->show_menu = $visibility;
        $this->save();
    }

    /**
     * check given url and display label if they added before
     * @param $url
     * @param $label
     * @return bool
     */
    public function isLinkExist($url, $label)
    {
        //if you call exists() against a non existent record then it gives error: Call to a member function exists() on null
        //Link::where('url', '=', $url)->exists()
        $linkCount = Link::where('url', '=', $url)->where('display', '=', $label)->count(); //->first(); if ($link === null)
        if ($linkCount <= 0) {
            // link doesn't exist
            return false;
        }
        return true;
    }
}
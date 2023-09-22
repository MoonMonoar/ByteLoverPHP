<?php
class CardMaker
{
    public static function makeSoloLearnCard($dataOBJ)
    {
        global $strings, $lang;
        $id = $dataOBJ->id;
        $tutorial_id = $dataOBJ->tutorial_id;
        $article_id = $dataOBJ->article_id;
        $body = $dataOBJ->body;

        //Format body
        $body = strip_tags($body);
        $body = substr($body, 0, 350).'...';

        $html = '<a target="_blank" href="'.self::getUrl($tutorial_id, $article_id).'">';
        $html.='<div class="card_welcome">
                <div class="card_holder">
                      <div class="t-info-g">
                        <div>
                          <div class="title b">'.$strings["learn_solo"][$lang].'
                            <sup>
                                <span class="badge badge-success">
                                    '.$strings["free"][$lang].'
                                </span>
                            </sup>: '.self::getTitleFromAtricleId($article_id).'
                          </div>
                        </div>
                      </div>
                      '.self::getBanner($tutorial_id).'
                      <div>
                        '.$body.'
                      </div>
                  </div>
                </div>';

        return $html.'</a>';
    }
    public static function getUrl($tutorial_id, $article_id){
        $url = "/solo/";
        switch ($tutorial_id){
            case 1:
                $url.= "c/";
                break;
        }
        return $url.= $article_id."/?ref=CardMaker";
    }
    public static function getTitleFromAtricleId($id){
        $id = str_replace("-", " ", $id);
        return ucwords($id);
    }

    private static function getBanner($tutorial_id)
    {
        global $script_version;
        $banner = '<img class="auto_img" src="/solo/imgs/c_banner.png?v='.$script_version.'" alt="C banner">';
        switch ($tutorial_id){
            case 1:
                $banner = '<img class="auto_img" src="/solo/imgs/c_banner.png?v='.$script_version.'" alt="C banner">';
                break;
        }

        return $banner;
    }
}
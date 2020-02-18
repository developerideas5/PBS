<?php
/*
   _                          _____     _       ____    ____      __ _
  / /                        |_   _|   / \     |_   \  /   _|    / /\ \
 / /  _ .--..--.    _   __     | |    / _ \      |   \/   |     / /  \ \
< <  [ `.-. .-. |  [ \ [  ]_   | |   / ___ \     | |\  /| |    / /    > >
 \ \  | | | | | |   \ '/ /| |__' | _/ /   \ \_  _| |_\/_| |_  / /    / /
  \_\[___||__||__][\_:  / `.____.'|____| |____||_____||_____|/_/    /_/ Community Edition
                   \__.' o---------------------------------------------------------------
                         |
                         o--------o Project Coordinator, CTO: Dr. rer. nat. Stephan Raub
                                  o B.Sc. Ingo Breuer
                                  o Michael Schlapa
                                  o Dennis-Bendert Schramm (retired)
                                  o Marcus Ihde-Meister
                                  o Christoph Gierling (retired)

Copyright (C) 2010,2011 The <myJAM/> Team, Heinrich-Heine-University Duesseldorf, Germany.

https://sourceforge.net/projects/myjam

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307,
USA.
*/

/**
 * @desc   Erzeugt HTML Code
 *         Sowas wurde bestimmt schon 1Mio. Mal implementiert
 *         Aber natürlich nicht so schön, wie jetzt ;)
 * @author Ingo Breuer
 */
class HTMLCreator
{
  /**
   * @desc   Die Primitivste Form eines HTML/DOM Elements
   * @return
   */
  static function mk($keyword, //
      $style_and_js, //
      $str //
  ) {
    return '<'.$keyword.' '.$style_and_js.' >' //
    .$str //
    .'</'.$keyword.'>' //
    ;
  }
  /**
   * @desc
   * @return
   */
  static function mk_a($html='', $style_and_js='') {
    return self::mk('a', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_abbr($html='', $style_and_js='') {
    return self::mk('abbr', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_acronym($html='', $style_and_js='') {
    return self::mk('acronym', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_address($html='', $style_and_js='') {
    return self::mk('address', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_applet($html='', $style_and_js='') {
    return self::mk('applet', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_area($html='', $style_and_js='') {
    return self::mk('area', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_b($html='', $style_and_js='') {
    return self::mk('b', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_base($html='', $style_and_js='') {
    return self::mk('base', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_basefont($html='', $style_and_js='') {
    return self::mk('basefont', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_bdo($html='', $style_and_js='') {
    return self::mk('bdo', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_big($html='', $style_and_js='') {
    return self::mk('big', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_blockquote($html='', $style_and_js='') {
    return self::mk('blockquote', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_body($html='', $style_and_js='') {
    return self::mk('body', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_br($html='', $style_and_js='') {
    return self::mk('br', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_button($html='', $style_and_js='') {
    return self::mk('button', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_caption($html='', $style_and_js='') {
    return self::mk('caption', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_center($html='', $style_and_js='') {
    return self::mk('center', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_cite($html='', $style_and_js='') {
    return self::mk('cite', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_code($html='', $style_and_js='') {
    return self::mk('code', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_col($html='', $style_and_js='') {
    return self::mk('col', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_colgroup($html='', $style_and_js='') {
    return self::mk('colgroup', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_dd($html='', $style_and_js='') {
    return self::mk('dd', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_del($html='', $style_and_js='') {
    return self::mk('del', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_dfn($html='', $style_and_js='') {
    return self::mk('dfn', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_dir($html='', $style_and_js='') {
    return self::mk('dir', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_div($html='', $style_and_js='') {
    return self::mk('div', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_dl($html='', $style_and_js='') {
    return self::mk('dl', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_dt($html='', $style_and_js='') {
    return self::mk('dt', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_em($html='', $style_and_js='') {
    return self::mk('em', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_fieldset($html='', $style_and_js='') {
    return self::mk('fieldset', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_font($html='', $style_and_js='') {
    return self::mk('font', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_form($html='', $style_and_js='') {
    return self::mk('form', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_frame($html='', $style_and_js='') {
    return self::mk('frame', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_frameset($html='', $style_and_js='') {
    return self::mk('frameset', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_h1($html='', $style_and_js='') {
    return self::mk('h1', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_h2($html='', $style_and_js='') {
    return self::mk('h2', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_h3($html='', $style_and_js='') {
    return self::mk('h3', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_h4($html='', $style_and_js='') {
    return self::mk('h4', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_h5($html='', $style_and_js='') {
    return self::mk('h5', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_h6($html='', $style_and_js='') {
    return self::mk('h6', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_head($html='', $style_and_js='') {
    return self::mk('head', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_hr($html='', $style_and_js='') {
    return self::mk('hr', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_html($html='', $style_and_js='') {
    return self::mk('html', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_i($html='', $style_and_js='') {
    return self::mk('i', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_iframe($html='', $style_and_js='') {
    return self::mk('iframe', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_img($html='', $style_and_js='') {
    return self::mk('img', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_input($html='', $style_and_js='') {
    return self::mk('input', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_ins($html='', $style_and_js='') {
    return self::mk('ins', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_isindex($html='', $style_and_js='') {
    return self::mk('isindex', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_kbd($html='', $style_and_js='') {
    return self::mk('kbd', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_label($html='', $style_and_js='') {
    return self::mk('label', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_legend($html='', $style_and_js='') {
    return self::mk('legend', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_li($html='', $style_and_js='') {
    return self::mk('li', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_link($html='', $style_and_js='') {
    return self::mk('link', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_map($html='', $style_and_js='') {
    return self::mk('map', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_menu($html='', $style_and_js='') {
    return self::mk('menu', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_meta($html='', $style_and_js='') {
    return self::mk('meta', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_noframes($html='', $style_and_js='') {
    return self::mk('noframes', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_noscript($html='', $style_and_js='') {
    return self::mk('noscript', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_object($html='', $style_and_js='') {
    return self::mk('object', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_ol($html='', $style_and_js='') {
    return self::mk('ol', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_optgroup($html='', $style_and_js='') {
    return self::mk('optgroup', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_option($html='', $style_and_js='') {
    return self::mk('option', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_p($html='', $style_and_js='') {
    return self::mk('p', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_param($html='', $style_and_js='') {
    return self::mk('param', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_pre($html='', $style_and_js='') {
    return self::mk('pre', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_q($html='', $style_and_js='') {
    return self::mk('q', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_s($html='', $style_and_js='') {
    return self::mk('s', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_samp($html='', $style_and_js='') {
    return self::mk('samp', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_script($html='', $style_and_js='') {
    return self::mk('script', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_select($html='', $style_and_js='') {
    return self::mk('select', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_small($html='', $style_and_js='') {
    return self::mk('small', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_span($html='', $style_and_js='') {
    return self::mk('span', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_strike($html='', $style_and_js='') {
    return self::mk('strike', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_strong($html='', $style_and_js='') {
    return self::mk('strong', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_style($html='', $style_and_js='') {
    return self::mk('style', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_sub($html='', $style_and_js='') {
    return self::mk('sub', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_sup($html='', $style_and_js='') {
    return self::mk('sup', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_table($html='', $style_and_js='') {
    return self::mk('table', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_tbody($html='', $style_and_js='') {
    return self::mk('tbody', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_td($html='', $style_and_js='') {
    return self::mk('td', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_textarea($html='', $style_and_js='') {
    return self::mk('textarea', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_tfoot($html='', $style_and_js='') {
    return self::mk('tfoot', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_th($html='', $style_and_js='') {
    return self::mk('th', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_thead($cols, $style_and_js='') {
    return self::mk('thead', $style_and_js, $cols);
  }
  /**
   * @desc
   * @return
   */
  static function mk_title($html, $style_and_js='') {
    return self::mk('title', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_tr($html='', $style_and_js='') {
    return self::mk('tr', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_tt($html='', $style_and_js='') {
    return self::mk('tt', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_u($html='', $style_and_js='') {
    return self::mk('u', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_ul($html='', $style_and_js='') {
    return self::mk('ul', $style_and_js, $html);
  }
  /**
   * @desc
   * @return
   */
  static function mk_var($html='', $style_and_js='') {
    return self::mk('var', $style_and_js, $html);
  }
  /**
   * @desc
   * @return listing
   */
  static function arrayToList($content,$deco=NULL) {
    $size = count($content);
    if(NULL != $deco) {
      if($size != count($deco)) {
        die("Error::Size Missmatch of Content And Decoration");
      }
    }
    $tmp = '';
    foreach($content as $key => $val) {
      $tmp .= self::mk_li($val,(NULL==$deco)?'':array_shift($deco));
    }
    return $tmp;
  }
}

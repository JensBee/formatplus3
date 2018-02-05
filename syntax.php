<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

class syntax_plugin_formatplus3 extends DokuWiki_Syntax_Plugin {
    function getType(){ return 'formatting'; }
    function getPType() { return 'normal'; }
    function getAllowedTypes() {
        return array('formatting', 'substition', 'disabled');
    }
    function getSort(){ return 0; }
    function connectTo($mode) {
        foreach ($this->match_replace as $ignored => $tag) {
            if ($tag["pre"])
                $this->Lexer->addEntryPattern(
                    $tag["pre"], $mode, 'plugin_formatplus3');
        }
    }
    function postConnect() {
        foreach ($this->match_replace as $ignored => $tag) {
            if ($tag["post"])
                $this->Lexer->addExitPattern(
                    $tag["post"], 'plugin_formatplus3');
        }
    }

    private $match_replace = array(
        '""' => array(
            "tag" => "q",
            "pre" => '""(?=.*?"")',
            "post" => '""'),
        '??' => array(
            "tag" => "var",
            "pre" => '\?\?(?=.*?\?\?)',
            "post" => '\?\?'),
        '@@' => array(
            "tag" => "dfn",
            "pre" => '@@(?=.*?@@)',
            "post" => '@@'),
        '``' => array(
            "tag" => "kbd",
            "pre" => '``(?=.*?``)',
            "post" => '``'),
        '&&' => array(
            "tag" => "cite",
            "pre" => '&&(?=.*?&&)',
            "post" => '&&'),
        '$$' => array(
            "tag" => "samp",
            "pre" => '\$\$(?=.*?\$\$)',
            "post" => '\$\$'),
        '!!' => array(
            "tag" => "strong class='smallcaps'",
            "pre" => '!!(?=.*?!!)',
            "post" => '!!'),
        '/^' => array(
            "tag" => "sup",
            "pre" => '/\^(?=.*?\^/)'),
        '^/' => array(
            "tag" => "sup",
            "post" => '\^/'),
        '/,' => array(
            "tag" => "sub",
            "pre" => '/,(?=.*?,/)'),
        ',/' => array(
            "tag" => "sub",
            "post" => ',/'),
        '/+' => array(
            "tag" => "ins class='simple'",
            "pre" => '/\+(?=.*?\+/)'),
        '+/' => array(
            "tag" => "ins",
            "post" => '\+/'),
        '/-' => array(
            "tag" => "del class='simple'",
            "pre" => '/-(?=.*?-/)'),
        '-/' => array(
            "tag" => "del",
            "post" => '-/'),
        '/!' => array(
            "tag" => "em class='inverse'",
            "pre" => '/!(?=.*?!/)'),
        '!/' => array(
            "tag" => "em",
            "post" => '!/')
    );

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, Doku_Handler $handler){
        switch ($state) {
            case DOKU_LEXER_ENTER:
            case DOKU_LEXER_UNMATCHED:
            case DOKU_LEXER_EXIT:
                return array($state, $match);
        }
        return array();
    }

    /**
     * Create output
     */
    function render($mode, Doku_Renderer $renderer, $data) {
        if($mode == 'xhtml'){
            list($state, $tag_or_match) = $data;
            switch ($state) {
              case DOKU_LEXER_ENTER:
                $renderer->doc .= "<".$this->match_replace[$tag_or_match]["tag"].">";
                break;
              case DOKU_LEXER_UNMATCHED:
                $renderer->doc .= $renderer->_xmlEntities($tag_or_match);
                break;
              case DOKU_LEXER_EXIT:
                $renderer->doc .= "</".$this->match_replace[$tag_or_match]["tag"].">";
                break;
            }
            return true;
        }
        return false;
    }
}
?>

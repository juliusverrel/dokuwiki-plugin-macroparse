<?php
    /**
     * MacroParse plugin: parse contents AFTER replacing macros by values
     *
     * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
     * @author     Julius Verrel (jv.git@posteo.de)
     */
     
    // must be run within Dokuwiki
    if (!defined('DOKU_INC')) die();
     
    class syntax_plugin_macroparse extends DokuWiki_Syntax_Plugin
    {
        function getInfo(){
            return array(
                'author' => 'Julius Verrel',
                'email'  => 'jv.git@posteo.de',
                'date'   => '2018-10-18',
                'name'   => 'MacroParse Plugin',
                'desc'   => 'Parse contents after replacing macros by values',
                'url'    => '...',
            );
        }
     
        function getType(){
            return "protected";
        }
     
        function getPType(){
            return "normal";
        }
     
        function getSort(){
            return 0;
        }
     
        function connectTo( $mode ) {
            $this->Lexer->addEntryPattern("<macroparse>(?=.*?</macroparse>)",$mode,"plugin_macroparse");
        }
     
        function postConnect() {
            $this->Lexer->addExitPattern( "</macroparse>","plugin_macroparse");
        }
     
        function handle( $match, $state, $pos, Doku_Handler $handler ){
            return array($state,$match);
        }
     
        function render( $mode, Doku_Renderer $renderer, $data ) {
            global $ID;
            if($mode == 'xhtml'){
                list($state, $data) = $data;
                if ($state === DOKU_LEXER_UNMATCHED) {
                    ob_start();
                    $data = $this->_apply_macro($data);
                    //echo "Macroparse:\n\n";
                    echo $data;
                    $renderer->doc .= p_render( "xhtml", p_get_instructions( ob_get_contents() ), $info );
                    ob_end_clean();
                }
                return true;
            }
            return false;
        }
        
       /**
       * inserts user or date dependent values
       *
       * (adapted from include and struct plugin)
       */
      function _apply_macro($data) {
          global $INFO;
          global $auth;

          $id = $INFO['id'];
          $nslist = preg_split("/:/", $id);
          array_pop($pglist);  // pop the page name
          $lastns = array_pop($pglist); // get "last" ns

          
          $replace = array(
                    '@ID@' => $INFO['id'],
                    '@NS@' => getNS($INFO['id']),
                    '@LASTNS@' => $lastns,
                    '@PAGE@' => noNS($INFO['id']),
                    '@YEAR@'  => date('Y'),
                    '@MONTH@' => date('m'),
                    '@WEEK@' => date('W'),
                    '@DAY@'   => date('d'),                    
                    '@TODAY@' => date('Y-m-d'));
          if ($auth) {
              $email = $INFO['userinfo']['mail'];
              $email_parts = preg_split("/@/", $email);
              $replace = array_merge($replace, array(
                    '@USER@' => $_SERVER['REMOTE_USER'],
                    '@EMAIL@' => $email,
                    '@EMAILNAME@' => $email_parts[0],
                    '@CLEANEMAIL@' => cleanID($email),
                    '@CLEANNAME@'  => cleanID($INFO['userinfo']['name']),
                    '@FULLNAME@'  => $INFO['userinfo']['name']));
          }       
 
          return str_replace(array_keys($replace), array_values($replace), $data);     
      }         
       
    }
?>


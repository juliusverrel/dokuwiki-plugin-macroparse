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
         COPIED FROM INCLUDE PLUGIN
       */
      function _apply_macro($id) {
          global $INFO;
          global $auth;

          // if we don't have an auth object, do nothing
          if (!$auth) return $id;
          $user     = $_SERVER['REMOTE_USER'];
          $group    = $INFO['userinfo']['grps'][0];
          // set group for unregistered users
          if (!isset($group)) {
              $group = 'ALL';
          }
          $time_stamp = time();
          if(preg_match('/@DATE(\w+)@/',$id,$matches)) {
              switch($matches[1]) {
              case 'PMONTH':
                  $time_stamp = strtotime("-1 month");
                  break;
              case 'NMONTH':
                  $time_stamp = strtotime("+1 month");
                  break;
              case 'NWEEK':
                  $time_stamp = strtotime("+1 week");
                  break;
              case 'PWEEK':
                  $time_stamp = strtotime("-1 week");
                  break;
              case 'TOMORROW':
                  $time_stamp = strtotime("+1 day");
                  break;
              case 'YESTERDAY':
                  $time_stamp = strtotime("-1 day");
                  break;
              case 'NYEAR':
                  $time_stamp = strtotime("+1 year");
                  break;
              case 'PYEAR':
                  $time_stamp = strtotime("-1 year");
                  break;
              }
              $id = preg_replace('/@DATE(\w+)@/','', $id);
          }
          $replace = array(
                  '@USER@'  => cleanID($user),
                  '@NAME@'  => cleanID($INFO['userinfo']['name']),
                  '@GROUP@' => cleanID($group),
                  '@YEAR@'  => date('Y',$time_stamp),
                  '@MONTH@' => date('m',$time_stamp),
                  '@WEEK@' => date('W',$time_stamp),
                  '@DAY@'   => date('d',$time_stamp),
                  '@YEARPMONTH@' => date('Ym',strtotime("-1 month")),
                  '@PMONTH@' => date('m',strtotime("-1 month")),
                  '@NMONTH@' => date('m',strtotime("+1 month")),
                  '@YEARNMONTH@' => date('Ym',strtotime("+1 month")),
                  '@YEARPWEEK@' => date('YW',strtotime("-1 week")),
                  '@PWEEK@' => date('W',strtotime("-1 week")),
                  '@NWEEK@' => date('W',strtotime("+1 week")),
                  '@YEARNWEEK@' => date('YW',strtotime("+1 week")),
                  );
          return str_replace(array_keys($replace), array_values($replace), $id);
      }
    }

?>

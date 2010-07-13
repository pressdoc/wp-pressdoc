<?php

  include ( ABSPATH . '/wp-includes/class-json.php' );

  class PressDocAPI {
    public static $API_URL      = 'http://pressdoc.com/api/pressdocs.json';
    public static $API_ITEM_URL = 'http://pressdoc.com/api/pressdocs/%s.json';

    public function get_pressdoc( $str_pressdoc_id ) {
      $str_api_url = self::$API_ITEM_URL;
      $str_api_url = sprintf( $str_api_url, $str_pressdoc_id );

      $arr_api_result = $this->fetch_json( $str_api_url );
      return $arr_api_result;
    }

    public function get_search() {
      $str_api_url = self::$API_URL;

      $arr_api_result = $this->fetch_json( $str_api_url );
      return $arr_api_result;
    }

    private function fetch_json( $str_api_url ) {
      $data = file_get_contents ( $str_api_url );

      if( !function_exists( 'json_decode' ) ) {
        function json_decode( $data, $bool ) {
          if ( $bool ) {
            $json = new Services_JSON( SERVICES_JSON_LOOSE_TYPE );
          } else {
            $json = new Services_JSON();
          }
          return ( $json->decode( $data ) );
        }
      } else {
        return ( json_decode( $data, true ) );
      }
    }
  }

?>

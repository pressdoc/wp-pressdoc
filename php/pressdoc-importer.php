<?php

  function pressdoc_news_page() {
    $api = new PressDocAPI();

    $pressdocs = $api->get_search($_GET['p']);

    if(!empty($_GET['contentid'])) {
      $message = pressdoc_add_item($_GET['contentid']);
    } else {
      $message = '';
    }

    $table_headers = array();

    $table_headers[] = '<th class="thumb" scope="col">PressRoom</th>';
    $table_headers[] = '<th class="name" scope="col">PressDoc</th>';
    $table_headers[] = '<th class="num" scope="col">Published</th>';
    $table_headers[] = '<th class="action-links" scope="col">Actions</th>';
    $table_headers = implode("\n", $table_headers);

    $table_content = pressdoc_render_search_results($pressdocs);

    ?>
      <div class="wrap">
        <h2>PressDoc News Feed</h2>

        <?php echo $message; ?>

        <p>Want news? Find something you like below, click 'Save to Drafts' and then publish away.</p>

        <?php
          $link = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) . '?page=' . $_GET['page'];
          echo pressdoc_render_pagination(
            $pressdocs['current_page'],
            $pressdocs['total_pages'],
            $pressdocs['total_entries'],
            $link,
            1 + ($pressdocs['current_page']-1) * $pressdocs['per_page'],
            1 + ($pressdocs['current_page']) * $pressdocs['per_page']
          );
        ?>
        <br />

        <div id="poststuff" class="metaebox-holder has-right-sidebar">
          <div id="side-info-column" class="inner-sidebar">
          </div>
        </div>

        <div id="post-body-content">
          <table cellspacing="0" id="install-plugins" class="widefat" style="clear:none;">
            <thead>
              <tr>
                <?php echo $table_headers; ?>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <?php echo $table_headers; ?>
              </tr>
            </tfoot>
            <tbody class="plugins">
              <?php echo $table_content; ?>
            </tbody>
          </table>
          <br />
          <?php echo pressdoc_render_simple_pagination($pressdocs['current_page'], $pressdocs['total_pages'], $link) ?>
        </div>
      </div>
    <?php
  }

  function pressdoc_add_item($str_item_id) {

    $message = array();
    $api = new PressDocAPI();
    $pressdoc = $api->get_pressdoc($str_item_id);

    if (empty($pressdoc)) {
      $message[] = '<div class="error">';
      $message[] = '  <p>Hmmm, we\'re experiencing an unknown error. Please try again.</p>';
      $message[] = '</div>';
      return implode("\n", $message);
    }

    $arr_images = array ();
    foreach ( $pressdoc['images'] as $image ) {
      $arr_images[] = '<a href="'. $image['original_url'] . '" title="' . $image['description'] . '"><img class="alignnone" title="' . $image['description'] . '" src="'. $image['medium_url'] . '" alt="' . $image['description'] . '" /></a>';
    }
    $str_images = implode('', $arr_images);

    $arr_videos = array ();
    foreach ( $pressdoc['videos'] as $video ) {
      $arr_videos[] = $video['embed_url_html'] . '<p>' . $video['description'] . '</p>';
    }
    $str_videos = implode("\n", $arr_videos);

    $postcontent .= '<!-- PRESSDOC WATERMARK -->';
    $postcontent .= '<strong>' . $pressdoc['medium_description'] . '</strong>';
    $postcontent .= $pressdoc['large_description'];
    $postcontent .= '<br /><br />';
    $postcontent .= $str_images . "\n";
    $postcontent .= $str_videos;
    $postcontent .= '<p><a href="' . $pressdoc['get_permalink'] . '">This PressDoc was published by ' . $pressdoc['company']['name'] . ' on ' . date('l jS F Y', strtotime($pressdoc['release_date'])) . '</a></p>';
    $postcontent .= '<!-- END PRESSDOC WATERMARK -->';

    $arr_tags = array();
    foreach ( $pressdoc['categories'] as $category ) {
      $arr_tags[] = $category['name'];
    }

    $data = array(
      'ID'            => null,
      'post_content'  => $postcontent,
      'post_title'    => $pressdoc['title'],
      'post_excerpt'  => $pressdoc['medium_description'],
      'tags_input'    => $arr_tags
    );

    $pressdoc_post_id = wp_insert_post( $data );

    wp_redirect(admin_url('post.php?action=edit&post=' . $pressdoc_post_id));
  }

  function pressdoc_render_search_results( $arr_pressdocs ) {
    $arr_html_output = array ();

    foreach ( $arr_pressdocs['pressdocs'] as $pressdoc ) {
      $link = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . '?page=' . $_GET['page'] . '&noheader=true&contentid=' . $pressdoc['id'];

      $image = '';
      if ( !empty ( $pressdoc['company']['small_logo_url'] ) ) {
        $image  = '<div style="float:left; height:125px; overflow:hidden; width:125px;">';
        $image .= '  <img src="' . $pressdoc['company']['small_logo_url'] . '" style="padding:5px 0;" alt="' . $pressdoc['company']['name'] . ' logo">';
        $image .= '</div>';
      }

      $arr_html_output [] = '<tr>';
      $arr_html_output [] = '  <td class="thumb">' . $image . '</td>';
      $arr_html_output [] = '  <td class="name">';
      $arr_html_output [] = '    <a href="http://' . $pressdoc['company']['subdomain'] . '.pressdoc.com" alt="' . $pressdoc['company']['name'] . ' PressRoom" title="' . $pressdoc['company']['name'] . ' PressRoom" target="_blank">' . $pressdoc['company']['name'] . '</a>';
      $arr_html_output [] = '      - ';
      $arr_html_output [] = '    <a href="' . $pressdoc['get_permalink'] . '" alt="' . $pressdoc['title'] . '" title="' . $pressdoc['title'] . '" target="_blank">' . $pressdoc['title'] . '</a>';
      $arr_html_output [] = '    <br /><br />';
      $arr_html_output [] = '    <span style="font-weight: normal">' . $pressdoc['medium_description'] . '</span>';
      $arr_html_output [] = '    <br /><br />';
      $arr_html_output [] = '    <a href="' . $pressdoc['get_permalink'] . '" alt="' . $pressdoc['title'] . '" title="' . $pressdoc['title'] . '" target="_blank">Read full PressDoc &rarr;</a>';
      $arr_html_output [] = '  </td>';
      $arr_html_output [] = '  <td class="vers" style="width: 100px;">' . date('D, d M Y H:i', strtotime($pressdoc['release_date'])) . '</td>';
      $arr_html_output [] = '  <td class="action-links">';
      $arr_html_output [] = '  <a href="' . $link . '" alt="Save to Drafts">Save to Drafts</a></td>';
      $arr_html_output [] = '</tr>';
    }

    return implode ( "\n", $arr_html_output );
  }

  function pressdoc_render_pagination( $current_page, $total_pages, $total_items, $link, $start_item, $end_item ) {
    $pagination = array();
    $pagination[] = '<div class="tablenav">';

    $pagination[] = '<div class="alignleft actions">';
    $pagination[] = '<p class="displaying-num">';
    if ( $total_pages == 0 ) {
      $pagination[] = '<strong>There are no results available for your search. Please try again.</strong>';
    } else {
      $pagination[] = 'Displaying ' . number_format( $start_item ) . ' to ' . number_format( $end_item ) . ' of ' . number_format( $total_items ) . ' PressDocs.';
    }
    $pagination[] = '</p>';
    $pagination[] = '</div>';

    if ( $total_pages > 1 ) {
      $window = 3;
      $p  = $current_page;
      $lp = $current_page - $window;
      $hp = $current_page + $window;

      $pagination[] = '<div class="tablenav-pages">';
      $pagination[] = '<span class="displaying-num">Go to page:</span>';

      while ( ( $lp < $total_pages + 1 ) && ( $lp <= $hp ) ) {
        if ( ( $lp > 0 ) ) {
          if ( $lp == $p ) {
            $pagination[] = '<span class="page-numbers current">' . number_format( $lp ) . '</span>';
          } else {
            $pagination[] = '<a href="' . $link . '&p=' . $lp . '" class="page-numbers" alt="Page number ' . number_format( $lp ) . '">' . number_format( $lp ) . '</a>';
          }
        }
        $lp++;
      }
      if ( ( $lp - 1 ) != $total_pages ) {
        $pagination[] = ' ... <a href="' . $link . '&p=' . $total_pages. '" class="page-numbers" alt="Page number ' . number_format( $total_pages ) . '">' . number_format( $total_pages ) . '</a>';
      }
      $pagination[] = '</div>';
    }

    $pagination[] = '</div>';
    return implode( "\n", $pagination );
  }

  function pressdoc_render_simple_pagination( $current_page, $total_pages, $link ) {
    $pagination = array();

    if ( $total_pages > 1 ) {
      if ( $current_page <= $total_pages && $current_page != 1 ) {
        $previous = $current_page - 1;
        $pagination[] = '<a href="' . $link . '&p=' . $previous .'" class="button" alt="Page number ' . $previous . '"> << Previous </a>';
      }

      if ( $current_page != $total_pages ) {
        $next = $current_page + 1;
        $pagination[] = '<a href="' . $link . '&p=' . $next . '" class="button" style="float:right" alt="Page number ' . $next . '"> Next >> </a>';
      }
    }

    return implode( "\n", $pagination );
  }

  function pressdoc_add_news_page() {
    global $wpdb;

    if( function_exists( 'add_menu_page' ) ) {
      //$title = sprintf( __('News %s'), "<span id='awaiting-mod' class='count-50'><span class='pending-count'>" . number_format_i18n(50) . "</span></span>" );
      $title = __('News');
      add_menu_page( __ ( 'News' ), $title, 2, __FILE__, 'pressdoc_news_page', WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__), "", plugin_basename(__FILE__)) . '../img/pressdoc-logo.png', 6 );
    }
  }

  add_action ( 'admin_menu', 'pressdoc_add_news_page' );

?>

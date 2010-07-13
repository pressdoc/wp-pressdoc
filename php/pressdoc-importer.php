<?php

  function pressdoc_news_page() {
    $api = new PressDocAPI();
    $pressdocs = $api->get_search();

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
      $arr_images[] = '<img class="alignnone" title="' . $image['description'] . '" src="'. $image['medium_url'] . '" alt="' . $image['description'] . '" />';
    }
    $str_images = implode("\n", $arr_images);

    $postcontent .= '<!-- PRESSDOC WATERMARK --><hr />';
    $postcontent .= '<p><a href="' . $pressdoc['get_permalink'] . '">This PressDoc was published by ' . $pressdoc['company']['name'] . ' on ' . date('l jS F Y', strtotime($pressdoc['release_date'])) . '</a></p>';
    $postcontent .= $pressdoc['large_description'];
    $postcontent .= '<br /><br />';
    $postcontent .= $str_images;
    $postcontent .= '<hr /><!-- END PRESSDOC WATERMARK -->';

    $data = array(
      'ID'            => null,
      'post_content'  => $postcontent,
      'post_title'    => $pressdoc['title'],
      'post_excerpt'  => $pressdoc['medium_description']
    );

    $pressdoc_post_id = wp_insert_post( $data );

    $message[] = '<div class="updated">';
    $message[] = '  <p><strong>Ready to publish:</strong>  <em>"' . $data['post_title'] . '"</em> was successfully saved in <strong><a href="' . admin_url('edit.php?post_status=draft') . '">Draft Mode</a></strong>. Now you can <strong><a href="' . admin_url('post.php?action=edit&post=' . pressdoc_post_id) . '">edit and publish</a></strong> your blog post.</p>';
    $message[] = '</div>';

    return implode("\n", $message);
  }

  function pressdoc_render_search_results( $arr_pressdocs ) {
    $arr_html_output = array ();

    foreach ( $arr_pressdocs as $pressdoc ) {
      $link = $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&contentid=' . $pressdoc['id'];

      $image = '';
      if ( !empty ( $pressdoc['company']['small_logo_url'] ) ) {
        $image  = '<div style="border:1px solid #EEEEEE; float:left; height:125px; overflow:hidden; width:125px;">';
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

  function pressdoc_add_news_page() {
    global $wpdb;

    if( function_exists( 'add_submenu_page' ) ) {
      add_submenu_page( 'post.php', __ ( 'News' ), __ ( 'News' ), 2, __FILE__, 'pressdoc_news_page' );
    }
  }

  add_action ( 'admin_menu', 'pressdoc_add_news_page' );

?>

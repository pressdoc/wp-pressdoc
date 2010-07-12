<?php

function PressDoc_NewsFeed_admin_page() {

?>
  <div class="wrap">
    <h2>PressDoc News Feed</h2>
    <?php
      $api = new PressDocNewsFeedAPI();
      $articles = $api->pressdoc_news_feed_api_search();

      if(!empty($_GET['contentid'])) {
        $message = PressDoc_NewsFeed_add_item($_GET['contentid']);
        if(!empty($message)) {
          echo $message;
        }
      }
    ?>

    <p>Want news? Find something you like below, click 'Save to Drafts' and then publish away.</p>

    <?php
      $headfoot = array();
      $headfoot[] = "<th class=\"pressroom\" scope=\"col\">PressRoom</th>";
      $headfoot[] = "<th class=\"name\" scope=\"col\">Headline</th>";
      $headfoot[] = "<th class=\"num\" scope=\"col\">Published</th>";
      $headfoot[] = "<th class=\"action-links\" scope=\"col\">Actions</th>";

      $headfoot = implode("\n", $headfoot);
    ?>

    <div id="poststuff" class="metaebox-holder has-right-sidebar">
      <div id="side-info-column" class="inner-sidebar">
    </div>
  </div>

  <div id="post-body-content">
    <table cellspacing="0" id="install-plugins" class="widefat" style="clear:none;">
      <thead>
        <tr>
          <?= $headfoot; ?>
        </tr>
      </thead>

      <tfoot>
        <tr>
          <?= $headfoot; ?>
        </tr>
      </tfoot>

      <tbody class="plugins">
       <?= PressDoc_Render_NewsFeed_Search($articles); ?>
      </tbody>
    </table>
  </div>
  </div>

<?php

  }

  function PressDoc_NewsFeed_add_item($str_item_id) {

    $message = array();
    $api = new PressDocNewsFeedAPI();
    $article = $api->pressdoc_news_feed_api_item($str_item_id);

    if (empty($article)) {
      $message[] = "<div class=\"error\">";
      $message[] = "  <p>Hmmm, we're experiencing an unknown error. Please try again.</p>";
      $message[] = "</div>";
      return implode("\n", $message);
    }

    $postcontent = "<p><em><strong>PLEASE NOTE</strong>: Add your own commentary here above the horizontal line, but do not make any changes below the line.  (Of course, you should also delete this text before you publish this post.)</em></p>\n\n";

    $postcontent .= "<hr /><!-- PRESSDOC WATERMARK -->";

    $postcontent .= "<p><a href=\"{$article['get_permalink']}\">This PressDoc was published by {$article['company']['name']} on ".date("l jS F Y H.i e", strtotime($article['release_date']))."</a></p>";

    $postcontent .= $article['large_description'];
    $postcontent .= "<!-- END PRESSDOC WATERMARK -->";

    $data = array(
      'ID' => null,
      'post_content' => $postcontent,
      'post_title' => $article['title'],
      'post_excerpt' => $article['medium_description']
    );

    wp_insert_post($data);

    $message[] = "<div class=\"updated\">";
    $message[] = "  <p><strong>Ready to publish:</strong>  <em>\"{$data['post_title']}\"</em> was successfully saved in <strong><a href=\"".admin_url("edit.php?post_status=draft")."\">Draft Mode</a></strong>. Now you can <strong><a href=\"".admin_url("post.php?action=edit&post={$guardian_post_id}")."\">edit and publish</a></strong> your blog post.</p>";
    $message[] = "  <p></p><p><em><strong>Note:</strong> Have you read the publishing guidelines, yet?  There are some important reminders to keep in mind.  See them in the box on the right of this admin panel.</em></p>";
    $message[] = "</div>";

    return implode("\n", $message);
  }

  function PressDoc_Render_NewsFeed_Search($arr_related_content) {
    $arr_html_output = array ();

    foreach ($arr_related_content as $related_content) {
      $link = "{$_SERVER['PHP_SELF']}?page={$_GET['page']}&contentid={$related_content['id']}";

      $arr_html_output [] = "    <tr>";
      $arr_html_output [] = "      <td class=\"pressroom\"><a href=\"http://{$related_content['company']['subdomain']}.pressdoc.com\" alt=\"{$related_content['company']['name']} PressRoom\" title=\"{$related_content['company']['name']} PressRoom\" target=\"_blank\">{$related_content['company']['name']}</a></td>";
      $arr_html_output [] = "      <td class=\"name\"><a href=\"{$related_content['get_permalink']}\" alt=\"{$related_content ['title']}\" title=\"{$related_content['title']}\" target=\"_blank\">{$related_content['title']}</a></td>";
      $arr_html_output [] = "      <td class=\"vers\">".date("j/m/Y", strtotime($related_content['release_date']))."</td>";
      $arr_html_output [] = "      <td class=\"action-links\">";
      $arr_html_output [] = "      <a href=\"{$link}\" alt=\"Save to Drafts\">Save to Drafts</a></td>";
      $arr_html_output [] = "    </tr>";
    }

    $arr_html_output [] = "  </ul>";
    $arr_html_output [] = "</div>";

    return implode ( "\n", $arr_html_output );
  }

  function PressDoc_NewsFeed_add_pages() {
    global $wpdb;

    if(function_exists("add_submenu_page")) {
      add_submenu_page("post.php", __ ("PressDoc News Feed"), __ ("PressDoc News Feed"), 2, __FILE__, "PressDoc_NewsFeed_admin_page" );
    }
  }

  add_action ("admin_menu", "PressDoc_NewsFeed_add_pages");
?>

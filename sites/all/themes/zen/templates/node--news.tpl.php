<?php
/**
 * @file
 * Zen theme's implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: An array of node items. Use render($content) to print them all,
 *   or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct url of the current node.
 * - $display_submitted: Whether submission information should be displayed.
 * - $submitted: Submission information created from $name and $date during
 *   template_preprocess_node().
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - node: The current template type, i.e., "theming hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Blog entry" it would result in "node-blog". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - node-teaser: Nodes in teaser form.
 *   - node-preview: Nodes in preview mode.
 *   - view-mode-[mode]: The view mode, e.g. 'full', 'teaser'...
 *   The following are controlled through the node publishing options.
 *   - node-promoted: Nodes promoted to the front page.
 *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser
 *     listings.
 *   - node-unpublished: Unpublished nodes visible only to administrators.
 *   The following applies only to viewers who are registered users:
 *   - node-by-viewer: Node is authored by the user currently viewing the page.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type, i.e. story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $pubdate: Formatted date and time for when the node was published wrapped
 *   in a HTML5 time element.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $view_mode: View mode, e.g. 'full', 'teaser'...
 * - $teaser: Flag for the teaser state (shortcut for $view_mode == 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content. Currently broken; see http://drupal.org/node/823380
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * Field variables: for each field instance attached to the node a corresponding
 * variable is defined, e.g. $node->body becomes $body. When needing to access
 * a field's raw values, developers/themers are strongly encouraged to use these
 * variables. Otherwise they will have to explicitly specify the desired field
 * language, e.g. $node->body['en'], thus overriding any language negotiation
 * rule that was previously applied.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see zen_preprocess_node()
 * @see template_process()
 */

global $base_url;
?>

<article class="node-<?php print $node->nid; ?> <?php print $classes; ?> clearfix"<?php print $attributes; ?>>
		<?php

			/*Feature Image*/
			$featureImage = field_get_items('node', $node, 'field_feature_image');
			if(is_array($featureImage)){
				if(array_key_exists('0', $featureImage)){
					$imgUrl = $featureImage['0']['uri'];  // the orig image uri
					$style = 'feature_large';  // or any other custom image style you've created via /admin/config/media/image-styles
					$imgSrc = image_style_url($style, $imgUrl);

					echo '<img src="'.$imgSrc.'">';
				}
			}
			/*Feature Image*/

			//print render($content['field_feature_image']);
		?>
			<div class="title">
				<?php print render($title); ?>
			</div>
		<?php

		$fieldTag = field_get_items('node', $node, 'field_tag');

		foreach($fieldTag as $key => $value )
		{
		   $tag = get_tag_name($value['tid']);
		   echo '<div class="tags">'.$tag.'</div>';
		}

		print render($content['field_sub_header']);
		print render($content['body']);

		$field_video = field_get_items('node', $node, 'field_feature_video');
		if(is_array($field_video)){
			foreach($field_video as $v) {#notice: $v is a copy, not a reference!
				//print_r($v['file']->uri);

				$split = array_reverse(explode("/", $v['file']->uri));
				$vimeouri = $split['0'];
				?><div class="vimeo"><iframe src="http://player.vimeo.com/video/<?php echo $vimeouri;?>?color=d5c558" width="500" height="281" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></div><?php
			}
		}

		?>


		<div class="sharingIcons">
			<div class="wrap">
				<span class='st_facebook_hcount' displayText='Facebook'></span>
				<span class='st_twitter_hcount' displayText='Tweet'></span>
				<span class='st_googleplus_hcount' displayText='Google +'></span>
			</div>
		</div>

		<div class="misc"><a class="top" href="#"><span>T</span>op</a><a class="share" href="#"><span>S</span>hare</a></div><?php


		?><div class="seeall"><a href="<?php echo $base_url;?>/news"><span>S</span>ee All News</a></div><?php

	?>
</article><!-- /.node -->

<div class="relatednews">
<?php
	$arguments = $node->nid;
	print views_embed_view("news", "newsblock", $arguments);
?>
</div>


<?php
/**
*页码
**/
function ashuwp_pagenavi() {
  global $wp_query, $wp_rewrite;
  $wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;
  $pagination = array(   
    'base' => @add_query_arg('paged','%#%'),   
    'format' => '',   
    'total' => $wp_query->max_num_pages,   
    'current' => $current,   
    'show_all' => false,   
    'type' => 'plain',   
    'end_size'=>'1',   
    'mid_size'=>'3',   
    'prev_text' => '上一页',
    'next_text' => '下一页'
  );   
  $total_pages = $wp_query->max_num_pages;
  if( $wp_rewrite->using_permalinks() ) 
    $pagination['base'] = user_trailingslashit( trailingslashit( remove_query_arg('s',get_pagenum_link(1) ) ) . 'page/%#%/', 'paged');   
  if( !empty($wp_query->query_vars['s']) )
    $pagination['add_args'] = array('s'=>get_query_var('s'));
  
	echo '<div class="page-nav clearfix"><nav>';
  if ( $current !=1 ) {
    echo '<a class="page-numbers first" href="'. esc_html(get_pagenum_link(1)).'">首页</a>';
  }
  
  echo paginate_links($pagination);
  
  if ( $current < $total_pages ) {
    echo '<a class="page-numbers last" href="'. esc_html(get_pagenum_link($total_pages)).'">尾页</a>';
  } 
	echo '</nav></div>';
}

/**
*中英文混合字符串截断
* $str 为要截断的字符串
* $limit_length 字符串长度，一个中文字符长度算2
* $type 设置为true时，会在字符串后面加上...
**/
function hunsubstrs($str,$limit_length,$type=false) {    
    $return_str   = "";
    $total_length = 0;
    $len = mb_strlen($str,'utf8');
    for ($i = 0; $i < $len; $i++) {
        $curr_char   = mb_substr($str,$i,1,'utf8');
        $curr_length = ord($curr_char) > 127 ? 2 : 1;
        if ($i != $len -1) {
            $next_length = ord(mb_substr($str,$i+1,1,'utf8')) > 127 ? 2 : 1;
        } else {
            $next_length = 0;
        }
        if ( $total_length + $curr_length + $next_length > $limit_length ) {
            if($type){
				$return_str .= $curr_char;
                return "{$return_str}...";
            }else{
				$return_str .= $curr_char;
                return "{$return_str}";
            }
        } else {
            $return_str .= $curr_char;
            $total_length += $curr_length;
        }
    }
    return $return_str;
}
if ( !function_exists('mb_strlen') ) {
	function mb_strlen ($text, $encode) {
		if ($encode=='UTF-8') {
			return preg_match_all('%(?:
					  [\x09\x0A\x0D\x20-\x7E]           # ASCII
					| [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
					|  \xE0[\xA0-\xBF][\x80-\xBF]       # excluding overlongs
					| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
					|  \xED[\x80-\x9F][\x80-\xBF]       # excluding surrogates
					|  \xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
					| [\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
					|  \xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
					)%xs',$text,$out);
		}else{
			return strlen($text);
		}
	}
}
if (!function_exists('mb_substr')) {
    function mb_substr($str, $start, $len = '', $encoding="UTF-8"){
        $limit = strlen($str);
        for ($s = 0; $start > 0;--$start) {// found the real start
            if ($s >= $limit)
                break;
            if ($str[$s] <= "\x7F")
                ++$s;
            else {
                ++$s; // skip length
                while ($str[$s] >= "\x80" && $str[$s] <= "\xBF")
                    ++$s;
            }
        }
       if ($len == '')
            return substr($str, $s);
        else
            for ($e = $s; $len > 0; --$len) {//found the real end
                if ($e >= $limit)
                    break;
                if ($str[$e] <= "\x7F")
                    ++$e;
                else {
                    ++$e;//skip length
                    while ($str[$e] >= "\x80" && $str[$e] <= "\xBF" && $e < $limit)
                        ++$e;
                }
            }
        return substr($str, $s, $e - $s);
    }
}

/**
*获取浏览数
* 参数为文章ID，浏览量的字段名称为post_views_count
**/
function getPostViews($postID){
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0";
    }
    return $count;
}
/**
*设置浏览数
*参数为文章ID，浏览量的字段名称为post_views_count
*一般用在文章模板 the_post(); 的后面。
**/
function setPostViews($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}

/**
*检测字符串是否以 http://开头，若不是以http://开头则自动加上
*用于规范网址
**/
function addhttp($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

//为评论者添加nofollow属性
add_filter('comments_popup_link_attributes', 'add_nofollow_to_comments_popup_link');
function add_nofollow_to_comments_popup_link(){
	return 'rel="nofollow" target="_blank"';
}
add_filter('get_comment_author_link','my_target_to_comments_athor_link');
function my_target_to_comments_athor_link( $link ){
	return str_replace( '\'>','\' target=\'_blank\'>', $link );
}
/**给评论回复链接加上nofollow**/
add_filter('comment_reply_link', 'add_nofollow_to_replay_link');
function add_nofollow_to_replay_link( $link ){
	return str_replace( '")\'>', '")\' rel=\'nofollow\'>', $link );
}

/**
*获取任意分类的"顶级父分类",返回分类ID,若本身是顶级分类,返回本身ID
* $term_id 分类的ID
* $taxonomy 分类法，默认为category
* $top_level 人为设定的顶级分类的父分类，默认为0。范例，若设置为3，则将ID为3的分类的第一级子分类定为"顶级父分类"
**/
function get_top_parent_term_id ($term_id ,$taxonomy='category', $top_level=0) {

	while ($term_id!=$top_level) {
		$term = get_term($term_id, $taxonomy);
		$term_id = $term->parent;
		$parent_id = $term->term_id;
	}
	return $parent_id;
}

/**
*获取页面的顶级父页面
* $page_id 页面的ID
**/
function get_top_parent_page_id($page_id) {

  $ancestors=get_post_ancestors($page_id);
  
  // Check if page is a child page (any level)
  if ($ancestors) {
    //  Grab the ID of top-level page from the tree
    return end($ancestors);
  } else {
    // Page is the top level, so use  it's own id
    return $page_id;
  }
}


/**
*新建页面函数, 用于主题自动新建页面
*参数$title 字符串 页面标题
*参数$slug  字符串 页面别名
*参数$page_template 字符串  模板名
*无返回值
**/
function ashu_add_page($title,$slug,$page_template=''){
  $allPages = get_pages();//获取所有页面
  $exists = false;
  foreach( $allPages as $page ){
    //通过页面别名来判断页面是否已经存在
    if( strtolower( $page->post_name ) == strtolower( $slug ) ){
      $exists = true;
    }
  }
  if( $exists == false ) {
    $new_page_id = wp_insert_post(
      array(
        'post_title' => $title,
        'post_type'     => 'page',
        'post_name'  => $slug,
        'comment_status' => 'closed',
        'ping_status' => 'closed',
        'post_content' => '',
        'post_status' => 'publish',
        'post_author' => 1,
        'menu_order' => 0
      ) 
    );   
    //如果插入成功 且设置了模板
    if($new_page_id && $page_template!=''){
      //保存页面模板信息
      update_post_meta($new_page_id, '_wp_page_template',  $page_template);
    }
  }   
}